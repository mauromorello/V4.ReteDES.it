<?php
  class Encryption {
    var $skey = __ENCRYPTIONKEY;

    public  function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    public function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public  function encode($value){
        if(!$value){return false;}
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext));
    }

    public function decode($value){
        if(!$value){return false;}
        $crypttext = $this->safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }
}
/*
Sample Call:

$str = "myPassword";

$converter = new Encryption;
$encoded = $converter->encode($str);
$decoded = $converter->decode($encode);

echo "$encoded<p>$decoded";
*/


// php-export-data by Eli Dickinson, http://github.com/elidickinson/php-export-data

/**
 * ExportData is the base class for exporters to specific file formats. See other
 * classes below.
 */
abstract class ExportData {
    protected $exportTo; // Set in constructor to one of 'browser', 'file', 'string'
    protected $stringData; // stringData so far, used if export string mode
    protected $tempFile; // handle to temp file (for export file mode)
    protected $tempFilename; // temp file name and path (for export file mode)

    public $filename; // file mode: the output file name; browser mode: file name for download; string mode: not used

    public function __construct($exportTo = "browser", $filename = "exportdata") {
        if(!in_array($exportTo, array('browser','file','string') )) {
            throw new Exception("$exportTo is not a valid ExportData export type");
        }
        $this->exportTo = $exportTo;
        $this->filename = $filename;
    }

    public function initialize() {

        switch($this->exportTo) {
            case 'browser':
                $this->sendHttpHeaders();
                break;
            case 'string':
                $this->stringData = '';
                break;
            case 'file':
                $this->tempFilename = tempnam(sys_get_temp_dir(), 'exportdata');
                $this->tempFile = fopen($this->tempFilename, "w");
                break;
        }

        $this->write($this->generateHeader());
    }

    public function addRow($row) {
        $this->write($this->generateRow($row));
    }

    public function finalize() {

        $this->write($this->generateFooter());

        switch($this->exportTo) {
            case 'browser':
                flush();
                break;
            case 'string':
                // do nothing
                break;
            case 'file':
                // close temp file and move it to correct location
                fclose($this->tempFile);
                rename($this->tempFilename, $this->filename);
                break;
        }
    }

    public function getString() {
        return $this->stringData;
    }

    abstract public function sendHttpHeaders();

    protected function write($data) {
        switch($this->exportTo) {
            case 'browser':
                echo $data;
                break;
            case 'string':
                $this->stringData .= $data;
                break;
            case 'file':
                fwrite($this->tempFile, $data);
                break;
        }
    }

    protected function generateHeader() {
        // can be overridden by subclass to return any data that goes at the top of the exported file
    }

    protected function generateFooter() {
        // can be overridden by subclass to return any data that goes at the bottom of the exported file
    }

    // In subclasses generateRow will take $row array and return string of it formatted for export type
    abstract protected function generateRow($row);

}

/**
 * ExportDataTSV - Exports to TSV (tab separated value) format.
 */
class ExportDataTSV extends ExportData {

    function generateRow($row) {
        foreach ($row as $key => $value) {
            // Escape inner quotes and wrap all contents in new quotes.
            // Note that we are using \" to escape double quote not ""
            $row[$key] = '"'. str_replace('"', '\"', $value) .'"';
        }
        return implode("\t", $row) . "\n";
    }

    function sendHttpHeaders() {
        header("Content-type: text/tab-separated-values");
    header("Content-Disposition: attachment; filename=".basename($this->filename));
    }
}

/**
 * ExportDataCSV - Exports to CSV (comma separated value) format.
 */
class ExportDataCSV extends ExportData {

    function generateRow($row) {
        foreach ($row as $key => $value) {
            // Escape inner quotes and wrap all contents in new quotes.
            // Note that we are using \" to escape double quote not ""
            $row[$key] = '"'. str_replace('"', '\"', $value) .'"';
        }
        return implode(",", $row) . "\n";
    }

    function sendHttpHeaders() {
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=".basename($this->filename));
    }
}


/**
 * ExportDataExcel exports data into an XML format  (spreadsheetML) that can be
 * read by MS Excel 2003 and newer as well as OpenOffice
 *
 * Creates a workbook with a single worksheet (title specified by
 * $title).
 *
 * Note that using .XML is the "correct" file extension for these files, but it
 * generally isn't associated with Excel. Using .XLS is tempting, but Excel 2007 will
 * throw a scary warning that the extension doesn't match the file type.
 *
 * Based on Excel XML code from Excel_XML (http://github.com/oliverschwarz/php-excel)
 *  by Oliver Schwarz
 */
class ExportDataExcel extends ExportData {

    const XmlHeader = "<?xml version=\"1.0\" encoding=\"%s\"?\>\n<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:html=\"http://www.w3.org/TR/REC-html40\">";
    const XmlFooter = "</Workbook>";

    public $encoding = 'UTF-8'; // encoding type to specify in file.
    // Note that you're on your own for making sure your data is actually encoded to this encoding

    public $title = 'Sheet1'; // title for Worksheet

    function generateHeader() {

        // workbook header
        $output = stripslashes(sprintf(self::XmlHeader, $this->encoding)) . "\n";

        // Set up styles
        $output .= "<Styles>\n";
        $output .= "<Style ss:ID=\"sDT\"><NumberFormat ss:Format=\"Short Date\"/></Style>\n";
        $output .= "</Styles>\n";

        // worksheet header
        $output .= sprintf("<Worksheet ss:Name=\"%s\">\n    <Table>\n", htmlentities($this->title));

        return $output;
    }

    function generateFooter() {
        $output = '';

        // worksheet footer
        $output .= "    </Table>\n</Worksheet>\n";

        // workbook footer
        $output .= self::XmlFooter;

        return $output;
    }

    function generateRow($row) {
        $output = '';
        $output .= "        <Row>\n";
        foreach ($row as $k => $v) {
            $output .= $this->generateCell($v);
        }
        $output .= "        </Row>\n";
        return $output;
    }

    private function generateCell($item) {
        $output = '';
        $style = '';

        // Tell Excel to treat as a number. Note that Excel only stores roughly 15 digits, so keep
        // as text if number is longer than that.
        if(preg_match("/^-?\d+(?:[.,]\d+)?$/",$item) && (strlen($item) < 15)) {
            $type = 'Number';
        }
        // Sniff for valid dates; should look something like 2010-07-14 or 7/14/2010 etc. Can
        // also have an optional time after the date.
        //
        // Note we want to be very strict in what we consider a date. There is the possibility
        // of really screwing up the data if we try to reformat a string that was not actually
        // intended to represent a date.
        elseif(preg_match("/^(\d{1,2}|\d{4})[\/\-]\d{1,2}[\/\-](\d{1,2}|\d{4})([^\d].+)?$/",$item) &&
                    ($timestamp = strtotime($item)) &&
                    ($timestamp > 0) &&
                    ($timestamp < strtotime('+500 years'))) {
            $type = 'DateTime';
            $item = strftime("%Y-%m-%dT%H:%M:%S",$timestamp);
            $style = 'sDT'; // defined in header; tells excel to format date for display
        }
        else {
            $type = 'String';
        }

        $item = str_replace('&#039;', '&apos;', htmlspecialchars($item, ENT_QUOTES));
        $output .= "            ";
        $output .= $style ? "<Cell ss:StyleID=\"$style\">" : "<Cell>";
        $output .= sprintf("<Data ss:Type=\"%s\">%s</Data>", $type, $item);
        $output .= "</Cell>\n";

        return $output;
    }

    function sendHttpHeaders() {
        header("Content-Type: application/vnd.ms-excel; charset=" . $this->encoding);
        header("Content-Disposition: inline; filename=\"" . basename($this->filename) . "\"");
    }

}
/*
<?php

// When executed in a browser, this script will prompt for download
// of 'test.xls' which can then be opened by Excel or OpenOffice.

require 'php-export-data.class.php';

// 'browser' tells the library to stream the data directly to the browser.
// other options are 'file' or 'string'
// 'test.xls' is the filename that the browser will use when attempting to
// save the download
$exporter = new ExportDataExcel('browser', 'test.xls');

$exporter->initialize(); // starts streaming data to web browser

// pass addRow() an array and it converts it to Excel XML format and sends
// it to the browser
$exporter->addRow(array("This", "is", "a", "test"));
$exporter->addRow(array(1, 2, 3, "123-456-7890"));

// doesn't care how many columns you give it
$exporter->addRow(array("foo"));

$exporter->finalize(); // writes the footer, flushes remaining data to browser.

exit(); // all done

?>

*/