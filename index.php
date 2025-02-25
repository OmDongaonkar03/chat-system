<?php
    include_once('config.php');
    $user = $_SESSION['user'];
    if(!$user){
        header('location:login-signup.php');
        exit();
    }
    $usersql = mysqli_query($conn,"SELECT * FROM `users` WHERE `email` = '$user'");
    $userdata = mysqli_fetch_assoc($usersql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends Chat - Modern Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0084ff;
            --secondary-color: #6c757d;
            --background-color: #f8f9fa;
            --card-shadow: 0 8px 24px rgba(0,0,0,0.1);
            --gradient-start: #0084ff;
            --gradient-end: #00c6ff;
        }

        body {
            background: var(--background-color);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            overflow-y: hidden;
        }

        .sidebar {
            width: 70px;
            background: linear-gradient(190deg, var(--gradient-end) 0%, var(--gradient-start) 100%);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem 0;
            z-index: 1000;
        }

        .sidebar-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .sidebar-icon:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .sidebar-icon.active {
            background: white;
            color: var(--primary-color);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-container {
            margin-left: 70px;
            width: calc(100% - 70px);
            height: 100vh;
            background: white;
            overflow: hidden;
        }

        .chat-container {
            display: flex;
            height: 100%;
        }

        .friends-list {
            width: 300px;
            border-right: 1px solid #e2e8f0;
            background: white;
            overflow-y: auto;
        }

        .friends-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            background: linear-gradient(100deg, var(--gradient-end) 0%, var(--gradient-start) 100%);
            color: white;
        }

        .friends-header .btn-link {
            color: white;
        }

        .friend-item {
            padding: 1rem;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 1px solid #f0f2f5;
        }

        .friend-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }

        .friend-item.active {
            background: linear-gradient(135deg, rgba(0,132,255,0.1) 0%, rgba(0,198,255,0.1) 100%);
        }

        .friend-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-right: 1rem;
            background: transparent;
            position: relative;
        }

        .friend-avatar::after {
            content: '';
            width: 12px;
            height: 12px;
            background: #22c55e;
            border: 2px solid white;
            border-radius: 50%;
            position: absolute;
            bottom: 0;
            right: 0;
        }

        .friend-info {
            flex: 1;
        }

        .friend-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .friend-status {
            font-size: 0.875rem;
            color: var(--secondary-color);
        }

        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        .chat-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            color: white;
            max-height: 86px;
        }

        .chat-header .friend-name {
            color: white;
        }

        .chat-header .friend-status {
            color: rgba(255, 255, 255, 0.8);
        }

        .chat-messages {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            background: #f8f9fa;
        }

        .message {
            margin-bottom: 1rem;
            max-width: 70%;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message-sent {
            margin-left: auto;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            color: white;
            border-radius: 1rem 1rem 0 1rem;
            padding: 0.75rem 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .message-received {
            background: white;
            border-radius: 1rem 1rem 1rem 0;
            padding: 0.75rem 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .chat-input-area {
            padding: 1rem;
            background: white;
            border-top: 1px solid #e2e8f0;
        }

        .chat-form {
            display: flex;
            gap: 0.5rem;
        }

        .message-input {
            flex: 1;
            border: 2px solid #e2e8f0;
            border-radius: 1.5rem;
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            resize: none;
        }

        .message-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0,132,255,0.1);
            outline: none;
        }

        .send-button {
            width: 46px;
            height: 46px;
            border-radius: 23px;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            border: none;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .send-button:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0,132,255,0.3);
        }

        .search-modal .modal-content {
            border: none;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .search-modal .modal-header {
            border-bottom: none;
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            border-radius: 20px 20px 0 0;
            color: white;
        }

        .search-modal .modal-body {
            padding: 1.5rem;
        }

        .search-input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem;
            padding-right: 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0,132,255,0.1);
            outline: none;
        }

        .search-button {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            padding: 0.5rem;
        }

        .search-results {
            max-height: 400px;
            overflow-y: auto;
        }

        .search-result-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .search-result-item:hover {
            background: #f8f9fa;
        }

        .search-result-item img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-right: 1rem;
        }

        .search-result-info {
            flex: 1;
        }

        .search-result-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .search-result-username {
            font-size: 0.875rem;
            color: var(--secondary-color);
        }

        .menu-modal .modal-content {
            border: none;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .menu-modal .modal-header {
            border-bottom: none;
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            border-radius: 20px 20px 0 0;
            color: white;
        }

        .menu-modal .btn-close {
            filter: brightness(0) invert(1);
        }

        .notification-modal .modal-content,
        .requests-modal .modal-content {
            border-radius: 20px;
            overflow: hidden;
        }

        .notification-item,
        .request-item {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .notification-item:hover,
        .request-item:hover {
            background: #f8f9fa;
        }

        .notification-content,
        .request-content {
            margin-left: 1rem;
            flex: 1;
        }

        .request-actions {
            display: flex;
            gap: 0.5rem;
        }

        .request-actions button {
            padding: 0.25rem 1rem;
            border-radius: 15px;
            border: none;
            transition: all 0.3s ease;
        }

        .accept-btn {
            background: var(--gradient-start);
            color: white;
        }

        .decline-btn {
            background: #ff4757;
            color: white;
        }

        .notification-time {
            font-size: 0.75rem;
            color: var(--secondary-color);
        }

        .profile-section {
            text-align: center;
            padding: 2rem 1rem;
            position: relative;
            background: linear-gradient(135deg, rgba(0,132,255,0.1) 0%, rgba(0,198,255,0.1) 100%);
            margin: -1rem -1rem 1rem -1rem;
        }

        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 60px;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }

        .status-indicator {
            width: 16px;
            height: 16px;
            background: #22c55e;
            border: 2px solid white;
            border-radius: 50%;
            position: absolute;
            bottom: 55px;
            left: 50%;
            margin-left: 35px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            color: #333;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
        }

        .menu-item:hover {
            background: linear-gradient(135deg, rgba(0,132,255,0.1) 0%, rgba(0,198,255,0.1) 100%);
            transform: translateX(5px);
        }

        .menu-item i {
            width: 24px;
            margin-right: 1rem;
            color: var(--primary-color);
        }
		.menu-item .badge {
            margin-left: auto;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            border-radius: 12px;
            padding: 0.5em 0.8em;
        }

        .menu-item.danger {
            color: #dc3545;
        }

        .menu-item.danger i {
            color: #dc3545;
        }

        .menu-item.danger:hover {
            background: rgba(220, 53, 69, 0.1);
        }

        .divider {
            height: 1px;
            background: #e9ecef;
            margin: 1rem 0;
        }

        .add-friend-btn {
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .add-friend-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0,132,255,0.3);
        }

		.chat-messages {
			flex: 1;
			padding: 1.5rem;
			overflow-y: auto;
			display: flex;
			flex-direction: column;
			gap: 1rem;
			background: #f8f9fa;
		}
		
		.message {
			max-width: 70%;
			padding: 0.75rem 1rem;
			border-radius: 1rem;
			position: relative;
			word-wrap: break-word;
		}
		
		.message-sent {
			align-self: flex-end;
			background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
			color: white;
			border-radius: 1rem 1rem 0 1rem;
			margin-left: auto;
		}
		
		.message-received {
			align-self: flex-start;
			background: white;
			color: #333;
			border-radius: 1rem 1rem 1rem 0;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
		}
		
		.chat-input-area {
			padding: 1rem;
			background: white;
			border-top: 1px solid #e2e8f0;
		}
		
		.chat-form {
			display: flex;
			gap: 0.5rem;
			align-items: center;
		}
		
		.message-input {
			flex: 1;
			padding: 0.75rem 1.25rem;
			border: 2px solid #e2e8f0;
			border-radius: 1.5rem;
			outline: none;
			transition: all 0.3s ease;
		}
		
		.message-input:focus {
			border-color: var(--primary-color);
			box-shadow: 0 0 0 3px rgba(0,132,255,0.1);
		}
		
		.send-button {
			width: 46px;
			height: 46px;
			border-radius: 23px;
			background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
			border: none;
			color: white;
			display: flex;
			align-items: center;
			justify-content: center;
			transition: all 0.3s ease;
			cursor: pointer;
		}
		
		.send-button:hover {
			transform: scale(1.05);
			box-shadow: 0 2px 8px rgba(0,132,255,0.3);
		}
        @media (max-width: 768px) {
            .main-container {
                margin-left: 60px;
                width: calc(100% - 60px);
            }

            .sidebar {
                width: 60px;
            }

            .friends-list {
                width: 100%;
                max-width: 300px;
                position: absolute;
                left: -100%;
                transition: 0.3s;
                z-index: 1000;
                height: 100%;
            }

            .friends-list.active {
                left: 0;
            }

            .chat-area {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-icon active">
            <img src="https://cdn-icons-png.flaticon.com/512/219/219983.png" alt="Profile" style="width: 100%; height: 100%; border-radius: 50%;">
        </div>
        <div class="sidebar-icon" data-bs-toggle="modal" data-bs-target="#friendRequestsModal">
            <i class="fas fa-user-plus"></i>
			<?php
				$requests = mysqli_query($conn, "SELECT * FROM request WHERE to_user = '" . $userdata['no'] . "' AND status = 'Pending'");
				if(mysqli_num_rows($requests) > 0){
			?>
				<span class="notification-badge"><?php echo mysqli_num_rows($requests); ?></span>
			<?php
				}
			?>
        </div>
        <div class="sidebar-icon" data-bs-toggle="modal" data-bs-target="#notificationsModal">
            <i class="fas fa-bell"></i>
            <span class="notification-badge">3</span>
        </div>
        <div class="sidebar-icon">
            <i class="fas fa-cog"></i>
        </div>
        <a href ="login-signup.php" class="sidebar-icon text-decoration-none" style="margin-top: auto;">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>

    <div class="main-container">
        <div class="chat-container">
            <!-- Friends List -->
            <div class="friends-list">
                <div class="friends-header">
                    <h4 class="mb-0">Friends</h4>
                    <button class="btn btn-link ms-auto" data-bs-toggle="modal" data-bs-target="#searchFriendsModal">
                        <i class="fas fa-user-plus"></i>
                    </button>
                </div>
                <?php
                    $friend = mysqli_query($conn, "SELECT * FROM `request` WHERE (`to_user` = '$userdata[no]' OR `from_user` = '$userdata[no]') AND status = 'Accepted'");
                    if(mysqli_num_rows($friend) > 0){
                        while($friendData = mysqli_fetch_assoc($friend)){
                            $friendID = ($friendData['to_user'] == $userdata['no']) ? $friendData['from_user'] : $friendData['to_user'];
                            $friendName = ($friendData['to_user'] == $userdata['no']) ? $friendData['from_name'] : $friendData['to_name'];
                ?>
                <a href="index.php?friendID=<?php echo $friendID; ?>&friendName=<?php echo $friendName; ?>&userID=<?php echo $userdata['no']; ?>" class="friend-item active text-decoration-none">
                    <div class="friend-avatar"><img src="" alt="User" style="max-height:50px;max-width:50px;" class="rounded-circle me-3"></div>
                    <div class="friend-info">
                        <div class="friend-name"><?php echo $friendName; ?></div>
                    </div>
                </a>
                <?php
                        }
                    }
                ?>
            </div>

            <!-- Chat Area -->
			<div class="chat-area">
				<?php if(isset($_GET['friendID']) && isset($_GET['friendName']) && isset($_GET['userID'])) { 
					$other_person_id = $_GET['friendID'];
					$other_person_name = $_GET['friendName'];
					$self_person_id = $_GET['userID'];
				?>
					<div class="chat-header">
						<div class="d-flex align-items-center">
							<div class="friend-avatar">
								<img src="https://cdn-icons-png.flaticon.com/512/219/219983.png" 
									alt="User" 
									style="height:50px;width:50px;" 
									class="rounded-circle">
							</div>
							<div class="ms-3">
								<h5 class="mb-0 friend-name"><?php echo $other_person_name; ?></h5>
							</div>
						</div>
					</div>
			
					<div class="chat-messages" id="chatMessages">
						<?php
						$person_chats_sql = mysqli_query($conn, "SELECT * FROM `chats` 
							WHERE
							(`from_user` = '$self_person_id' AND `to_user` = '$other_person_id')
							OR 
							(`to_user` = '$self_person_id' AND `from_user` = '$other_person_id')
							ORDER BY no ASC");
			
						while($chat = mysqli_fetch_assoc($person_chats_sql)) {								
							$is_sent = ($chat['from_user'] == $self_person_id);
						?>
							<div class="message <?php echo $is_sent ? 'message-sent' : 'message-received'; ?>">
								<?php echo $chat['msg']; ?>
							</div>
						<?php } ?>
					</div>
			
					<div class="chat-input-area">
						<div class="chat-form">
							<input type="text" name="message" id="message" class="message-input" placeholder="Type your message..." required>
							<button type="button" name="send_message" class="send-button" onclick="sendmsg(<?php echo $self_person_id; ?>,<?php echo $other_person_id;?>)">
								<i class="fas fa-paper-plane"></i>
							</button>
						</div>
					</div>
			
				<?php } else { ?>
					<div class="d-flex flex-column justify-content-center align-items-center h-100 text-center text-muted">
						<i class="fas fa-comments fa-3x mb-3"></i>
						<h4>Welcome to Chat</h4>
						<p>Select a conversation to start chatting</p>
					</div>
				<?php } ?>
			</div>
        </div>
    </div>

    <!-- Friend Requests Modal -->
    <div class="modal fade requests-modal" id="friendRequestsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">	
                    <h5 class="modal-title">Friend Requests</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body">
					<?php
						$requests = mysqli_query($conn, "SELECT * FROM request WHERE to_user = '" . $userdata['no'] . "' AND status = 'Pending'");
						if(mysqli_num_rows($requests) > 0){
							while($request_data = mysqli_fetch_assoc($requests)){;
					?>
                    <div class="request-item">
                        <img src="https://cdn-icons-png.flaticon.com/512/219/219983.png" alt="User" style="width: 50px; height: 50px; border-radius: 50%;">
                        <div class="request-content">
                            <h6 class="mb-1"><?php echo $request_data['from_name'];?></h6>
                            <small class="text-muted"><?php echo $request_data['from_userName'];?></small>
                        </div>
                        <div class="request-actions">
                            <button class="accept-btn" id="req-<?php echo $request_data['no'];?>" onclick="request_action('accept',<?php echo $request_data['no'];?>)">	
								Accept
							</button>
                            <button class="decline-btn" id="req-<?php echo $request_data['no'];?>" onclick="request_action('reject',<?php echo $request_data['no'];?>)">	
								Decline
							</button>
                        </div>
                    </div>
					<?php
							}
						}else{
					?>
						<div class="d-flex justify-content-center align-items">
							No friend Request
						</div>
					<?php
						}
					?>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Modal -->
    <div class="modal fade notification-modal" id="notificationsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="notification-item">
                        <i class="fas fa-user-plus text-primary" style="font-size: 24px;"></i>
                        <div class="notification-content">
                            <p class="mb-1">John Doe accepted your friend request</p>
                            <span class="notification-time">2 hours ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Friends Modal -->
    <div class="modal fade search-modal" id="searchFriendsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Friends</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="search-input-group">
                        <input type="text" class="search-input" placeholder="Search for friends..." id="friendSearchInput" name="searchName" onkeyup="searchfriend()">
                    </div>
                    <div class="search-results" id="searchResults">
                    <?php
                    $avb_users_sql = mysqli_query($conn, "SELECT * FROM users WHERE no NOT IN (SELECT no FROM users WHERE email = '$user') AND no NOT IN (SELECT from_user FROM request WHERE to_user = (SELECT no FROM users WHERE email = '$user') AND status IN ('Accepted', 'Pending') UNION SELECT to_user FROM request WHERE from_user = (SELECT no FROM users WHERE email = '$user') AND status IN ('Accepted', 'Pending'))");

                    if(mysqli_num_rows($avb_users_sql) > 0){
                        while ($avb_users = mysqli_fetch_assoc($avb_users_sql)) {
							$user_id = $avb_users['no'];
							$check_request = mysqli_query($conn, "
								SELECT * FROM request 
								WHERE ((from_user = '$user' AND to_user = '$user_id') 
								OR (from_user = '$user_id' AND to_user = '$user')) 
								AND status = 'Pending'
							");
						
							$button_text = (mysqli_num_rows($check_request) > 0) ? "Sent!" : "Add Friend";
							$disabled = (mysqli_num_rows($check_request) > 0) ? "disabled" : "";
						?>
							<div class="search-result-item" id="search-result-item">
								<img src="" alt="">
								<div class="search-result-info">
									<div class="search-result-name"><?php echo $avb_users['name']; ?></div>
									<div class="search-result-username"><?php echo $avb_users['userName']; ?></div>
								</div>
								<button id="btn-<?php echo $avb_users['no']; ?>" class="add-friend-btn" type="button"
									onclick="addfriend(<?php echo $avb_users['no']; ?>, '<?php echo addslashes($avb_users['name']); ?>')"
									<?php echo $disabled; ?>>
									<?php echo $button_text; ?>
								</button>
							</div>
						<?php
						}
					}
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function searchfriend(){
            let word = document.getElementById("friendSearchInput").value;
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("searchResults").innerHTML = this.responseText;
                }
            };
            let param = "searchfriend";
            xhttp.open("GET","connect.php?param="+param+"&input="+word,true);
            xhttp.send();
        }
		
		function addfriend(to_id, to_name) {
			let from_req_id = "<?php echo $userdata['no']; ?>";
			let from_req_name = "<?php echo $userdata['name']; ?>";
			let from_req_username = "<?php echo $userdata['userName']; ?>";
			
			let xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function () {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("btn-" + to_id).innerText = "Sent!";
					document.getElementById("btn-" + to_id).disabled = true;
				}
			};
		
			let param = "friendreq";
			let url = "connect.php?param=" + param + "&to_id=" + to_id + "&to_name=" + to_name + "&from_id=" + from_req_id + "&from_name=" + from_req_name + "&from_userName=" + from_req_username;
		
			xhttp.open("GET", url, true);
			xhttp.send();
		}
		
		function request_action(action,num){			
			let xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function () {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("modal-body").innerHTML = this.responseText;
				}
			};
			let param = "reqaction";
            xhttp.open("GET","connect.php?param="+param+"&action="+action+"&id="+num,true);
            xhttp.send();
		}

        window.onload = function() {
            var chatMessages = document.querySelector('.chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
		
		function sendmsg(user,other){
			var msg = document.getElementById('message').value;
			let xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function () {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("chatMessages").innerHTML = this.responseText;
				}
			};
			let param = "sendmsg";
            xhttp.open("GET","connect.php?param="+param+"&msg="+msg+"&from="+user+"&to="+other,true);
            xhttp.send();
			
			document.getElementById('message').value = ''; 
		}
		
		setInterval(updatechats, 1000);
		
		function updatechats(){
			let user = '<?php echo $_GET['userID'];?>';
			let other = '<?php echo $_GET['friendID'];?>';
			
			let xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function () {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("chatMessages").innerHTML = this.responseText;
				}
			};
			let param = "updtchats";
            xhttp.open("GET","connect.php?param="+param+"&from="+user+"&to="+other,true);
            xhttp.send();
		}
    </script>
</body>
</html>