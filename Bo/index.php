<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Online Photo Gallery - Share your photos</title>

        <!-- CSS -->
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
        <link rel="stylesheet" href="ref/bower_components/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="ref/bower_components/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="ref/dist/css/form-elements.css">
        <link rel="stylesheet" href="ref/dist/css/style.css">

    </head>

    <body>
        <!-- Top content -->
        <div class="top-content">
            <div class="inner-bg">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2 text">
                            <h1><strong>Online Photo Gallery</strong></h1>
                            <div class="description">
                            	<p>
	                            	This is a free Online Photo Gallery. Share your wonderful photos with your friends and family.
                            	</p>
								<?php
								if ( $_GET['ERR'] == 'pswd' ) {
									echo '<p><font color="red"><b>Wrong Password!</b></font></p>';
								} elseif ( $_GET['ERR'] == 'session' ) {
									echo '<p><font color="red"><b>Your Session Has Been Expired!</b></font></p>';
								} elseif ( $_GET['ERR'] == 'name' ) {
									echo '<p><font color="red"><b>Unknown Username!</b></font></p>';
								} elseif ( $_GET['ERR'] == 'err' ) {
									echo '<p><font color="red"><b>Unknown Error! Please retry!</b></font></p>';
								} elseif ( $_GET['ERR'] == 'role' ) {
									echo '<p><font color="red"><b>You Don\'t have Permission!</b></font></p>';
								} elseif ( $_GET['ERR'] == 'dup-name' ) {
                                    echo '<p><font color="red"><b>User Name has been registered!</b></font></p>';
                                } elseif ( $_GET['ERR'] == 'dup-email' ) {
                                    echo '<p><font color="red"><b>Email has been registered!</b></font></p>';
                                }
								?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-5">
                        	
                        	<div class="form-box">
	                        	<div class="form-top">
	                        		<div class="form-top-left">
	                        			<h3>Login</h3>
	                            		<p>Enter username and password to log in:</p>
	                        		</div>
	                        		<div class="form-top-right">
	                        			<i class="fa fa-lock"></i>
	                        		</div>
	                            </div>
	                            <div class="form-bottom">
				                    <form role="form" action="login.php" method="post" class="login-form">
				                    	<div class="form-group">
				                    		<label class="sr-only" for="form-login-username">Username</label>
				                        	<input type="text" name="login-username" placeholder="Username..." class="form-username form-control" id="form-login-username">
				                        </div>
				                        <div class="form-group">
				                        	<label class="sr-only" for="form-login-password">Password</label>
				                        	<input type="password" name="login-password" placeholder="Password..." class="form-password form-control" id="form-login-password">
				                        </div>
				                        <button type="submit" class="btn" name="login-button">Login</button>
				                    </form>
			                    </div>
		                    </div>

	                        
                        </div>
                        
                        <div class="col-sm-1 middle-border"></div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-5">
                        	<div class="form-box">
                        		<div class="form-top">
	                        		<div class="form-top-left">
	                        			<h3>Sign up now</h3>
	                            		<p>Fill in the form below to sign up and get instant access:</p>
	                        		</div>
	                        		<div class="form-top-right">
	                        			<i class="fa fa-pencil"></i>
	                        		</div>
	                            </div>
	                            <div class="form-bottom">
				                    <form role="form" action="signup.php" method="post" class="registration-form">
										<div class="form-group">
											<label class="sr-only" for="form-signup-username">Username</label>
											<input type="text" name="signup-username" placeholder="User name..." class="form-username form-control" id="form-signup-username">
										</div>
                                        <div class="form-group">
                                            <label class="sr-only" for="form-signup-password">Password</label>
                                            <input type="password" name="signup-password" placeholder="New Password..." class="form-password form-control" id="form-signup-password">
                                        </div>
                                        <div class="form-group">
                                            <label class="sr-only" for="form-signup-confirm-password">Confirm Password</label>
                                            <input type="password" name="signup-confirm-password" placeholder="Confirm New Password..." class="form-password form-control" id="form-signup-confirm-password">
                                        </div>
										<div class="form-group">
				                    		<label class="sr-only" for="form-first-name">First name</label>
				                        	<input type="text" name="signup-first-name" placeholder="First name..." class="form-first-name form-control" id="form-first-name">
				                        </div>
				                        <div class="form-group">
				                        	<label class="sr-only" for="form-last-name">Last name</label>
				                        	<input type="text" name="signup-last-name" placeholder="Last name..." class="form-last-name form-control" id="form-last-name">
				                        </div>
				                        <div class="form-group">
				                        	<label class="sr-only" for="form-email">Email</label>
				                        	<input type="text" name="form-email" placeholder="Email..." class="form-email form-control" id="form-email">
				                        </div>
										<div class="form-group">
											<label class="sr-only" for="form-phone">Email</label>
											<input type="text" name="form-phone" placeholder="Phone..." class="form-phone form-control" id="form-phone">
										</div>
				                        <div class="form-group">
				                        	<label class="sr-only" for="form-address">About yourself</label>
				                        	<textarea name="form-address" placeholder="Address..."
				                        				class="form-address form-control" id="form-address"></textarea>
				                        </div>
				                        <button type="submit" class="btn" name="signup-button">Sign up</button>
				                    </form>
			                    </div>
                        	</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
        	<div class="container">
        		<div class="row">
        			
        			<div class="col-sm-8 col-sm-offset-2">
        				<div class="footer-border"></div>
        				<p>Copyright by Bo Zhou, Baihong Qi, Yueran Sun. 2016</p>
        			</div>
        			
        		</div>
        	</div>
        </footer>

        <!-- Javascript -->
        <script src="ref/dist/js/jquery-1.11.1.min.js"></script>
        <script src="ref/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="ref/dist/js/jquery.backstretch.min.js"></script>
        <script src="ref/dist/js/scripts.js"></script>

        <!--[if lt IE 10]>
        <script src="ref/dist/js/placeholder.js"></script>
        <![endif]-->

    </body>

</html>