<?php 
SESSION_START();
include ('Chat.php');
$chat = new Chat();
$chat->updateUserOnline($_SESSION['userID'], 0);
$_SESSION['username'] = "";
$_SESSION['userID']  = "";
$_SESSION['login_details_id']= "";
header("Location:index.php");
?>






