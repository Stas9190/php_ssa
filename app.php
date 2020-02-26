<?php

/**
 * Класс, реализующий функционал приложения на стороне сервера
 */

include_once("ssa/classes/render/render.php");
require_once("ssa/classes/db/db_connection.php");
include_once("models.php");

include_once("ssa/classes/ssa_files.php");
include_once("ssa/classes/model_handler/sql_generator/query_builder.php");

use Render\Render;
use DBConnection\Command;
use ssa_files\ssa_files;

use QueryBuilder\SqlQueryBuilder;
use QueryBuilder\TSqlQueryBuilder;

class App
{
    private $human = null;

    function __construct()
    {
    }

    public function def_function()
    {
        $this->loadStart();
    }

    public function loadStart()
    {
        $this->makeQuery(new TSqlQueryBuilder);
    }

    private function makeQuery(SqlQueryBuilder $queryBuilder)
    {
        $query = $queryBuilder
            ->select('users', ['name', 'email', 'password'])
            ->limit(10)
            ->where('age', 18, '>')
            ->order(['id'])
            ->getSql();

        echo $query;
    }
}
