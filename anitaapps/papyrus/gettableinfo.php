<?php

require_once('database.php');

$cookiename = "papyrus".$_SERVER['SERVER_ADDR'];
if ($_COOKIE[$cookiename] != ""){
    $userdata = json_decode($_COOKIE[$cookiename]);
    $myDB = new sqlDB($userdata[0], $userdata[1]);
    $res = $myDB->checkLogin();
    if (substr($res[0], 0, 7) != 'ERROR: ') {
        $dbname = $_GET['db'];
        if ($dbname != "") {
            $tbname = $_GET['table'];
            if ($tbname != "") {
                if ($myDB->tableExists($dbname,$tbname)) {
                    $res = $myDB->getStructure($dbname,$tbname);
                    print json_encode($res);
                }
            }
        }
    }
    
}

?>
