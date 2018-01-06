<?php
$user = $_GET['user'];
$pass = $_GET['pass'];

require_once('database.php');

$cookiename = "papyrus".$_SERVER['SERVER_ADDR'];
if ($_COOKIE[$cookiename] != ""){
    print '<p><a href="logout.php">Logout</a></p><br>';
}
else {
    //a
    if($user=="" && $pass==""){
        print '<form action="login.php">Username: <input type="text" name="user" /><br /> Password: <input type="password" name="pass" /><br /><br /> <input type="submit" value="Login" /> <input type="reset" value="Annulla" /> </form>' ;
    }
    
    
    if ($user!="" && $pass!=""){
        
        $myDB = new sqlDB($user, $pass);
        $res = $myDB->checkLogin();
        if (substr($res[0], 0, 7) == 'ERROR: ') {
            print_r($res);
        } else {
            $vettore = array($user,$pass);
            setcookie($cookiename, json_encode($vettore), time() + 12000);
            print '<a href="index.php">Index</a><META http-equiv="REFRESH" content="0; url=index.php">';
        }
        
        
        
        
    }
}


?>
