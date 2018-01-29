<?php

print '<h1>Su cosa vuoi lavorare?</h1>';
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
        
        $res = $myDB->listDatabases();
        print "Database:"."</br>";
        foreach ($res as $value) {
            print '<a href="edit.php?db='.$value.'">'.$value.'</a>';
            print ' ';
            print '<a href="delete.php?db='.$value.'">Elimina</a>';
            print "</br>";
        }
        print "</br>";
        print '<a href="create.php">Crea nuovo database</a>';
        print "</br>";
        
        print "Utenti:"."</br>";
        print '<a href="createusr.php">Crea nuovo utente</a>';
        
    }
    
}
else {
    //a
    if($user=="" && $pass==""){
        print '<form action="login.php">Username: <input type="text" name="user" /><br /> Password: <input type="password" name="pass" /><br /><br /> <input type="submit" value="Login" /> <input type="reset" value="Annulla" /> </form>' ;
    }
}


?>
