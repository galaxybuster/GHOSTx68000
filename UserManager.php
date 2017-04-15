<?php
	require_once('lib/database.class.php');

	class UserManager {
		public static function initiateStatus($userID) {
			// adds user to active users table.

			$db = Database::getInstance();

			// first, delete if exists. 
			$query = "DELETE FROM `users_active` WHERE `user_id` = ?";
			$db->query($query, array($userID));

			
			// eventually, we will load from save-state table, if has saved session. probably removing the save-state session.

			// finally, add to active table.
			$query = "INSERT INTO `users_active` (`user_id`, `location_id`, `last_online`) VALUES (?, ?, now())";
			$db->query($query, array($userID, 1));
			// for now, we will initiate in location while i figure out what this project even is.

			return true;
		}

		public static function updateStatus($userID) {
			// updates last action timestamp
		}


		public static function userGetLocation($userID) {
			$db = Database::getInstance();

			$query = "SELECT `location_id` FROM `users_active` WHERE `user_id` = ?";
			$db->query($query, array($userID));

			return $db->firstResult()['location_id'];
		}

		public static function userSetLocation($userID, $locationID) {
			$db = Database::getInstance();

			$query = "UPDATE `users_active` SET `location_id` = ? WHERE `user_id` = ?";
			$db->query($query, array($locationID, $userID));

			return true;
		}
	}