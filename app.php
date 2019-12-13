<?php

/**
 * Класс, реализующий функционал приложения на стороне сервера
 */

include_once("ssa/classes/render/render.php");
require_once("ssa/classes/db/db_connection.php");
include_once("models.php");
include_once("ssa/classes/moldmaker.php");
include_once("ssa/classes/ssa_files.php");

use Render\Render;
use DBConnection\Command;
use ssa_files\ssa_files;

class App
{
    function __construct()
    { }
}
