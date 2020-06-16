<?php
require('Database.php');
require('JSON.php');

try {
    jsonOK(["id" => Database::createGame()]);
} catch (Exception $e) {
	jsonFail($e->getMessage());
}