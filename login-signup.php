<?php
	include('config.php');
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		if(isset($_POST['login'])){
			$email = $_POST['email'];
			$password = $_POST['pass'];
			
			$check = mysqli_query($conn,"SELECT * FROM `users` WHERE `email` = '$email' AND `pass` = '$password'");
			if(mysqli_num_rows($check) > 0){
				$_SESSION['user'] = $email;
				header('location:index.php');
				exit();
			}else{
				echo "<script>alert('Invalid details')</script>";
			}
		}
		
		if(isset($_POST['signup'])){
			$Uname = $_POST['Uname'];
			$Uusername = $_POST['Uusername'];
			$Uemail = $_POST['Uemail'];
			$Upassword = $_POST['Upass'];
			
			$available = mysqli_query($conn,"SELECT * FROM `users` WHERE `email` = '$Uemail' AND `userName` = '$Uusername'");
			if(mysqli_num_rows($available) > 0){
				echo "<script>alert('Email Already Exist')</script>";
			}else{
				$add = mysqli_query($conn,"INSERT INTO `users`(`name`,`userName`,`email`, `pass`) VALUES ('$Uname','$Uusername','$Uemail','$Upassword')");
				
				if(!$add){
					echo "<script>alert('Somthing Went Wrong')</script>";
					exit();
				}
				
				$_SESSION['user'] = $Uemail;
				header('location:index.php');
				exit();
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Signup - Modern Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0084ff;
            --secondary-color: #6c757d;
            --background-color: #f8f9fa;
            --card-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }

        body {
            background: var(--background-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        .main-container {
            width: 100%;
            max-width: 1000px;
            margin: 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .auth-container {
            display: flex;
            min-height: 600px;
        }

        .auth-sidebar {
            flex: 1;
            background: linear-gradient(135deg, #0084ff, #00c6ff);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .auth-sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('/api/placeholder/500/500') center/cover;
            opacity: 0.1;
        }

        .auth-sidebar h2 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            position: relative;
        }

        .auth-sidebar p {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
        }

        .auth-content {
            flex: 1.2;
            padding: 3rem;
            background: white;
        }

        .auth-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .auth-header h3 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .tab-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            justify-content: center;
        }

        .tab-btn {
            padding: 0.75rem 2rem;
            border: none;
            background: none;
            font-size: 1rem;
            color: var(--secondary-color);
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-control {
            height: 52px;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0,132,255,0.1);
        }

        .form-label {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            padding: 0 0.5rem;
            color: var(--secondary-color);
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .form-control:focus + .form-label,
        .form-control:not(:placeholder-shown) + .form-label {
            top: 0;
            transform: translateY(-50%) scale(0.9);
            color: var(--primary-color);
        }

        .btn-auth {
            width: 100%;
            height: 52px;
            background: var(--primary-color);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .btn-auth:hover {
            background: #0073e6;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,132,255,0.2);
        }

        .social-login {
            margin-top: 2rem;
            text-align: center;
        }

        .social-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1rem;
        }

        .social-btn {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 2px solid #e2e8f0;
            color: #4a5568;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .helper-text {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1rem 0;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
            }

            .auth-sidebar {
                padding: 2rem;
                text-align: center;
            }

            .auth-content {
                padding: 2rem;
            }

            .main-container {
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="auth-container">
            <div class="auth-sidebar">
                <h2>Welcome Back!</h2>
                <p>Connect with friends and join our community. Start your journey with us today.</p>
            </div>
            
            <div class="auth-content">
                <div class="auth-header">
                    <h3>Get Started</h3>
                    <p class="text-muted">Please enter your details to continue</p>
                </div>

                <div class="tab-buttons">
                    <button class="tab-btn active" onclick="switchTab('login')">Login</button>
                    <button class="tab-btn" onclick="switchTab('signup')">Sign Up</button>
                </div>

                <!-- Login Form -->
                <form id="login-form" class="auth-form active" method="POST">
                    <div class="form-group">
                        <input type="email" class="form-control" id="loginEmail" name="email" placeholder=" ">
                        <label class="form-label" for="loginEmail">Email address</label>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="loginPassword" name="pass" placeholder=" ">
                        <label class="form-label" for="loginPassword">Password</label>
                    </div>

                    <button type="submit" name="login" class="btn-auth">Login</button>
                </form>

                <!-- Signup Form -->
                <form id="signup-form" class="auth-form" style="display: none;" method="POST">
                    <div class="form-group">
                        <input type="text" class="form-control" id="signupName" name="Uname" placeholder=" ">
                        <label class="form-label" for="signupName">Full Name</label>
                    </div>
					<div class="form-group">
                        <input type="text" class="form-control" id="signupName" name="Uusername" placeholder=" ">
                        <label class="form-label" for="signupName"> Set a User name</label>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" id="signupEmail" name="Uemail" placeholder=" ">
                        <label class="form-label" for="signupEmail">Email address</label>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="signupPassword" name= "Upass" placeholder=" ">
                        <label class="form-label" name="signup" for="signupPassword">Password</label>
                    </div>

                    <button type="submit" name="signup" class="btn-auth">Create Account</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            
            document.querySelectorAll('.auth-form').forEach(form => form.style.display = 'none');
            
            event.target.classList.add('active');
            
            document.getElementById(`${tab}-form`).style.display = 'block';
        }
    </script>
</body>
</html>