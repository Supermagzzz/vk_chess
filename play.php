<?php
require('Board.php');
require('JSON.php');

function checkArgument($name)
{
    if (!isset($_GET[$name])) {
        jsonFail($name . " argument absent");
    }
}

try {
    $method = Database::escape($_GET['method']);
    $id = Database::escape($_GET['id']);
    checkArgument('method');
    checkArgument('id');
    $state = Database::getState($id);
    if ($state == null) {
        jsonFail("invalid game ID");
    }
    $board = new Board($id, $state['state'], $state['turn']);
    if ($method == "print") {
        if ($board->isMate()) {
            $gameState = "Mate";
        } else if ($board->isCheck()) {
            $gameState = "Check";
        } else {
            $gameState = "Normal";
        }
        jsonOK(['board' => $state['state'], 'turn' => ($state['turn'] ? "Black" : "White"), 'gameState' => $gameState]);
    } else if ($method == "makeTurn") {
        $start = Database::escape($_GET['start']);
        $end = Database::escape($_GET['end']);
        checkArgument('start');
        checkArgument('end');
        if (strlen($start) != 2 || strlen($end) != 2) {
            jsonFail("invalid start or end");
        }
        $pos1 = new Position(Board::getSize() - (ord($start[1]) - ord('0')) - 1, ord($start[0]) - ord('A'));
        $pos2 = new Position(Board::getSize() - (ord($end[1]) - ord('0')) - 1, ord($end[0]) - ord('A'));
        if ($board->makeTurn($pos1, $pos2)) {
            jsonOK();
        } else {
            jsonFail("Invalid turn");
        }
    } else {
        jsonFail("Invalid method");
    }
} catch (Exception $e) {
    jsonFail($e->getMessage());
}