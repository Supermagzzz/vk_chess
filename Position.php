<?php
	class Position {
		private $r;
		private $c;

		function __construct($r, $c) {
			$this->r = $r;
			$this->c = $c;
		}

		function getRow() {
			return $this->r;
		}

		function getCol() {
			return $this->c;
		}
	}
