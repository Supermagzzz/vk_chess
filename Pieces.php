<?php
	require("Position.php");

	abstract class Cell {
		abstract public function getSymbol();

		public static function getCellBySymbol($symbol) {
			$color = ctype_upper($symbol) ? 1 : 0;
			$symbol = strtolower($symbol);
			switch ($symbol) {
				case '.':
					return new EmptyCell();
				case 'p':
					return new Pawn($color);
				case 'h':
					return new Horse($color);
				case 'b':
					return new Bishop($color);
				case 'r':
					return new Rook($color);
				case 'q':
					return new Queen($color);
				case 'k':
					return new King($color);
				default:
					break;
			}
		}
	}

	class EmptyCell extends Cell {
		public function getSymbol() {
			return '.';
		}
	}

	abstract class Piece extends Cell {
		protected $color;

		public function __construct($color) {
			$this->color = $color;
		}

		abstract public function getValidMoves($board, $pos);

		abstract protected function getLowerSymbol();

		public function getColor() {
			return $this->color;
		}

		public function getSymbol() {
			$symbol = $this->getLowerSymbol();
			if ($this->color) {
				return strtoupper($symbol);
			}
			return $symbol;
		}

		protected static function pushIf(&$arr, $pos, $foo, $board) {
			if ($board->$foo($pos)) {
				array_push($arr, $pos);
			}
		}

		protected static function pushManyIf(&$arr, $pos, $delta, $foo, $board) {
			$k = 1;
			do {
				$newPos = new Position($pos->getRow() + $delta->getRow() * $k, $pos->getCol() + $delta->getCol() * $k);
				self::pushIf($arr, $newPos, $foo, $board);
				$k++;
			} while ($board->free($newPos));
		}
	}

	class Pawn extends Piece {
		protected function getLowerSymbol() {
			return 'p';
		}

		public function getValidMoves($board, $pos) {
			$ans = array();
			$dr = $this->color ? 1 : -1;
			self::pushIf($ans, new Position($pos->getRow() + $dr, $pos->getCol()), 'free', $board);
			self::pushIf($ans, new Position($pos->getRow() + $dr, $pos->getCol() + 1), 'occupied', $board);
			self::pushIf($ans, new Position($pos->getRow() + $dr, $pos->getCol() - 1), 'occupied', $board);
			if (($this->color ? 1 : $board->getSize() - 2) == $pos->getRow()) {
				self::pushIf($ans, new Position($pos->getRow() + 2 * $dr, $pos->getCol()), 'free', $board);
			}
			return $ans;
		}
	}

	class Horse extends Piece {
		
		private static $deltas;

		public function __construct($color) {
			parent::__construct($color);
			if (!self::$deltas) {
				self::$deltas = [new Position(1, 2), new Position(1, -2), new Position(-1, 2), new Position(-1, -2),
								new Position(2, 1), new Position(2, -1), new Position(-2, 1), new Position(-2, -1)];
			}
		}

		protected function getLowerSymbol() {
			return 'h';
		}

		public function getValidMoves($board, $pos) {
			$ans = array();
			for ($i = 0; $i < count(self::$deltas); $i++) {
				self::pushIf($ans, new Position($pos->getRow() + self::$deltas[$i]->getRow(), $pos->getCol() + self::$deltas[$i]->getCol()),
					'freeOrOccupied', $board);
			}
			return $ans;
		}
	}

	class Bishop extends Piece {
		protected function getLowerSymbol() {
			return 'b';
		}

		public function getValidMoves($board, $pos) {
			$ans = array();
			for ($dx = -1; $dx <= 1; $dx += 2) {
				for ($dy = -1; $dy <= 1; $dy += 2) {
					self::pushManyIf($ans, $pos, new Position($dx, $dy), 'freeOrOccupied', $board);
				}
			}
			return $ans;
		}
	}

	class Rook extends Piece {
		protected function getLowerSymbol() {
			return 'r';
		}

		public function getValidMoves($board, $pos) {
			$ans = array();
			for ($d = -1; $d <= 1; $d += 2) {
				self::pushManyIf($ans, $pos, new Position($d, 0), 'freeOrOccupied', $board);
				self::pushManyIf($ans, $pos, new Position(0, $d), 'freeOrOccupied', $board);
			}
			return $ans;
		}
	}

	class Queen extends Piece {
		protected function getLowerSymbol() {
			return 'q';
		}

		public function getValidMoves($board, $pos) {
			$ans = array();
			for ($dx = -1; $dx <= 1; $dx++) {
				for ($dy = -1; $dy <= 1; $dy++) {
					self::pushManyIf($ans, $pos, new Position($dx, $dy), 'freeOrOccupied', $board);
				}
			}
			return $ans;
		}
	}

	class King extends Piece {
		protected function getLowerSymbol() {
			return 'k';
		}

		public function getValidMoves($board, $pos) {
			$ans = array();
			for ($dx = -1; $dx <= 1; $dx++) {
				for ($dy = -1; $dy <= 1; $dy++) {
					self::pushIf($ans, new Position($pos->getRow() + $dx, $pos->getCol() + $dy), 'freeOrOccupied', $board);
				}
			}
			return $ans;
		}
	}
