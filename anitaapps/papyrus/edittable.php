<?php

print '<h1>Modifica tabella</h1>';
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
            $tbname = $_GET['table'];
            if ($tbname != "") {
                
                if ($myDB->tableExists($dbname,$tbname)) {
                    foreach ($_POST as $key => $value) {
                        if (substr($key,0,12) == 'type_papyrus') {
                            if ($key != "type_papyrusnew"){
                                if ($_POST[substr($key,12)] == ""){
                                    $myDB->removeColumn($dbname, $tbname, substr($key,12));
                                } else {
                                    $myDB->updateColumn($dbname, $tbname, substr($key,12), $_POST[substr($key,12)], $value);
                                }
                            } else {
                                if ($_POST['papyrusnew']!='') {
                                    $myDB->addColumn($dbname, $tbname, $_POST['papyrusnew'], $_POST['type_papyrusnew']);
                                }
                            }
                        }
                    }
                } else {
                    if ($_POST['papyrusnew']!='') {
                        $columns  = $_POST['papyrusnew'].' '.$_POST['type_papyrusnew'];
                        $res = $myDB->createTable($dbname,$tbname,$columns);
                    }
                }
                
                print '<form action="edittable.php?db='.$dbname.'&table='.$tbname.'" method="post" >';
                if ($myDB->tableExists($dbname,$tbname)) {
                    $res = $myDB->getStructure($dbname,$tbname);
                    foreach ($res as $value) {
                        print 'Nome del campo: <input type="text" name="'.$value[0].'" value="'.$value[0].'"/> ' ;
                        print 'Tipo: <select name="type_papyrus'.$value[0].'">'.writeoptions($myDB->datatypes,$value[1]).'</select> <br /><br /> ' ;
                    }
                }
                print 'Aggiungi nuovo campo:';
                print 'Nome del campo: <input type="text" name="papyrusnew" value=""/> ' ;
                print 'Tipo: <select name="type_papyrusnew">'.writeoptions($myDB->datatypes,"text").'</select> <br /><br /> ' ;
                print '<input type="submit" value="Salva" /> <input type="reset" value="Annulla" /> </form>';
                
                
                
            }
            
            
        }
    }
    
} else {
    //a
    if($user=="" && $pass==""){
        print '<form action="login.php">Username: <input type="text" name="user" /><br /> Password: <input type="password" name="pass" /><br /><br /> <input type="submit" value="Login" /> <input type="reset" value="Annulla" /> </form>' ;
    }
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



?>
