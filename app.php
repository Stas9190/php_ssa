<?php

/**
 * Класс, реализующий функционал приложения на стороне сервера
 */

include_once("ssa/classes/render/render.php");
require_once("ssa/classes/db/db_connection.php");
include_once("models.php");
include_once("ssa/classes/model_handler.php");
// include_once("ssa/classes/moldmaker.php");
include_once("ssa/classes/ssa_files.php");

use Render\Render;
use DBConnection\Command;
use ssa_files\ssa_files;
use model_handler\model_handler;

class App
{
    private $human = null;

    function __construct()
    {
        $this->human = new Models\Human();
    }

    function def_function()
    {
        $mh = new model_handler($this->human);
        $mh->Create(['test', 'test2', 'test3']);
    }
}
