<?php
/**
 * set document type
 * @param string $type type of document
 */
function set_content_type($type = 'application/json') {
    header('Content-Type: '.$type);
}

/**
 * Read CSV from URL or File
 * @param  string $filename  Filename
 * @param  string $delimiter Delimiter
 * @return array            [description]
 */
function read_csv($filename, $delimiter = ",") {
    $file_data = array();
    $handle = @fopen($filename, "r") or false;
    if ($handle !== FALSE) {
        while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            $file_data[] = $data;
        }
        fclose($handle);
    }
    return $file_data;
}

/**
 * Print Log to the page
 * @param  mixed  $var    Mixed Input
 * @param  boolean $pre    Append <pre> tag
 * @param  boolean $return Return Output
 * @return string/void     Dependent on the $return input
 */
function plog($var, $pre=true, $return=false) {
    $info = print_r($var, true);
    $result = $pre ? "<pre>$info</pre>" : $info;
    if ($return) return $result;
    else echo $result;
}

/**
 * Log to file
 * @param  string $log Log
 * @return void
 */
function elog($log, $fn = "debug.log") {
    $fp = fopen($fn, "a");
    fputs($fp, "[".date("d-m-Y h:i:s")."][Log] $log\r\n");
    fclose($fp);
}

function conv_date_to_db ($data){
  //list ($d, $m, $y) = explode ("/", $data);
  $d=substr($data, 0, 2);
  $m=substr($data, 3, 2);
  $y=substr($data, 6, 4);
  $h=substr($data, 11, 2);
  $min=substr($data, 14, 2);
  $sec=substr($data, 17, 2);

  if(empty($h)){$h="00";}
  if(empty($min)){$min="00";}
  if(empty($sec)){$sec="00";}

  return "$y-$m-$d $h:$min:$sec";
}
function gas_mktime($data){
  // 01 / 01 / 2010  15: 00: 00
  // 01 2 34 5 67891 123 456 78
  // 20 0 0- 1 0-31
  $d=substr($data, 0, 2);
  $m=substr($data, 3, 2);
  $y=substr($data, 6, 4);
  $h=substr($data, 11, 2);
  $min=substr($data, 14, 2);
  $sec=substr($data, 17, 2);
  if(empty($h)){$h="00";}
  if(empty($min)){$min="00";}
  if(empty($sec)){$sec="00";}


  return  mktime($h, $min, $sec, $m, $d, $y);

}
?>