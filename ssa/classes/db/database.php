<?php

/**
 * @author ShulgaSA
 * @version 1.0.0
 * @package database
 */

namespace database;

// Класс pdo
include_once("db_connection.php");
// Класс sqlsrv
include_once("SqlConnection.php");

use SqlConnection\SqlConnection;
use SqlConnection\Command as SqlCommand;
use DBConnection\DBConnection;
use DBConnection\Command;

/** 
 * Класс, предоставляющий унифицированный интерфейс для 
 * работы с классами db_connection и SqlConnection
 */
class DatabaseAdapter
{
    var $globalExtension;
    /**
     * Метод, инстанцирующий экземпляр целевого класса взаимодействия с бд
     * 
     * @param string $name      Наименование профиля настроек в SETTINGS
     * @param string $extension Указываем, что хотим использовать pdo, mssql или mysqli
     * 
     * @return object
     */
    public function Connect(string $name, string $extension = "")
    {
        $this->globalExtension = $extension;
        $obj = null;
        switch ($extension) {
            case "pdo":
                $obj = DBConnection::getInstance($GLOBALS['DATABASES'], $name);
                break;
            case "sqlsrv":
                $obj = new SqlConnection($GLOBALS['DATABASES'], $name);
                break;
            default:
                $obj = DBConnection::getInstance($GLOBALS['DATABASES'], $name);
                break;
        }

        $con = $obj->Connect();
        return $con;
    }

    /**
     * Метод, осуществляющий выборку из бд
     *
     * @param string $sql   Строка с запросом
     * @param array  @parms Массив с параметрами
     * @param string @types Строка с типами параметров 
     *
     * @return array 
     */
    public function Execute(string $query, array $parms, $con, string $types = ""): array
    {
        $context = [];
        switch ($this->globalExtension) {
            case "pdo":
                $context = $this->ExecDBQuery($query, $parms, $con);
                break;
            case "sqlsrv":
                $context = $this->LoadDataFromDB($query, $con);
                break;
            default:
                $context = $this->ExecDBQuery($query, $parms, $con);
                break;
        }
        return $context;
    }

    /**
     * Метод, реализующий запросы вставки, обновления и удаления записей из бд
     * 
     * @param string $sql    Строка с запросом
     * @param array  $params Массив с параметрами
     * @param string $types  Строка с типами параметров
     * 
     * @return array
     */
    public function ExecuteNonQuery(string $query, array $parms, $con, string $types = ""): array
    {
        $context = [];
        switch ($this->globalExtension) {
            case "pdo":
                $context = $this->PDODBInteraction($query, $parms, $con);
                break;
            case "sqlsrv":
                $context = $this->DBInteraction($query, $con, $parms);
                break;
            default:
                $context = $this->PDODBInteraction($query, $parms, $con);
                break;
        }
        return $context;
    }

    /**
     * Метод sqlsrv для выборки данных из таблиц
     * 
     * @param string $query Строка с запросом
     * @param object $con   Объект с подключением к бд
     */
    private function LoadDataFromDB($query, $con)
    {
        if ($query == '') return;
        $cmd = new SqlCommand($con, $query);
        return $cmd->Execute();
    }

    /**
     * Метод sqlsrv для вставки, обновления и удаления записей из бд
     * 
     * @param string $query Строка с запросом
     * @param object $con   Объект с подключением к бд
     * @param array  $parms Массив с параметрами
     */
    private function DBInteraction($query, $con, $parms = null)
    {
        if ($query == '') return;
        $cmd = new SqlCommand($con, $query, 'non_query', $parms);
        return $cmd->Execute();
    }

    /**
     * Метод pdo для выборки данных из таблиц
     * 
     * @param string $query Строка с запросом
     * @param array  $parms Массив с параметрами
     * @param object $con   Объект с подключением к бд
     */
    private function ExecDBQuery($query, $parms = [], $con)
    {
        if ($query == '') return;

        $cmd = new Command($con, $query, $parms);
        return $cmd->Execute();
    }

    /**
     * Метод pdo для вставки, обновления и удаления записей из бд
     * 
     * @param string $query Строка с запросом
     * @param array  $parms Массив с параметрами
     * @param object $con   Объект с подключением к бд
     */
    private function PDODBInteraction($query, $parms, $con)
    {
        if ($query == '') return;
        $cmd = new Command($con, $query, $parms);
        return $cmd->ExecuteNonQuery();
    }
}
