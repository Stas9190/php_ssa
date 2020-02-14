<?php

/**
 * Класс, реализующий функционал приложения на стороне сервера
 */

include_once("ssa/classes/render/render.php");
require_once("ssa/classes/db/db_connection.php");
include_once("models.php");
include_once("ssa/classes/moldmaker.php");
include_once("ssa/classes/ssa_files.php");
include_once("ssa/classes/query_builder/query_builder.php");

use Render\Render;
use DBConnection\Command;
use ssa_files\ssa_files;
use QueryBuilder\SqlQueryBuilder;
use QueryBuilder\TSqlQueryBuilder;

class App
{
    function __construct()
    {
    }

    public function loadStart()
    {
        echo 'testing builder patternr<br>';
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
