<?php
namespace controllers;
require __BASE_PATH__ . "/controllers/AbstractController.php";


class MainController extends \controllers\AbstractController
{
    function actionMain(): void
    {
        $db = \JsonDataSource::getInstance(__JSON_PATH__);
        $data = [];
        $data["columns"] = $db->getColumns("contacts");
        $data["contacts"] = $db->getAll("contacts");
        $this->view("main", $data);
    }
}