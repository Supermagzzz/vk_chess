<?php
require('Database.php');
try {
    echo json_encode(["status" => "OK", "id" => Database::createGame()]);
} catch (Exception $e) {
    echo json_encode(["status" => "Fail", "message" => $e->getMessage()]);
}