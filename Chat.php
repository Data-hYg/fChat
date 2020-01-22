<?php
class Chat{
    private $host  = 'localhost';
    private $user  = 'dbConUser';
    private $password   = "phpConnect";
    private $database  = "fchat";   
    private $chatTable = 'chat';
    private $chatUsersTable = 'user';
    private $chatLoginDetailsTable = 'chat_logging';
    private $dbConnect = false;
    public function __construct(){
        if(!$this->dbConnect){ 
            $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            }else{
                $this->dbConnect = $conn;
            }
        }
    }
	private function getData($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$data= array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                    $data[]=$row;            
		}
		return $data;
	}
	private function getNumRows($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}
	public function loginUsers($username, $password){
		$sqlQuery = "
			SELECT userID, username 
			FROM ".$this->chatUsersTable." 
			WHERE username='".$username."' AND password='".$password."'";	
        return  $this->getData($sqlQuery);
	}		
        
        public function registerUser($username, $password, $profilepic, $facebook, $twitter, $instagram){
            $sqlQuery = "SELECT userID FROM ".$this->chatUsersTable."
                        WHERE username='".$username."'";
            if(!($this->getData($sqlQuery))){
                if($profilepic==""){
                    $profilepic = "test1.jpg";
                }
                $realQuery = "INSERT INTO ".$this->chatUsersTable."
                    (username, password, profilePic, current_session, status, facebook, twitter, instagram)
                    VALUES ('".$username."', '".$password."', '".$profilepic."', 1, 0,'".$facebook."','".$twitter."','".$instagram."')";
                //echo $realQuery;
                mysqli_query($this->dbConnect, $realQuery);
            }else{
                return "This Username is already in the Database.";
            }
        }
	public function chatUsers($userid){
		$sqlQuery = "
			SELECT * FROM ".$this->chatUsersTable." 
			WHERE userID != '$userid'";
		return  $this->getData($sqlQuery);
	}
	public function getUserDetails($userid){
		$sqlQuery = "
			SELECT * FROM ".$this->chatUsersTable." 
			WHERE userID = '$userid'";
		return  $this->getData($sqlQuery);
	}
	public function getUserAvatar($userid){
		$sqlQuery = "
			SELECT profilePic 
			FROM ".$this->chatUsersTable." 
			WHERE userID = '$userid'";
		$userResult = $this->getData($sqlQuery);
		$userAvatar = '';
		foreach ($userResult as $user) {
			$userAvatar = $user['profilePic'];
		}	
		return $userAvatar;
	}	
	public function updateUserOnline($userId, $online) {		
		$sqlUserUpdate = "
			UPDATE ".$this->chatUsersTable." 
			SET status = '".$online."' 
			WHERE userID = '".$userId."'";			
		mysqli_query($this->dbConnect, $sqlUserUpdate);		
	}
	public function insertChat($reciever_userid, $user_id, $chat_message) {
		$sqlInsert = "
			INSERT INTO ".$this->chatTable." 
			(reciever_userID, sender_userID, message, status) 
			VALUES ('".$reciever_userid."', '".$user_id."', '".$chat_message."', '1')";
                print_r($sqlInsert);
		$result = mysqli_query($this->dbConnect, $sqlInsert);
		if(!$result){
			return ('Error in query: '. mysqli_error());
		} else {
			$conversation = $this->getUserChat($user_id, $reciever_userid);
			$data = array(
				"conversation" => $conversation			
			);
			echo json_encode($data);	
		}
	}
	public function getUserChat($from_user_id, $to_user_id) {
		$fromUserAvatar = $this->getUserAvatar($from_user_id);	
		$toUserAvatar = $this->getUserAvatar($to_user_id);			
                
                $sqlUpdate = "
			UPDATE ".$this->chatTable." 
			SET status = '0' 
			WHERE sender_userID = '".$to_user_id."' AND reciever_userID = '".$from_user_id."' AND status = '1'";
		mysqli_query($this->dbConnect, $sqlUpdate);
                        
		$sqlQuery = "
			SELECT * FROM ".$this->chatTable." 
			WHERE (sender_userID = '".$from_user_id."' 
			AND reciever_userID = '".$to_user_id."') 
			OR (sender_userID = '".$to_user_id."' 
			AND reciever_userID = '".$from_user_id."') 
			ORDER BY timestamp ASC";
		$userChat = $this->getData($sqlQuery);	
		$conversation = '<ul>';
		foreach($userChat as $chat){
			$user_name = '';
			if($chat["sender_userID"] == $from_user_id) {
				$conversation .= '<li class="sent">';
				$conversation .= '<img width="22px" height="22px" src="userpics/'.$fromUserAvatar.'" alt="" />';
			} else {
				$conversation .= '<li class="replies">';
				$conversation .= '<img width="22px" height="22px" src="userpics/'.$toUserAvatar.'" alt="" />';
			}			
			$conversation .= '<p>'.$chat["message"].'</p>';			
			$conversation .= '</li>';
		}		
		$conversation .= '</ul>';
		return $conversation;
	}
	public function showUserChat($from_user_id, $to_user_id) {		
		$userDetails = $this->getUserDetails($to_user_id);
		$toUserAvatar = '';
		foreach ($userDetails as $user) {
			$toUserAvatar = $user['profilePic'];
			$userSection = '<img src="userpics/'.$user['profilePic'].'" alt="" />
				<p>'.$user['username'].'</p>
				<div class="social-media">
                                <a target="_blank" rel="noopener noreferrer" href="'.$user['facebook'].'"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                                <a target="_blank" rel="noopener noreferrer" href="'.$user['twitter'].'"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                                <a target="_blank" rel="noopener noreferrer" href="'.$user['instagram'].'"><i class="fa fa-instagram" aria-hidden="true"></i></a>
				</div>';
		}		
		// get user conversation
		$conversation = $this->getUserChat($from_user_id, $to_user_id);	
		// update chat user read status		
		$sqlUpdate = "
			UPDATE ".$this->chatTable." 
			SET status = '0' 
			WHERE sender_userID = '".$to_user_id."' AND reciever_userID = '".$from_user_id."' AND status = '1'";
		mysqli_query($this->dbConnect, $sqlUpdate);		
		// update users current chat session
		$sqlUserUpdate = "
			UPDATE ".$this->chatUsersTable." 
			SET current_session= '".$to_user_id."' 
			WHERE userID = '".$from_user_id."'";
		mysqli_query($this->dbConnect, $sqlUserUpdate);		
		$data = array(
			"userSection" => $userSection,
			"conversation" => $conversation			
		 );
		 echo json_encode($data);		
	}	
	public function getUnreadMessageCount($senderUserid, $recieverUserid) {
		$sqlQuery = "
			SELECT * FROM ".$this->chatTable."  
			WHERE sender_userID = '$senderUserid' AND reciever_userID = '$recieverUserid' AND status = '1'";
		$numRows = $this->getNumRows($sqlQuery);
		$output = '';
		if($numRows > 0){
			$output = $numRows;
		}
		return $output;
	}	
	public function updateTypingStatus($is_type, $loginDetailsId) {		
		$sqlUpdate = "
			UPDATE ".$this->chatLoginDetailsTable." 
			SET is_typing = '".$is_type."' 
			WHERE ID = '".$loginDetailsId."'";
		mysqli_query($this->dbConnect, $sqlUpdate);
	}		
	public function fetchIsTypeStatus($userId){
		$sqlQuery = "
		SELECT is_typing FROM ".$this->chatLoginDetailsTable." 
		WHERE userID = '".$userId."' ORDER BY last_activity DESC LIMIT 1"; 
		$result =  $this->getData($sqlQuery);
		$output = '';
		foreach($result as $row) {
			if($row["is_typing"] == 'yes'){
				$output = ' - <small><em>Typing...</em></small>';
			}
		}
		return $output;
	}		
	public function insertUserLoginDetails($userId) {		
		$sqlInsert = "
			INSERT INTO ".$this->chatLoginDetailsTable."(userID) 
			VALUES ('".$userId."')";
		mysqli_query($this->dbConnect, $sqlInsert);
		$lastInsertId = mysqli_insert_id($this->dbConnect);
        return $lastInsertId;		
	}	
	public function updateLastActivity($loginDetailsId) {		
		$sqlUpdate = "
			UPDATE ".$this->chatLoginDetailsTable." 
			SET last_activity = now() 
			WHERE ID = '".$loginDetailsId."'";
		mysqli_query($this->dbConnect, $sqlUpdate);
	}	
	public function getUserLastActivity($userId) {
		$sqlQuery = "
			SELECT last_activity FROM ".$this->chatLoginDetailsTable." 
			WHERE userID = '$userId' ORDER BY last_activity DESC LIMIT 1";
		$result =  $this->getData($sqlQuery);
		foreach($result as $row) {
			return $row['last_activity'];
		}
	}	
        
        
        public function updateStatus($userID, $status) {
            
            $sqlUpdate = "
                UPDATE ".$this->chatUsersTable." 
		SET status = '".$status."'  
		WHERE userID = '".$userID."'";
            print_r($sqlUpdate);
            mysqli_query($this->dbConnect, $sqlUpdate);
	}
     //This is not working    
        public function getSettings($userID){
            	$userDetails = $this->getUserDetails($userID);
		$toUserAvatar = '';
		foreach ($userDetails as $user) {
			$toUserAvatar = $user['profilePic'];
			$settings = '<img src="userpics/'.$user['profilePic'].'" alt="" />
				<p><input type="text">userpics/'.$user['profilePic'].'</input></p><br>
				<div class="social-media">
                                    <div>
                                        <i class="fa fa-facebook" aria-hidden="true"></i>
                                        <input type="text">userpics/'.$user['profilePic'].'</input>
                                    </div>
                                    <div>
                                        <i class="fa fa-instagram" aria-hidden="true"></i>
                                        <input type="text">userpics/'.$user['profilePic'].'</input>
                                    </div>
                                    <div>
                                        <i class="fa fa-twitter" aria-hidden="true"></i>
                                        <input type="text">userpics/'.$user['profilePic'].'</input>
                                    </div>
				</div>';
		}	

                $data = array(
			"settings" => $settings
		 );
                return json_encode($data);
        }
}
?>