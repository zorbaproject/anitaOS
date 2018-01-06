<?php
$cookiename = "papyrus".$_SERVER['SERVER_ADDR'];
    setcookie($cookiename);
    print 'Logout fatto.<p><a href="index.php">Homepage</a></p>';
print '<META http-equiv="REFRESH" content="0; url=index.php">';
?> 
