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

use SqlConnection;
use DBConnection\DBConnection;

/** 
 * Класс, предоставляющий унифицированный интерфейс для 
 * работы с классами db_connection и SqlConnection
 */
class DatabaseAdapter
{
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
        $obj = null;
        switch ($extension) {
            case "pdo":
                $obj = DBConnection::getInstance($GLOBALS['DATABASES'], $name);
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
    public function Execute(string $sql, array $parms, string $types = ""): array
    {
        return [];
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
    public function ExecuteNonQuery(string $sql, array $parms, string $types = ""): array
    {
        return [];
    }
}
