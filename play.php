<?php
    require('Board.php');

    function checkArgument($name) {
        if (!isset($_GET[$name])) {
            echo json_encode(['status' => 'Fail', 'message' => $name." argument absent"]);
            exit();
        }
    }
    try {
        $method = Database::escape($_GET['method']);
        $id = Database::escape($_GET['id']);
        checkArgument('method');
        checkArgument('id');
        $state = Database::getState($id);
        if ($state == null) {
            echo json_encode(["status" => "Fail", "message" => "invalid game ID"]);
            exit();
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
            echo json_encode(['status' => 'OK', 'board' => $state['state'],
                'turn' => ($state['turn'] ? "Black" : "White"), 'gameState' => $gameState]);
        } else if ($method == "makeTurn") {
            $start = Database::escape($_GET['start']);
            $end = Database::escape($_GET['end']);
            checkArgument('start');
            checkArgument('end');
            if (strlen($start) != 2 || strlen($end) != 2) {
                echo json_encode(['status' => 'Fail', 'message' => 'Invalid start or end']);
                exit();
            }
            $pos1 = new Position(Board::getSize() - (ord($start[1]) - ord('0')) - 1, ord($start[0]) - ord('A'));
            $pos2 = new Position(Board::getSize() - (ord($end[1]) - ord('0')) - 1, ord($end[0]) - ord('A'));
            if ($board->makeTurn($pos1, $pos2)) {
                echo json_encode(['status' => 'OK']);
            } else {
                echo json_encode(['status' => 'Fail', 'message' => 'Invalid turn']);
            }
        } else {
            echo json_encode(['status' => 'Fail', 'message' => 'Invalid method']);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "Fail", "message" => $e->getMessage()]);
    }