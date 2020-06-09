<?php 
	require("Pieces.php");
	require("Database.php");

	class Board {
		
		private static $SIZE = 8;
		private $id;
		private $state;
		private $turn;

		public function __construct($id, $state, $turn) {
			$this->id = $id;
			$this->turn = $turn;
			$this->state = array();
			$pointer = 0;
			for ($i = 0; $i < self::$SIZE; $i++) {
				$this->state[$i] = array();
				for ($j = 0; $j < self::$SIZE; $j++) {
					$this->state[$i][$j] = Cell::getCellBySymbol($state[$pointer]);
					$pointer++;
				}
			}
		}

		public static function valid($pos) {
			return $pos instanceof Position &&
                $pos->getRow() >= 0 && $pos->getCol() >= 0 && $pos->getRow() < self::$SIZE && $pos->getCol() < self::$SIZE;
		}

		public function get($pos) {
			if (self::valid($pos)) {
				return $this->state[$pos->getRow()][$pos->getCol()];
			}
			return null;
		}

		private function set($pos, $item) {
			if (self::valid($pos)) {
				$this->state[$pos->getRow()][$pos->getCol()] = $item;
			}
		}

		public function free($pos) {
			return self::valid($pos) && $this->get($pos) instanceof EmptyCell;
		}

		public function occupied($pos) {
			return self::valid($pos) && !$this->free($pos) && $this->get($pos)->getColor() != $this->turn;
		}

		public function freeOrOccupied($pos) {
			return $this->free($pos) || $this->occupied($pos);
		}

		public function myPiece($pos) {
			return self::valid($pos) && !$this->freeOrOccupied($pos);
		}

		private function getValidMoves($pos) {
			return $this->get($pos)->getValidMoves($this, $pos);
		}

		public function makeTurn($pos1, $pos2) {
			if ($this->myPiece($pos1)) {
				$validMoves = $this->getValidMoves($pos1);
				if ($validMoves != null && in_array($pos2, $validMoves)) {
					if ($this->notCheckByTurn($pos1, $pos2)) {
						$this->swap($pos1, $pos2);
						$this->changeTurn();
						return true;
					}
				}
			}
			return false;
		}

		public static function getSize() {
			return self::$SIZE;
		}

		public function toString() {
			$str = "";
			for ($i = 0; $i < self::$SIZE; $i++) {
				for ($j = 0; $j < self::$SIZE; $j++) {
					$str .= $this->state[$i][$j]->getSymbol();
				}
			}
			return $str;
		}

		public function isCheck() {
			$kingPos = $this->getKingPosition();
			$this->turn = $this->turn ? 0 : 1;
			for ($i = 0; $i < self::$SIZE; $i++) {
				for ($j = 0; $j < self::$SIZE; $j++) {
					$pos = new Position($i, $j);
					if ($this->myPiece($pos) && in_array($kingPos, $this->getValidMoves($pos))) {
						$this->turn = $this->turn ? 0 : 1;
						return true;
					}
				}
			}
			$this->turn = $this->turn ? 0 : 1;
			return false;
		}

		public function isMate() {
			for ($i = 0; $i < self::$SIZE; $i++) {
				for ($j = 0; $j < self::$SIZE; $j++) {
					$pos = new Position($i, $j);
					if ($this->myPiece($pos)) {
						$validMoves = $this->getValidMoves($pos);
						foreach ($validMoves as $move) {
						 	if ($this->notCheckByTurn($pos, $move)) {
						 		return false;
						 	}
						}
					}
				}
			}
			return true;
		}

		private function getKingPosition() {
		    $kingPos = null;
			for ($i = 0; $i < self::$SIZE; $i++) {
				for ($j = 0; $j < self::$SIZE; $j++) {
					$pos = new Position($i, $j);
					if ($this->myPiece($pos) && $this->get($pos) instanceof King) {
						$kingPos = $pos;
					}
				}
			}
			return $kingPos;
		}

		private function changeTurn() {
			$this->turn = $this->turn ? 0 : 1;
			Database::updateTurn($this->id, $this->turn);
		}

		private function notCheckByTurn($pos1, $pos2) {
			$pos1Save = $this->get($pos1);
			$pos2Save = $this->get($pos2);
			$this->set($pos2, $this->get($pos1));
			$this->set($pos1, new EmptyCell());
			$ans = $this->isCheck();
			$this->set($pos1, $pos1Save);
			$this->set($pos2, $pos2Save);
			return !$ans;
		}

		private function swap($pos1, $pos2) {
			$this->set($pos2, $this->get($pos1));
			$this->set($pos1, new EmptyCell());
			Database::updateState($this->id, $this->toString());
			return true;
		}
	}

