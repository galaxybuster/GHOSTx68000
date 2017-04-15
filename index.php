<?php
	header('Content-Type: text/html;charset=utf-8');
	require_once 'lib/Twig/Autoloader.php';
	Twig_Autoloader::register();

	require_once 'lib/init.php';
	require_once 'lib/database.class.php';
	require_once 'lib/user.class.php';
	require_once 'lib/catchcopy.php';

	// database
	$db = Database::getInstance();

	// Configure Twig
	$loader = new Twig_Loader_Filesystem('templates/');
	$twig = new Twig_Environment($loader);

	$export = array(
		'title' => 'GHOSTx68000',
		'assets' => 'templates/',
		'emailR' => "",
		'email' => "",
		'usernameR' => "",
		'time' => date('Y-m-d  G:i')
	);

	if (isset($_SESSION['user'])) {
		/*
		 * ACTUAL MEAT OF GAME STUFF
		**/
		include 'UserManager.php';
		include 'LocationManager.php';
		$location = UserManager::userGetLocation($_SESSION['user']['id']);
		$export['location'] = LocationManager::getLocationInfo($location);

		echo $twig->render('index.html', $export);
	} else {
		$export['eyecatcher'] = $catchcopy[mt_rand(0, sizeof($catchcopy) - 1)];

		if (isset($_POST['submit-login'])) {
			// Credentials passed to the server
			$user = new User();
			$success = $user->login($_POST['email'], $_POST['pass']);
			// $user->login handles the session, so i think we're done here
			if ($success) {
				// add user to active users table.
				include 'UserManager.php';
				UserManager::initiateStatus($_SESSION['user']['id']);

				// redirect to this page again with the session now set.
				header("Location: ".$GLOBALS['config']['domain'].$GLOBALS['config']['directory']."?profile=".$_SESSION['user']['username']);
				die("Redirecting");
			} else {
				$export['response'] = "Username or Password Do Not Match";
				// re-populate fields with submitted values (except password)
				$export['email'] = $_POST['email'];
			}
		} else if (isset($_POST['submit-register'])) {
			// re-populate fields with submitted values (except password)
			$export['usernameR'] = $_POST['username'];
			$export['emailR'] = $_POST['email'];

			// make sure required fields are submitted
			$required = array('email', 'password', 'retype', 'username');
			// Loop over field names, make sure each one exists and is not empty
			$error = false;
			foreach($required as $field) {
				if (empty($_POST[$field])) {
					$error = true;
				}
			}
			if ($error) {
				$export['responseR'] = "Missing required field.";
				die($twig->render('login.html', $export));
			}

			// ensure that email is actually an email
			if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
				$export['responseR'] = "Please enter a valid email address.";
				die($twig->render('login.html', $export));
			}

			// ensure that passwords match
			if ($_POST['password'] !== $_POST['retype']) {
				$export['responseR'] = "Passwords do not match.";
				die($twig->render('login.html', $export));
			}

			// ensure username meets criteria
			if (!preg_match("/^[A-Za-z0-9_]{3,16}$/", $_POST['username'])) {
				$export['responseR'] = "Username must be between 3 and 16 characters, alphanumeric with underscore.";
				die($twig->render('login.html', $export));
			}

			$user = new User();

			// make sure that username or email do not already belong to a user
			if ($user->userWithNameExists($_POST['username'])) {
				$export['responseR'] = "Someone already has that username...";
				die($twig->render('login.html', $export));
			} else if ($user->userWithEmailExists($_POST['email'])) {
				$export['responseR'] = "Someone has already signed up with that email...";
				die($twig->render('login.html', $export));
			} else {

				// Still here? Sign em up
				$success = $user->queueNewUser($_POST['email'], $_POST['username'], $_POST['password']);

				if ($success) {
					$export['responseR'] = "Verification email sent. Check your email to complete your registration.";
				} else {
					$export['responseR'] = "Error registering account.";
				}

				die($twig->render('login.html', $export));
			}
		}

		echo $twig->render('login.html', $export);
	}