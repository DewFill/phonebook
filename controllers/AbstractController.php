<?php

namespace controllers;
abstract class AbstractController
{
//    abstract function run(string|null $action = null);
    function view(string $name, array $data)
    {
        $filepath = __BASE_PATH__ . "/views/" . $name . ".php";
        if (file_exists($filepath)) {
            require($filepath);
        }
    }

    function output(string $data): void
    {
        echo $data;
    }
}