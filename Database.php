<?php
	class Database {
		private static $initialized = false;
		private static $mysqli;
		private static $addr = '127.0.0.1';
		private static $login = 'root';
		private static $pass = '';
		private static $db = 'vk_chess';
		private static $games = 'games';
		private static $initBoard = "RHBQKBHRPPPPPPPP................................pppppppprhbqkbhr";

		public static function initialize() {
			if (self::$initialized) {
	            return;
	        }
	        self::$initialized = true;
			self::$mysqli = new mysqli(self::$addr, self::$login, self::$pass, self::$db);
			self::checkConnection();
		}

		private static function query($str) {
			$ans = self::$mysqli->query($str);
			self::checkConnection();
			return $ans;
		}

		public static function createGame() {
			self::query("INSERT INTO ".self::$games." (state, turn) VALUES ('".self::$initBoard."', 0)");
			return self::$mysqli->insert_id;
		}

		public static function getState($id) {
			$result = self::query("SELECT state, turn FROM ".self::$games." WHERE id = ".$id);
            return $result->fetch_assoc();
		}

		public static function updateTurn($id, $turn) {
			self::query("UPDATE ".self::$games." SET turn = ".$turn." WHERE id = ".$id);
		}

		public static function updateState($id, $state) {
			self::query("UPDATE ".self::$games." SET state = '".$state."' WHERE id = ".$id);
		}

		private static function checkConnection() {
            if (self::$mysqli->errno) {
                throw new RuntimeException("Database connection error");
            }
        }

        public static function escape($str) {
        	return self::$mysqli->real_escape_string($str);
        }
	}

    try {
        Database::initialize();
    } catch (Exception $e) {
        echo json_encode(["status" => "Fail", "message" => $e->getMessage()]);
        exit();
    }