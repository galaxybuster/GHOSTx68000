<?php
	require_once("lib/database.class.php");

	if (isset($_GET['key'])) {
		// Check if it exists

		$db = Database::getInstance();
		$db->query("SELECT * FROM users_confirm WHERE activation_key = ?", array($_GET['key']));
		$result = $db->firstResult();
		if ($result != null) {
			$db->query("INSERT INTO users (email, username, password, salt) VALUES (?, ?, ?, ?)", array($result['email'], $result['username'], $result['password'], $result['salt']));
			
			// Remove the data from confirm table
			$db->query("DELETE FROM users_confirm WHERE activation_key = ?", array($_GET['key']));

			echo "Successfully confirmed account.<br/>";
			echo "<a href='index.php'>click here to login</a>";
			
		} else {
			echo "Error...";
		}
	} else {
		// Nothing set, redirect to index
		header("Location: index.php");
		die("Redirecting");
	}