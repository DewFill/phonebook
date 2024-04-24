<?php

namespace controllers;

use exceptions\SchemaDoesNotMatch;
use exceptions\TableDoesNotExists;
use JsonDataSource;

class ApiController extends \controllers\AbstractController
{
    function actionDeleteContact($data): void
    {
//        var_dump($data);
        $id = intval($data["id"]);
        if ($id === 0) $this->viewError("Id is not an Integer");
        $db = JsonDataSource::getInstance(__JSON_PATH__);
        $isDeleted = $db->deleteOneBy("contacts", "id", $id);
        if ($isDeleted) $this->viewSuccess(["id" => $id]);
        else $this->viewError();
    }

    function actionAddContact($data)
    {

        if (empty($data["name"]) or empty($data["phone_number"])) {
            $this->viewError("POST data is not correct");
            return;
        }
        $phone_number = filter_var($data["phone_number"], FILTER_SANITIZE_NUMBER_INT);

        if (empty($phone_number)) {
            $this->viewError("Phone number is not valid!");
            return;
        }


        $db = JsonDataSource::getInstance(__JSON_PATH__);
        try {
            $db->insert("contacts", [null, $data["name"], $phone_number]);
            $this->viewSuccess();
        } catch (SchemaDoesNotMatch|TableDoesNotExists $e) {
            $this->viewError($e->getMessage());
        }
    }

    public function view(string $name, array $data)
    {
        header('Content-type: application/json');
    }

    function viewSuccess($data = null): void
    {
        header('Content-type: application/json');
        $this->output(json_encode([
            "action" => "success",
            "data" => $data
        ]));
    }

    function viewError($data = null): void
    {
        header('Content-type: application/json');
        $this->output(json_encode([
            "action" => "error",
            "data" => $data
        ]));
    }
}