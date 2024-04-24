<?php
const __BASE_PATH__ = __DIR__;
const __JSON_PATH__ = __DIR__ . "/db.json";

require "controllers/MainController.php";
require "controllers/ApiController.php";
require "JsonDataSource.php";
require "models/exceptions/SchemaDoesNotMatch.php";
require "models/exceptions/TableDoesNotExists.php";

use controllers\ApiController;
use controllers\MainController;

$db = JsonDataSource::getInstance(__JSON_PATH__);

//$db->insert("contacts", [null, "name", "test"]);
//$db->insert("contacts", [null, "name", "test"]);


if ($_SERVER["REQUEST_URI"] === "/" and $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new MainController();
    $controller->actionMain();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //handle APIs
    if ($_SERVER["REQUEST_URI"] === "/api/delete/contact") {
        $controller = new ApiController();
        $controller->actionDeleteContact($_POST);
    }

    if ($_SERVER["REQUEST_URI"] === "/api/add/contact") {
        $controller = new ApiController();
        $controller->actionAddContact($_POST);
    }
}

