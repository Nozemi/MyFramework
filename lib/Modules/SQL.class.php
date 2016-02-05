<?php
	class SQL
	{		
		public static function Query($type, $query, $params= null, $dbdetails = null)
		{
			if($dbdetails == null) {
				$dbdetails = Utilities::FindKey('Database', $GLOBALS['Config']);
			}
			
			$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
			
			try {
				$db = new PDO('mysql:host=' . $dbdetails['DBHost'] . ';dbname=' . $dbdetails['DBName'] . ';charset=utf8', $dbdetails['DBUser'], $dbdetails['DBPass'], $options);
			} catch(PDOException $ex) {
				if(Utilities::FindKey('Debug', $GLOBALS['Config']) === true) {
					$message = 'Error message: ' . $ex->getMessage();
				} else {
					$message = 'Database connection error. Please report to administrator as soon as possible!';
				}
				
				if(!class_exists('MessageHandler')) {
					echo $message; exit;
				} else {
					new MessageHandler($message, 'ERR'); exit;
				}
			}
			
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			
			if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
				function undo_magic_quotes_gpc(&$array) {
					foreach($array as &$value) {
						if(is_array($value)) {
							undo_magic_quotes_gpc($value);
						} else {
							$value = stripslashes($value);
						}
					}
				}
				
				undo_magic_quotes_gpc($_POST);
				undo_magic_quotes_gpc($_GET);
				undo_magic_quotes_gpc($_COOKIE);
			}
			
			$result = self::runQuery($query, $params, $db);
			
			if($result['Success'] === true)
			{
				switch($type)
				{
					default:
					case 0:
						return true;
						break;
					case 1:
						return $result['Result']->fetch();
						break;
					case 2:
						return $result['Result']->fetchAll();
						break;
				}
			} else {
				return $result;
			}
		}
		
		private static function runQuery($query, $params = null, $db)
		{
			try {
				$stmt = $db->prepare($query);
				$result = $stmt->execute($params);
				
				return array(
					'Result' => $stmt,
					'Success' => true
				);
			} catch(PDOException $ex) {				
				if(Utilities::FindKey('Debug', $GLOBALS['Config']) === true) {
					$message = 'Error Message: ' . $ex->getMessage();
				} else {
					$message = 'Something went wrong while running the query. Please report to an administrator as soon as possible.';
				}
				
				return array(
					'Result' => $message,
					'Success' => false
				);
			}
		}
	}
?>