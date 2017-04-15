<?php
	require_once('lib/database.class.php');

	class LocationManager {
		public static function getLocationInfo($locationID) {
			// returns name, description with location ID
			$db = Database::getInstance();
			$query = "SELECT `name`, `description` FROM `locations` WHERE `id`=?";
			$db->query($query, array($locationID));
			return ($db->firstResult());
		}
	}