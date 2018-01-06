<?php

print '<h1>Modifica database</h1>';
require_once('database.php');
$papyrus_forms = "papyrus_forms";

$cookiename = "papyrus".$_SERVER['SERVER_ADDR'];
if ($_COOKIE[$cookiename] != ""){
    print '<p><a href="index.php">Seleziona database</a> ';
    print '<a href="logout.php">Logout</a></p></br>';
    $userdata = json_decode($_COOKIE[$cookiename]);
    $myDB = new sqlDB($userdata[0], $userdata[1]);
    $res = $myDB->checkLogin();
    if (substr($res[0], 0, 7) == 'ERROR: ') {
        print_r($res);
    } else {
        
        $dbname = $_GET['db'];
        if ($dbname != "") {

                print "<h3>Maschere:</h3>";
                if ($myDB->tableExists($dbname,$papyrus_forms)) {
                    $res = $myDB->selectFrom($dbname,$papyrus_forms);
                    foreach ($res as $value) {
                        print '<a href="viewform.php?db='.$dbname.'&form='.$value["frmname"].'">'.$value["frmname"].'</a>';
                        print ' ';
                        print '<a href="editform.php?db='.$dbname.'&form='.$value["frmname"].'">Modifica</a>';
                        print ' ';
                        print '<a href="delete.php?db='.$dbname.'&form='.$value["frmname"].'">Elimina</a>';
                        print "</br>";
                    }
                }
                print "</br>";
                print '<form action="editform.php"><input name="db" type="hidden" value="'.$dbname.'">Crea una nuova maschera: <input type="text" name="form" /> <input type="submit" value="Crea" /> </form>' ;
                print "</br>";
                print "</br>";
                print "</br>";
                print "</br>";
                
                print "<h3>Tabelle:</h3>";
                $res = $myDB->listTables($dbname);
                foreach ($res as $value) {
                    if ($value != $papyrus_forms) {
                        print '<a href="edittable.php?db='.$dbname.'&table='.$value.'">'.$value.'</a>';
                        print ' ';
                        print '<a href="delete.php?db='.$dbname.'&table='.$value.'">Elimina</a>';
                        print "</br>";
                    }
                }
                print "</br>";
                print '<form action="edittable.php"><input name="db" type="hidden" value="'.$dbname.'">Crea una nuova tabella: <input type="text" name="table" /> <input type="submit" value="Crea" /> </form>' ;
                print "</br>";
                print "</br>";
                print "</br>";
                
            
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
