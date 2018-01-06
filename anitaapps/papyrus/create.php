<?php

print '<h1>Crea un nuovo database</h1>';
require_once('database.php');

$cookiename = "papyrus".$_SERVER['SERVER_ADDR'];
if ($_COOKIE[$cookiename] != ""){
    print '<p><a href="logout.php">Logout</a></p><br>';
    $userdata = json_decode($_COOKIE[$cookiename]);
    $myDB = new sqlDB($userdata[0], $userdata[1]);
    $res = $myDB->checkLogin();
    if (substr($res[0], 0, 7) == 'ERROR: ') {
        print_r($res);
    } else {
        //print_r($res);
        if ($_GET['db'] != "") {
            //print $_GET['db'];
            $pdo = $myDB->createDB($_GET['db']);
            print_r($pdo);
            print '<a href="index.php">Index</a><META http-equiv="REFRESH" content="0; url=index.php">';
            
        } else {
            print '<form action="create.php">Nome del nuovo database: <input type="text" name="db" /><br /><br /> <input type="submit" value="Login" /> <input type="reset" value="Annulla" /> </form>' ;
        }
        
    }
    
}
else {
    //a
    if($user=="" && $pass==""){
        print '<form action="login.php">Username: <input type="text" name="user" /><br /> Password: <input type="password" name="pass" /><br /><br /> <input type="submit" value="Login" /> <input type="reset" value="Annulla" /> </form>' ;
    }
}
?>
