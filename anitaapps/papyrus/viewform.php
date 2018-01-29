<?php

//print '<h1>Modifica maschera</h1>';
require_once('database.php');
$papyrus_forms = "papyrus_forms";

$dbname = $_GET['db'];
$cookiename = "papyrus".$_SERVER['SERVER_ADDR'];
if ($_COOKIE[$cookiename] != ""){
    print '<p><a href="edit.php?db='.$dbname.'">Torna al database</a> ';
    print '<a href="logout.php">Logout</a></p></br>';
    $userdata = json_decode($_COOKIE[$cookiename]);
    $myDB = new sqlDB($userdata[0], $userdata[1]);
    $res = $myDB->checkLogin();
    if (substr($res[0], 0, 7) == 'ERROR: ') {
        print_r($res);
    } else {
        if ($dbname != "") {
            if (!$myDB->tableExists($dbname,$papyrus_forms)) {
                $columns= "frmname text, content text";
                $myDB->createTable($dbname,$papyrus_forms,$columns);
            }
            
            $frmname = $_GET['form'];
            $mycommand = $_POST['command'];
            $tbkey = "";
            if ($frmname != "") {
                
                $jsscript = file_get_contents("draggable.js");
                print '<script>'.$jsscript.'</script>';
                
                if ($myDB->tableExists($dbname,$papyrus_forms)) {
                    
                    /*if ($_POST['frmname'] != "") {
                        $frmname = $_POST['frmname'];
                        $allelements = array();
                        $i = 0;
                        foreach ($_POST as $key => $value) {
                            if (substr($key, -5) == "check"){
                                $elemPrefix = substr($key, 0, -5);
                                if ($_POST[$elemPrefix.'tablefield'] != "" && $_POST[$elemPrefix.'table'] != ""){
                                    $elemname = strval($i)."_".$_POST[$elemPrefix.'tablefield']."@".$_POST[$elemPrefix.'table'];
                                    $allelements[$elemname] = array("table" => $_POST[$elemPrefix.'table'], "tablefield" => $_POST[$elemPrefix.'tablefield'], "left" => $_POST[$elemPrefix.'left'], "top" => $_POST[$elemPrefix.'top']);
                                    //$allelements[$elemname] = array("table" => $_POST[$elemPrefix.'table'], "tablefield" => $_POST[$elemPrefix.'tablefield'], "left" => $_POST[$elemPrefix.'left'], "top" => $_POST[$elemPrefix.'top'], "key" => $_POST[$elemPrefix.'key']);
                                    $i = $i +1;
                                }
                            }
                        }
                        $allelements["keys"] = array("tbkey" => $_POST['tbkey']);
                        
                        //pulizia vecchi record
                        $where = 'WHERE frmname = ?';
                        $params = [$frmname];
                        $res = $myDB->deleteFrom($dbname,$papyrus_forms,$where,$params);
                        //inserimento nuovo record
                        $frmcontent = json_encode($allelements);
                        $params = array("frmname" => $frmname, "content" => $frmcontent);
                        $res = $myDB->insert($dbname,$papyrus_forms, $params);
                        
                    }*/
                    
                    $where = 'WHERE frmname = ?';
                    $params = [$frmname];
                    $res = $myDB->selectFrom($dbname,$papyrus_forms,$where,$params);
                    if (count($res)>0) {
                        $allelements = json_decode($res[0]["content"], true);
                        $tbkey = $allelements["keys"]["tbkey"];
                    }
                    $tbkeyvalue = $_POST['tbkeyvalue'];
                    $searchresults = array();
                    if ($mycommand == "search") {
                        foreach ($_POST as $key => $value) {
                        if (strpos($key, "@") > 0 && $value != "") {    
                            $atpos = strpos($key, "@")+1;
                            $tmptable = substr($key, $atpos); //dopo @
                            $undrscrpos = strpos($key, "_")+1;
                            $tmpfield = substr($key, $undrscrpos, $atpos-$undrscrpos-1); // tra _ e @
                            $where = "";
                            if ($_POST['useregex'] == "true") {
                                $where = 'WHERE `'.$tmpfield.'` REGEXP ?';
                            } else {
                                $where = 'WHERE `'.$tmpfield.'` LIKE ?';
                                $value = str_replace("*","%",$value); //gli utenti sono abituati a caratteri jolly tipo * e ?
                                $value = str_replace("?","_",$value);
                            }
                            $params = [$value];
                            $res = $myDB->selectFrom($dbname,$tmptable,$where,$params);
                            $tbkeyvalue = '';
                            if (count($res)>0) {
                                $tbkeyvalue = $res[0][$tbkey];
                            }
                        }
                        }
                        $where = 'WHERE frmname = ?';
                        $params = [$frmname];
                        $res = $myDB->selectFrom($dbname,$papyrus_forms,$where,$params);
                        if (count($res)>0) {
                            $allelements = json_decode($res[0]["content"], true);
                            foreach ($allelements as $key => $value) {
                                if ($key != "keys") {
                                    $tmptable = $value["table"];
                                    $tmpfield = $value["tablefield"];
                                    $where = 'WHERE `'.$tbkey.'` = ?';
                                    $params = [$tbkeyvalue];
                                    $resu = $myDB->selectFrom($dbname,$tmptable,$where,$params);
                                    if (count($resu)>0) {
                                        $isr = 0; //TODO: in futuro permetteremo di avere più risulati, non solo il primo della lista
                                        $searchresults[$tmpfield."@".$tmptable][$isr] = $resu[$isr][$tmpfield]; 
                                    }
                                }
                            }
                        }
                    }
                    
                    
                    $where = 'WHERE frmname = ?';
                    $params = [$frmname];
                    $res = $myDB->selectFrom($dbname,$papyrus_forms,$where,$params);
                    print "<script>document.title = '".$frmname."';</script>";
                    print '<form action="viewform.php?db='.$dbname.'&form='.$frmname.'" method="post" >';
                    print 'Cosa vuoi fare?:<input type="radio" name="command" value="search" checked="checked"/> Cerca <input type="radio" name="command" value="insert"/> Inserisci <input type="radio" name="command" value="replace"/> Sostituisci <input type="radio" name="command" value="delete"/> Elimina ';
                    $checked = "";
                    if ($_POST['useregex'] == "true") $checked = "checked";
                    print '<i>Usare RegEx</i>: <input type="checkbox" name="useregex" value="true" '.$checked.'> ';
                    if (count($res)>0) {
                        $allelements = json_decode($res[0]["content"], true);
                        $tbkey = $allelements["tbkey"];
                        foreach ($allelements as $key => $value) {
                            if ($key == "keys") {
                                $tbkey = $value["tbkey"];
                                $box = $box.'<b>'.$value["tablefield"].'</b><input type="hidden" name="tbkeyvalue" value="'.$tbkeyvalue.'" />';
                                print $box;
                            } else {
                                //print drawBox($key,$key,"checked", $value["table"], $value["tablefield"], $value["top"], $value["left"]);
                                $box = "";
                                $top = $value["top"];
                                $left = $value["left"];
                                $box = '<div id="'.$key.'div" style="top:'.$top.';left:'.$left.';position:absolute;border: 1px solid #d3d3d3;">';
                                $tmptable = $value["table"];
                                $tmpfield = $value["tablefield"];
                                $box = $box.'<b>'.$tmpfield.'</b><input type="text" name="'.$key.'" value="'.$searchresults[$tmpfield."@".$tmptable][0].'" />';
                                $box = $box.'</div>';
                                print $box;
                            }
                        }
                        //if ($tbkey == "") print 'Indica un campo chiave in tutte le tabelle:<input type="text" name="tbkey" value="'.$tbkey.'" />  '; 
                    }
                    
                    
                    print '<input type="submit" value="Esegui" /> <input type="reset" value="Annulla" /> </form>';
                }
                
                
            }
            
            
        }
    }
    
} else {
    //a
    if($user=="" && $pass==""){
        print '<form action="login.php">Username: <input type="text" name="user" /><br /> Password: <input type="password" name="pass" /><br /><br /> <input type="submit" value="Login" /> <input type="reset" value="Annulla" /> </form>' ;
    }
}

function drawBox($elemPrefix, $titlestr = "", $checked = "", $table = "", $tablefield = "", $top = "", $left = "") {
    if ($top == "") $top = "200";
    if ($left == "") $left = "0";
    if ($titlestr == "") $titlestr = $elemPrefix;
    
    $myDB = $GLOBALS['myDB'];
    $dbname = $GLOBALS['dbname'];
    $papyrus_forms = $GLOBALS['papyrus_forms'];
    $alltables = $myDB->listTables($dbname);
    array_unshift($alltables, "");
    $index = array_search($papyrus_forms,$alltables);
    if($index !== FALSE)   unset($alltables[$index]);
    
    $allfields = array();
    if ($table != ""){
        $res = $myDB->getStructure($dbname,$table);
        foreach ($res as $alval) {
            $allfields[] = $alval[0];
        }
    }
    $onchange = "onchange=\"getfields(this,'".$dbname."')\"";
    
    $result = '<div id="'.$elemPrefix.'" style="top:'.$top.';left:'.$left.';width:120px;height:120px;position:absolute;background-color:#f1f1f1;border: 1px solid #d3d3d3;">'; 
    $result = $result.'<div id="'.$elemPrefix.'header" style="cursor: move;background-color: #2196F3;color: #fff;">'.$titlestr.'</div>'; 
    $result = $result.' Attivo: <input type="checkbox" name="'.$elemPrefix.'check" value="attivo" '.$checked.'> '; 
    $result = $result.'<br /> Tabella: <select name = "'.$elemPrefix.'table" '.$onchange.' >'.writeoptions($alltables, $table).'</select> '; 
    $result = $result.'<br /> Campo: <select name = "'.$elemPrefix.'tablefield" >'.writeoptions($allfields, $tablefield).'</select> '; 
    $result = $result.'<br /> X: <input type="text" name="'.$elemPrefix.'left" value="'.$left.'" size="3" /> '; 
    $result = $result.'Y: <input type="text" name="'.$elemPrefix.'top" value="'.$top.'" size="3" /><br />'; 
    $result = $result.'</div>';
    $result = $result.'<script>dragElement(document.getElementById(("'.$elemPrefix.'")));</script>';
    return $result;
}

function writeoptions($values, $selected = ""){
    $text = "";
    foreach ($values as $value) {
        $text = $text.'<option value="'.$value.'" ';
        if ($value == $selected) $text = $text.'selected';
        $text = $text. '>'.$value.'</option>';
    }
    return $text;
}



/*
 * $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND status=?');
 * $stmt->execute([$email, $status]);
 * $user = $stmt->fetch();
 */

//il cookie appartiene davvero all'utente
/*
 * print '<form method="post" id="mioform" action="upload.php" enctype="multipart/form-data">';
 * print '<input type="hidden" name="action" value="upload" />';
 * print 'Puoi caricare file grandi al massimo '.ini_get('upload_max_filesize').'B. </br>';
 * print 'Carica il tuo file: ';
 * print '<input type="file" name="user_file" />';
 * print '<br />';
 * print '</form>';
 * print '<a href="javascript:upload()"><img width="36" src="upload.png"/>Upload</a>';
 * 
 * print '<div id="aggiornamento" style="visibility:hidden">';
 * print 'Upload in corso...<br/>';
 * print '<img src="file-up.gif"/>';
 * print '</div>';
 * 
 * print '<script type="text/javascript">';
 * print 'function upload()';
 * print '{';
 * print 'var mioform = document.getElementById("mioform");';
 * print 'document.getElementById("aggiornamento").setAttribute("style", "visibility:visible"); ';
 * print 'mioform.submit();';
 * print '}';
 * print '</script>';
 * 
 * //qui devo mettere l'elenco dei file dell'utente.
 * 
 * $myDirectory = opendir("./uploads/".$utente[0]);
 * while($entryName = readdir($myDirectory)) {
 *        $dirArray[] = $entryName;
 * }
 * closedir($myDirectory);
 * 
 * $indexCount = count($dirArray);
 * print ("Hai caricato un totale di ".$indexCount." files<br>\n");
 * 
 * print("<TABLE border=1 cellpadding=5 cellspacing=0 class=whitelinks>\n");
 * print("<TR><TH>Filename</TH></TR>\n");
 * for($index=0; $index < $indexCount; $index++) {
 * if (substr("$dirArray[$index]", 0, 1) != "."){ 
 * print("<TR><TD><a href=\"$dirArray[$index]\">$dirArray[$index]</a></td>");
 * print("</TR>\n");
 * }
 * }
 * print("</TABLE>\n");
 * 
 */


?>
