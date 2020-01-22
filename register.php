<?php 
SESSION_START();
include('./header.php');
$registerError = '';
if (!empty($_POST['username']) && !empty($_POST['pwd']) && !empty($_POST['pwdrep'])) {
    if($_POST['pwd'] == $_POST['pwdrep']){
	include ('Chat.php');
	$chat = new Chat();
	$user = $chat->registerUser($_POST['username'], $_POST['pwd'],$_POST['picture'], $_POST['facebook'], $_POST['twitter'], $_POST['instagram']);
	if(!empty($user)) {
            $registerError = $user;
	} else {
            $registerError = "Thank you for your registration. \n"
                    . "Please go to <strong><a href='login.php'><h3> Chat access</h3></a></strong>.";
	}
    }else{
        $registerError = "Passwords do not match.";
    }
} else{
    $registerError = "Please fill in all requiered fields";
}

?>
<?php include('./container.php');?>

<link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.2/css/font-awesome.min.css'>
<link href="css/style.css" rel="stylesheet" id="bootstrap-css">
<script src="js/chat.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="js/jquery-3.4.1.min.js"></script>

<style>
.modal-dialog {
    width: 400px;
    margin: 30px auto;	
}
</style>
<div class="container">		
	<h2>FChat</h1>		
	<div class="row">
		<div class="col-sm-4">
			<form method="post" enctype="multipart/form-data">
				<div class="form-group">
				<?php if ($registerError ) { ?>
					<div class="alert alert-warning"><?php echo $registerError; ?></div>
				<?php } ?>
				</div>
				<div class="form-group">
                                    <label for="profile_picture">Select Avatar Picture:</label><br>
                                    <input type="radio" name="picture" checked="checked" value="test1.jpg">
                                    <img src="userpics/test1.jpg" alt="" />
                                    <input type="radio" name="picture" value="user5.jpg">
                                    <img src="userpics/user5.jpg" alt="" />
                                    <br>
                                    <label for="username">Username:</label>
                                    <input type="username" class="form-control" name="username" required>
				</div>
				<div class="form-group">
					<label for="pwd">Password:</label>
					<input type="password" class="form-control" name="pwd" required>
                                        <label for="pwd">Repeat password:</label>
					<input type="password" class="form-control" name="pwdrep" required>
                                        <label for="facebook">Facebook (optional):</label>
					<input type="text" class="form-control" name="facebook" >
                                        <label for="twitter">Twitter (optional):</label>
					<input type="text" class="form-control" name="twitter" >
                                        <label for="instagram">Instagram (optional):</label>
					<input type="text" class="form-control" name="instagram" >
				</div>  
				<button type="submit" name="register" class="btn btn-info">Register</button>
			
                        </form>
		</div>
		
	</div>
</div>	
<?php include('./footer.php');?>






