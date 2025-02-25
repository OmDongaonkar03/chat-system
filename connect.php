<?php
	include_once('config.php');
	$user_email = $_SESSION['user'];

	$work = $_GET['param'];
	
	if ($work == 'searchfriend') {
		if(isset($_GET['input'])){
			$input = $_GET['input'];
			$avb_users_sql = mysqli_query($conn, "SELECT * FROM users WHERE (name LIKE '%$input%' OR userName LIKE '%$input%') AND no NOT IN (SELECT no FROM users WHERE email = '$user_email') AND no NOT IN (SELECT from_user FROM request WHERE to_user = (SELECT no FROM users WHERE email = '$user_email') AND status = 'Accepted' UNION SELECT to_user FROM request WHERE from_user = (SELECT no FROM users WHERE email = '$user_email') AND status = 'Accepted');");
			
			if(mysqli_num_rows($avb_users_sql) > 0){
				while($avb_users = mysqli_fetch_assoc($avb_users_sql)){
					echo '
					<div class="search-result-item">
						<img src="" alt="">
						<div class="search-result-info">
							<div class="search-result-name">' . $avb_users['name'] . '</div>
							<div class="search-result-username">' . $avb_users['userName'] . '</div>
						</div>
						<button class="add-friend-btn" type="button" onclick="addfriend()">
							Add Friend
						</button>
					</div>';
				}
			}else{
				echo'
				</div>
				<div class="search-results d-flex justify-content-center align-items" id="searchResults">
					No User Found
				</div>';
			}
		}
	}
	
	if ($work == 'friendreq') {
		if (isset($_GET['param']) && $_GET['param'] == 'friendreq' && isset($_GET['from_userName'])) {
			$to_id = $_GET['to_id'];
			$to_name = $_GET['to_name'];
			$from_id = $_GET['from_id'];
			$from_name = $_GET['from_name'];
			$from_username = $_GET['from_userName'];
		
			$sql = "INSERT INTO request (from_user, from_name, from_userName, to_user, to_name, status) VALUES ('$from_id', '$from_name', '$from_username', '$to_id', '$to_name', 'Pending')";
			
			if (mysqli_query($conn,$sql)) {
				echo "Request Sent!";
			} else {
				echo "Error: " . mysqli_error($conn);
			}
		}
	}
	
	if($work == 'reqaction'){
		$action = $_GET['action'];
		$id = $_GET['id'];
		
		if($action == 'accept'){
			$change_status = mysqli_query($conn,"UPDATE `request` SET `status`='Accepted' WHERE `no` = '$id'");
		} else {
			$change_status = mysqli_query($conn,"DELETE FROM `request` WHERE `no` = '$id'");
		}
		$user = $_SESSION['user'];
		$usersql = mysqli_query($conn,"SELECT * FROM `users` WHERE `email` = '$user'");
		$userdata = mysqli_fetch_assoc($usersql);
			
		$requests = mysqli_query($conn, "SELECT * FROM request WHERE to_user = '" . $userdata['no'] . "' AND status = 'Pending'");
		
		if(mysqli_num_rows($requests) > 0){
			while($request_data = mysqli_fetch_assoc($requests)){
				echo '
					<div class="request-item">
						<img src="https://cdn-icons-png.flaticon.com/512/219/219983.png" alt="User" style="width: 50px; height: 50px; border-radius: 50%;">
						<div class="request-content">
							<h6 class="mb-1">' . $request_data['from_name'] . '</h6>
							<small class="text-muted">' . $request_data['from_userName'] . '</small>
						</div>
						<div class="request-actions">
							<button class="accept-btn" id="req-' . $request_data['no'] . '" 
								onclick="request_action(\'accept\', ' . $request_data['no'] . ')">
								Accept
							</button>
							<button class="decline-btn" id="req-' . $request_data['no'] . '" 
								onclick="request_action(\'reject\', ' . $request_data['no'] . ')">
								Decline
							</button>
						</div>
					</div>
				';
			}
		} else {
			echo '
				<div class="d-flex justify-content-center align-items-center">
					No Friend Requests
				</div>
			';
		}
	}
	
	if($work == 'sendmsg'){
		$from = $_GET['from'];
		$to = $_GET['to'];
		$msg = $_GET['msg'];
		
		$add_msg = mysqli_query($conn,"INSERT INTO `chats`(`from_user`, `to_user`, `msg`) VALUES ('$from','$to','$msg')");
		
		if($add_msg){
			$person_chats_sql = mysqli_query($conn, "SELECT * FROM `chats` 
			WHERE
			(`from_user` = '$from' AND `to_user` = '$to')
			OR 
			(`from_user` = '$to' AND `to_user` = '$from')  
			ORDER BY no ASC");
			
			while($chat = mysqli_fetch_assoc($person_chats_sql)) {                
				$is_sent = ($chat['from_user'] == $from);
				echo '
				<div class="message ' . ($is_sent ? 'message-sent' : 'message-received') . '">
					' . $chat['msg'] . '
				</div>';
			}
		}
	}
	
	if($work == 'updtchats'){
		$me = $_GET['from'];
		$him = $_GET['to'];
		
		$person_chats_sql = mysqli_query($conn, "SELECT * FROM `chats` 
		WHERE
		(`from_user` = '$me' AND `to_user` = '$him')
		OR 
		(`from_user` = '$him' AND `to_user` = '$me')  
		ORDER BY no ASC");
		
		while($chat = mysqli_fetch_assoc($person_chats_sql)) {                
			$is_sent = ($chat['from_user'] == $me);
			echo '
			<div class="message ' . ($is_sent ? 'message-sent' : 'message-received') . '">
				' . $chat['msg'] . '
			</div>';
		}
	}
?>