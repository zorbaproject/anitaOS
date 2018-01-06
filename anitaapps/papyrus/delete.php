<?php

print '<h1>Elimina database</h1>';
require_once('database.php');
$papyrus_forms = "papyrus_forms";

$cookiename = "papyrus".$_SERVER['SERVER_ADDR'];
if ($_COOKIE[$cookiename] != ""){
    print '<p><a href="logout.php">Logout</a></p><br>';
    $userdata = json_decode($_COOKIE[$cookiename]);
    $myDB = new sqlDB($userdata[0], $userdata[1]);
    $res = $myDB->checkLogin();
    if (substr($res[0], 0, 7) == 'ERROR: ') {
        print_r($res);
    } else {
        $dbname = $_GET['db'];
        $frmname = $_GET['form'];
        $tbname = $_GET['table'];
        if ($dbname != "") {
            if($tbname!=""){
                $pdo = $myDB->deleteTable($dbname,$tbname);
                print_r($pdo);
                print '<a href="edit.php?db='.$dbname.'">Index</a><META http-equiv="REFRESH" content="0; url=edit.php?db='.$dbname.'">';
            }
            
            if($frmname!=""){
                $where = 'WHERE frmname = ?';
                $params = [$frmname];
                $res = $myDB->deleteFrom($dbname,$papyrus_forms,$where,$params);
                print '<a href="edit.php?db='.$dbname.'">Index</a><META http-equiv="REFRESH" content="0; url=edit.php?db='.$dbname.'">';
            }
            
            if($tbname == "" && $frmname == ""){
                $pdo = $myDB->deleteDB($dbname);
                print_r($pdo);
                print '<a href="index.php">Index</a><META http-equiv="REFRESH" content="0; url=index.php">';
            }
            
        }/* else {
        print '<form action="create.php">Database da cancellare: <input type="text" name="db" /><br /><br /> <input type="submit" value="Login" /> <input type="reset" value="Annulla" /> </form>' ;
    }*/
    
    }
    
}
else {
    //a
    if($user=="" && $pass==""){
        print '<form action="login.php">Username: <input type="text" name="user" /><br /> Password: <input type="password" name="pass" /><br /><br /> <input type="submit" value="Login" /> <input type="reset" value="Annulla" /> </form>' ;
    }
}
?>
