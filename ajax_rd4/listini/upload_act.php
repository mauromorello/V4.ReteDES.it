<?php
require_once("inc/init.php");
$converter = new Encryption;
$page_title = "Importa articoli";

$id_listino = CAST_TO_INT($_GET["id"]);
$file = CAST_TO_STRING($_GET["f"]);
$extension = CAST_TO_STRING($_GET["e"]);
$filename = "LIS_".$file."_RD4.".$extension;
if($_GET["s"]=="e"){
    $show_all = false;
}else{
    $show_all = true;
}
if($_GET["do"]=="ins"){
    $insert = true;
}else{
    $insert = false;
}
if(!file_exists("../../public_rd4/listini/".$filename)){echo "file missing...";die();}

if($_GET["act"]=="check"){
            //arico excel.php
             require_once('../../lib_rd4/PHPExcel.php');

             /** Load $inputFileName to a PHPExcel Object  **/


             $objPHPExcel = PHPExcel_IOFactory::load("../../public_rd4/listini/".$filename);
             $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator(2);
             $worksheet = $objPHPExcel->getActiveSheet();

             $riga=0;
             $codice_ko = 0;
             $prezzo_zero = 0;
             $misura_zero = 0;
             $qta_scatola_zero = 0;
             $qta_minima_zero = 0;
             $qta_minima_sup = 0;
             $multiplo_errato = 0;

             $t.='  <table class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Stato</th>
                                <th>Cod</th>
                                <th>Desc</th>
                                <th>Prz</th>
                                <th>U.M</th>
                                <th>M</th>
                                <th>N.Br</th>
                                <th>QS</th>
                                <th>Qm</th>
                                <th>Note</th>
                                <th>U</th>
                                <th>O1</th>
                                <th>O2</th>
                                <th>O3</th>
                            </tr>
                        </thead>
                        <tbody>
                    ';

             $lastRow = $worksheet->getHighestRow();
             for ($row = 2; $row <= $lastRow; $row++) {

                    $riga++;
                    //$t.='<tr>';
                    //CODICE
                    $cell = $worksheet->getCell('A'.$row);
                    $value= $cell->getValue();
                    $codice=html_entity_decode(CAST_TO_STRING($value));
                    if($codice==""){
                        $codice_vuoto++;
                        $errore_riga++;
                        $codice=" ? ";
                    }else{
                        //CONTROLLO CODICE NON UNIVOCO
                        $sql = "SELECT count(*) as c FROM  retegas_articoli WHERE id_listini = :id_listini AND codice = :codice";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam(':id_listini', $id_listino, PDO::PARAM_INT);
                        $stmt->bindParam(':codice', $codice, PDO::PARAM_STR);

                        $stmt->execute();
                        $rowC = $stmt->fetch(PDO::FETCH_ASSOC);
                        if($rowC["c"]>0){
                            $codice_ko++;
                            $errore_riga++;
                            $codice_err_class= 'text-danger bold';
                        }else{
                            $codice_err_class= '';
                        }
                    }
                    $r.='<td class="'.$codice_err_class.'">'.$codice.'</td>';

                    //DESCRIZIONE
                    $cell = $worksheet->getCell('B'.$row);
                    $value= $cell->getValue();
                    $descrizione_articoli =  html_entity_decode(CAST_TO_STRING($value));
                    if($descrizione_articoli==""){
                        $descrizione_vuota++;
                        $errore_riga++;
                        $descrizione_articoli=" ? ";
                    }
                    $r.='<td>'.$descrizione_articoli.'</td>';

                    //PREZZO
                    $cell = $worksheet->getCell('C'.$row);
                    $value= $cell->getValue();
                    if(strstr($value, ",")) {
                        $value = str_replace(".", "", $value); // replace dots (thousand seps) with blancs
                        $value = str_replace(",", ".", $value); // replace ',' with '.'
                    }
                    $prezzo=CAST_TO_FLOAT($value);
                    if($prezzo<=0){
                        $prezzo_zero++;
                        $errore_riga++;
                        $prezzo_err='text-danger bold';
                    }else{
                        $prezzo_err='';
                    }
                    $r.='<td class="'.$prezzo_err.'">'.$prezzo.'</td>';

                    //U MIS
                    $cell = $worksheet->getCell('D'.$row);
                    $value= $cell->getValue();
                    $u_misura = html_entity_decode(clean(CAST_TO_STRING($value)));
                    if($u_misura==""){
                        $u_misura_vuota++;
                        $errore_riga++;
                        $u_misura=" ? ";
                    }
                    $r.='<td>'.$u_misura.'</td>';

                    //MISURA
                    $cell = $worksheet->getCell('E'.$row);
                    $value= $cell->getValue();
                    if(strstr($value, ",")) {
                        $value = str_replace(".", "", $value); // replace dots (thousand seps) with blancs
                        $value = str_replace(",", ".", $value); // replace ',' with '.'
                    }
                    $misura=CAST_TO_FLOAT($value);
                    if($misura<=0){
                        $misura_zero++;
                        $errore_riga++;
                        $misura_err='text-danger bold';
                    }
                    $r.='<td class="'.$misura_err.'">'.$misura.'</td>';

                    //NOTE BREVI
                    $cell = $worksheet->getCell('F'.$row);
                    $value= $cell->getValue();
                    $ingombro = html_entity_decode((CAST_TO_STRING($value)));
                    $r.='<td><small>'.$ingombro.'</small></td>';

                    // Q SCAT
                    $cell = $worksheet->getCell('G'.$row);
                    $value= $cell->getValue();
                    if(strstr($value, ",")) {
                        $value = str_replace(".", "", $value); // replace dots (thousand seps) with blancs
                        $value = str_replace(",", ".", $value); // replace ',' with '.'
                    }
                    $qta_scatola=round(CAST_TO_FLOAT($value,0),4);
                    if($qta_scatola<=0){
                        $qta_scatola_zero++;
                        $errore_riga++;
                        $qta_scatola_err='text-danger bold';
                    }
                    $r.='<td class="'.$qta_scatola_err.'">'.$qta_scatola.'</td>';

                    // Q MIN
                    $cell = $worksheet->getCell('H'.$row);
                    $value= $cell->getValue();
                    if(strstr($value, ",")) {
                        $value = str_replace(".", "", $value); // replace dots (thousand seps) with blancs
                        $value = str_replace(",", ".", $value); // replace ',' with '.'
                    }
                    $qta_minima=round(CAST_TO_FLOAT($value,0),4);
                    if($qta_minima<=0){
                        $qta_minima_zero++;
                        $errore_riga++;
                        $qta_minima_err='text-danger bold';
                    }else{
                        if($qta_minima>$qta_scatola){
                            $qta_minima_sup++;
                            $errore_riga++;
                            $qta_minima_err='text-danger bold';
                        }else{
                            $big = $qta_scatola;
                            while ($big > 0) {
                                $big=round($big-$qta_minima,4);
                            }
                            if($big<>0){
                                $log .=" big = $big ";
                                $multiplo_errato++;
                                $errore_riga++;
                                $qta_minima_err='text-danger bold';
                            }
                        }
                    }
                    $r.='<td class="'.$qta_minima_err.'">'.$qta_minima.'</td>';

                    //NOTE
                    $cell = $worksheet->getCell('I'.$row);
                    $value= $cell->getValue();
                    $articoli_note = html_entity_decode((CAST_TO_STRING($value)));
                    $r.='<td>'.$articoli_note.'</td>';

                    //UNIVOCO
                    $cell = $worksheet->getCell('J'.$row);
                    $value= $cell->getValue();
                    $articoli_unico = CAST_TO_INT($value,0,1);
                    $r.='<td>'.$articoli_unico.'</td>';

                    //OPZ1
                    $cell = $worksheet->getCell('K'.$row);
                    $value= $cell->getValue();
                    $articoli_opz_1 = html_entity_decode((CAST_TO_STRING($value)));
                    $r.='<td>'.$articoli_opz_1.'</td>';

                    //OPZ2
                    $cell = $worksheet->getCell('L'.$row);
                    $value= $cell->getValue();
                    $articoli_opz_2 = html_entity_decode((CAST_TO_STRING($value)));
                    $r.='<td>'.$articoli_opz_2.'</td>';

                    //OPZ3
                    $cell = $worksheet->getCell('M'.$row);
                    $value= $cell->getValue();
                    $articoli_opz_3 = html_entity_decode((CAST_TO_STRING($value)));
                    $r.='<td>'.$articoli_opz_3.'</td>';

                    if($errore_riga>0){
                        $riga_sbagliata++;
                        $t.='<tr><td><i class="fa fa-times text-danger"></i></td>'.$r.'</tr>
                        ';
                    }else{
                        if($show_all){
                            $t.='<tr><td><i class="fa fa-check text-success"></i></td>'.$r.'</tr>
                            ';
                        }
                    if($insert){
                        $sql ="INSERT INTO retegas_articoli  (              id_listini,
                                                                            codice,
                                                                            descrizione_articoli,
                                                                            prezzo,
                                                                            u_misura,
                                                                            misura,
                                                                            qta_scatola,
                                                                            qta_minima,
                                                                            articoli_unico,
                                                                            ingombro,
                                                                            articoli_opz_1,
                                                                            articoli_opz_2,
                                                                            articoli_opz_3,
                                                                            articoli_note
                                                                            )
                                                                            VALUES (
                                                                            :id_listini,
                                                                            :codice,
                                                                            :descrizione_articoli,
                                                                            :prezzo,
                                                                            :u_misura,
                                                                            :misura,
                                                                            :qta_scatola,
                                                                            :qta_minima,
                                                                            :articoli_unico,
                                                                            :ingombro,
                                                                            :articoli_opz_1,
                                                                            :articoli_opz_2,
                                                                            :articoli_opz_3,
                                                                            :articoli_note
                                                                            );";


                        $stmt = $db->prepare($sql);

                        $stmt->bindParam(':id_listini', $id_listino, PDO::PARAM_INT);
                        $stmt->bindParam(':codice', $codice, PDO::PARAM_STR);
                        $stmt->bindParam(':descrizione_articoli', $descrizione_articoli, PDO::PARAM_STR);

                        $stmt->bindParam(':prezzo', $prezzo, PDO::PARAM_STR);
                        $stmt->bindParam(':u_misura', $u_misura, PDO::PARAM_STR);
                        $stmt->bindParam(':misura', $misura, PDO::PARAM_STR);
                        $stmt->bindParam(':qta_scatola', $qta_scatola, PDO::PARAM_STR);
                        $stmt->bindParam(':qta_minima', $qta_minima, PDO::PARAM_STR);

                        $stmt->bindParam(':articoli_unico', $articoli_unico, PDO::PARAM_INT);
                        $stmt->bindParam(':ingombro', $ingombro, PDO::PARAM_STR);

                        $stmt->bindParam(':articoli_opz_1', $articoli_opz_1, PDO::PARAM_STR);
                        $stmt->bindParam(':articoli_opz_2', $articoli_opz_2, PDO::PARAM_STR);
                        $stmt->bindParam(':articoli_opz_3', $articoli_opz_3, PDO::PARAM_STR);

                        $stmt->bindParam(':articoli_note', $articoli_note, PDO::PARAM_STR);

                        $stmt->execute();
                        if($stmt->rowCount()==1){
                            $inserted++;
                            }
                        }
                    }

                    $errore_riga=0;
                    $r='';
                    //$t.='</tr>';

             }

             if($riga_sbagliata>0){
                $panel='<div class="well">
                    <a class="btn btn-default pull-left" href="#ajax_rd4/listini/listino.php?id='.$id_listino.'">Torna al listino</a>
                    <a class="btn btn-default pull-right" href="#ajax_rd4/listini/upload_act.php?id='.$id_listino.'&f='.$file.'&e='.$extension.'&act=check&s=e">Mostra solo errori</a>
                    <div class="clearfix"></div>
                    </div>
                    ';
             }else{
               $panel='<div class="jumbotron text-center"><button id="do_upload" class="btn btn-xl btn-success"><h1><i class="fa fa-rocket"></i>   Carica!</h1></button></div>';
             }

             $t.='  </tbody>
                </table>';



             $objPHPExcel->disconnectWorksheets();
             unset($objPHPExcel);



}
if($insert){
    if($inserted==$riga){
        $res=array("result"=>"OK", "msg"=>"$inserted su $riga articoli caricati " );
    }else{
        $res=array("result"=>"KO", "msg"=>"$inserted su $riga articoli caricati " );
    }

    unlink("../../public_rd4/listini/".$filename);

    echo json_encode($res);
    die();
}


?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-upload"></i> Importa articoli  &nbsp;</h1>
</div>
<div class="margin-top-10">
    <?php echo $panel; ?>
</div>
<hr>
<div class="margin-top-10 table-responsive">
    <?php echo $t; ?>
</div>


<script type="text/javascript">

    pageSetUp();



    var pagefunction = function(){
        $('#do_upload').click(function(){
            console.log("do_upload");
            $.ajax({
                          type: "GET",
                          url: "ajax_rd4/listini/upload_act.php?id=<?php echo $id_listino?>&f=<?php echo $file?>&e=<?php echo $extension; ?>&act=check&do=ins",
                          dataType: 'json',
                          //data: {act: "del_articoli", id : <?php echo $id_listino?>},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                ok(data.msg);
                                //$('#jqgrid').trigger( 'reloadGrid' );
                                loadURL("ajax_rd4/listini/listino.php?id=<?php echo $id_listino?>",$('#content'));
                            }else{
                                ko(data.msg);
                                loadURL("ajax_rd4/listini/listino.php?id=<?php echo $id_listino?>",$('#content'));
                            ;}

                        });
        })

    } // end pagefunction



    pagefunction();
</script>
