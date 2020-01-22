<?php 
session_start();
include('header.php');
?>
<title>FChat | Alex | Timo</title>
<link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.2/css/font-awesome.min.css'>
<link href="css/font-awesome.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet" id="bootstrap-css">
<script src="js/chat.js"></script>
<script src="js/jquery-3.4.1.min.js"></script>
<style>
.modal-dialog {
    width: 400px;
    margin: 30px auto;	
}
#dialog{

        display: none;

}
</style>
<?php include('container.php');?>
<div class="container">			
	<?php if(isset($_SESSION['userID']) && $_SESSION['userID']) { ?> 	
		<div class="chat">	
			<div id="frame">		
				<div id="sidepanel">
					<div id="profile">
					<?php
					include ('Chat.php');
					$chat = new Chat();
					$loggedUser = $chat->getUserDetails($_SESSION['userID']);
					echo '<div class="wrap">';
					$currentSession = '';
					foreach ($loggedUser as $user) {
						$currentSession = $user['current_session'];
						echo '<img id="profile-img" src="userpics/'.$user['profilePic'].'" class="online" alt="" />';
						echo  '<p>'.$user['username'].'</p>';
							//echo '<i class="fa fa-chevron-down expand-button" aria-hidden="true"></i>';
							echo '<div id="status-options">';
							echo '<ul>';
								echo '<li style="float: right" id="status-online" class="active"><span class="status-circle"></span> <p>Online&nbsp&nbsp</p></li>';
								echo '<li style="float: right" id="status-away"><span class="status-circle"></span> <p>Away&nbsp&nbsp</p></li>';
								echo '<li style="float: right" id="status-busy"><span class="status-circle"></span> <p>Busy&nbsp&nbsp</p></li>';
								echo '<li style="float: right" id="status-offline"><span class="status-circle"></span> <p>Offline&nbsp&nbsp</p></li>';
							echo '</ul>';
							echo '</div>';
                                                        /*
							echo '<div id="expanded">';	
                                                        echo '<input type="text">userpics/'.$user['profilePic'].'</input>';
							echo '<a href="logout.php">Logout</a>';
							echo '</div>';
                                                        */
                                                         
					}
					echo '</div>';
					?>
					</div>
					<div id="search">
						<label for=""><i class="fa fa-search" aria-hidden="true"></i></label>
						<input type="text" id="mySearch" onkeyup="searchUser()" placeholder="Search contacts..." />					
					</div>
					<div id="contacts">	
					<?php
					echo '<ul id="myUL">';
					$chatUsers = $chat->chatUsers($_SESSION['userID']);
					foreach ($chatUsers as $user) {
						$status = 'offline';
						if($user['status']) {
							$status = 'online';
						}
						$activeUser = '';
						if($user['userID'] == $currentSession) {
							$activeUser = "active";
						}
						echo '<li id="'.$user['userID'].'" class="contact '.$activeUser.'" data-touserID="'.$user['userID'].'" data-tousername="'.$user['username'].'">';
						echo '<div class="wrap">';
						echo '<span id="status_'.$user['userID'].'" class="contact-status '.$status.'"></span>';
						echo '<img src="userpics/'.$user['profilePic'].'" alt="" />';
						echo '<div class="meta">';
						echo '<p class="name">'.$user['username'].'<span id="unread_'.$user['userID'].'" class="unread">'.$chat->getUnreadMessageCount($user['userID'], $_SESSION['userID']).'</span></p>';
						echo '<p class="preview"><span id="isTyping_'.$user['userID'].'" class="isTyping"></span></p>';
						echo '</div>';
						echo '</div>';
						echo '</li>'; 
					}
					echo '</ul>';
					?>
					</div>
					<div id="bottom-bar">	
                                            <button onclick="location.href='logout.php'" type="button"><i class="fa fa-sign-out fa-fw"></i>Logout</button>
                                        <button id="settings"><i class="fa fa-cog fa-fw" aria-hidden="true"></i> <span>Settings</span></button>					
					</div>
				</div>			
				<div class="content" id="content"> 
					<div class="contact-profile" id="userSection">	
					<?php
					$userDetails = $chat->getUserDetails($currentSession);
					foreach ($userDetails as $user) {										
						echo '<img src="userpics/'.$user['profilePic'].'" alt="" />';
							echo '<p>'.$user['username'].'</p>';
					}	
					?>						
					</div>
					<div class="messages" id="conversation">		
					<?php
					echo $chat->getUserChat($_SESSION['userID'], $currentSession);						
					?>
					</div>
					<div class="message-input" id="replySection">				
						<div class="message-input" id="replyContainer">
							<div class="wrap">
								<input type="text" class="chatMessage" id="chatMessage<?php echo $currentSession; ?>" placeholder="Write your message..." />
								<button class="submit chatButton" id="chatButton<?php echo $currentSession; ?>" style="padding-top: 5px;padding-bottom: 12px;"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>	
							</div>
						</div>					
					</div>
				</div>
			</div>
		</div>
	<?php } else { ?>
		<br>
		<br>
		<strong><a href="login.php"><h3>Login To Access Chat System</h3></a></strong>
                <strong><a href="register.php"><h3>Register To Chat System</h3></a></strong>		

	<?php } ?>
	<br>
	<br>		
</div>	
<?php include('footer.php');?>