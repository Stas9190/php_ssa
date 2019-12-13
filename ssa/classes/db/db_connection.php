<?php

/**
 * @author ShulgaSA
 * @version 1.0
 * @package DBConnection
 */

namespace DBConnection;

use PDOException;

/** 
 * Класс подключения к бд 
 */
class DBConnection
{
    /**
     * Параметры подключения к бд 
     * @var array
     */
    var $DATABASES;

    function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();

        switch ($i) {
            case 1:
                $this->DATABASES = $a[0];
                $this->DATABASE_PROFILE_NAME = "DEFAULT";
                break;
            case 2:
                $this->DATABASES = $a[0];
                $this->DATABASE_PROFILE_NAME = $a[1];
                break;
        }
    }

    /**
     * Метод реализующий подключение к базе
     * 
     * @return array
     */

    function Connect()
    {
        /**
         * Имя базы данных
         * @var string
         */
        $Database = $this->DATABASES[$this->DATABASE_PROFILE_NAME]["NAME"];
        /**
         * Имя хоста базы данных
         * @var string
         */
        $serverName = $this->DATABASES[$this->DATABASE_PROFILE_NAME]["HOST"];
        /**
         * Кодировка
         * @var string
         */
        $CharacterSet = $this->DATABASES[$this->DATABASE_PROFILE_NAME]["CHARSET"];
        /**
         * Имя пользователя бд
         * @var string
         */
        $UID = $this->DATABASES[$this->DATABASE_PROFILE_NAME]["USER"];
        /**
         * Пароль пользователя бд
         * @var string
         */
        $PWD = $this->DATABASES[$this->DATABASE_PROFILE_NAME]["PASSWORD"];
        /**
         * Драйвер, использующийся для подключения к бд
         * @var string
         */
        $DRIVER = isset($this->DATABASES[$this->DATABASE_PROFILE_NAME]['DRIVER']) ? $this->DATABASES[$this->DATABASE_PROFILE_NAME]['DRIVER'] : "mysql";
        $result = array();
        $Log = new Log();

        if (count($this->getDrivers()) > 0) {

            try {
                if (!in_array($DRIVER, $this->getDrivers(), TRUE)) {
                    $Log->Add("Драйвер " . $DRIVER . " не установлен");
                    $result["status"] = 0;
                }
            } catch (PDOException $ex) {
                $Log->Add("Ошибка подключения к базе данных: <br> {$ex->getMessage()}");
                $result["status"] = 0;
            }

            $settings = array();

            $result['driver'] = $DRIVER;
            if ($DRIVER == 'mysql') {
                $dsn = "{$DRIVER}:dbname={$Database};host={$serverName}";
                $settings = array(\PDO::MYSQL_ATTR_INIT_COMMAND => $CharacterSet, \PDO::FETCH_ASSOC, \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);
            }
            if ($DRIVER == "odbc") {
                $dsn = "{$DRIVER}:Driver={SQL Server};Server={$serverName};Database={$Database}";
                $settings = array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);
            }
            if ($DRIVER == 'dblib') {
                $dsn = "{$DRIVER}:Driver={$DRIVER};host={$serverName};Database={$Database}";
            }

            if (isset($dsn)) {
                try {
                    $dbh = new \PDO(
                        $dsn,
                        $UID,
                        $PWD,
                        $settings
                    );
                    $result["status"] = 1;
                    $Log->Add("Соединение успешно установлено");
                    $result['connection'] = $dbh;
                } catch (PDOException $e) {
                    $Log->Add("Подключение не удалось: '. {$e->getMessage()}");
                    $result["status"] = 0;
                }
            }
        } else {
            $Log->Add("PDO не поддерживает ни одного драйвера");
            $result['status'] = 0;
        }

        $result['log'] = $Log->Get();

        return $result;
    }

    /**
     * Метод получает список доступных драйверов бд на сервере
     * 
     * @return array
     */

    function getDrivers()
    {
        return \PDO::getAvailableDrivers();
    }
}

/**
 * Класс реализующий исполнение запросов к бд
 */
class Command
{
    /**
     * Объект подключенияя к бд
     * @var object
     */
    var $conn;
    /**
     * Текст запроса
     * @var string
     */
    var $sql;
    /**
     * Параметры, передаваемые в запрос
     * @var array
     */
    var $params;

    function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();

        switch ($i) {
            case 2:
                $this->conn = $a[0];
                $this->sql = $a[1];
                $this->params = null;
                break;
            case 3:
                $this->conn = $a[0];
                $this->sql = $a[1];
                //массив
                $this->params = $a[2];
                break;
        }
    }

    /**
     * Метод выполняющий получение выборки из таблиц бд
     * 
     * @return array
     */
    function Execute()
    {
        $result = array('status' => 0); //результирующая выборка
        $parms = $this->params != null ? $this->params : null;
        $Log = new Log();

        $connection = $this->conn['connection'];
        if ($this->conn['status'] != 1) {
            $result['status'] = 0;
            return $result;
        }

        try {
            $stmt = $connection->prepare($this->sql);
            if ($stmt->execute($parms)) {
                $res = array();
                $i = 0;
                $result['status'] = 1;
                $Log->Add("Запрос успешно выполнен");
                while ($row = $stmt->fetch()) {
                    if ($this->conn['driver'] == 'odbc')
                        $row = array_map(array($this, 'changeEncodingArrayElementsTo1251'), $row);
                    $res[$i] = $row;
                    $i++;
                }
                $result['data'] = $res;
                $result['log'] = $Log;
            }

            $stmt = null;
            $connection = null;
        } catch (PDOException $e) {
            $Log->Add($e->getMessage());
            $result['log'] = $Log;
        }

        return $result;
    }

    /**
     * Изменение кодировки строки ('Windows-1251 => UTF-8)
     * @param string входная строка
     * 
     * @return string
     */

    function changeEncodingArrayElementsTo1251($str)
    {
        return iconv('Windows-1251', 'UTF-8', $str);
    }

    /**
     * Изменение кодировки строки ('UTF-8 => Windows-1251)
     * @param string входная строка
     * 
     * @return string
     */

    function changeEncodingArrayElementsToUTF($str)
    {
        return iconv('UTF-8', 'Windows-1251', $str);
    }

    /**
     * Метод выполняющий обновление, вставку, удалени информации в бд
     * 
     * @return array
     */
    function ExecuteNonQuery()
    {
        $result = array('status' => 0);
        $parms = $this->params != null ? $this->params : null;
        $Log = new Log();

        if ($this->conn['driver'] == 'odbc')
            $parms = array_map(array($this, 'changeEncodingArrayElementsToUTF'), $parms);

        $connection = $this->conn['connection'];

        $result['status'] = $this->conn['status'] == 1 ? 1 : 0;
        if ($result['status'] == 0) return $result;

        try {
            $stmt = $connection->prepare($this->sql);

            if ($stmt->execute($parms))
                $result['status'] = 1;

            $stmt = null;
            $connection = null;
        } catch (PDOException $e) {
            $Log->Add($e->getMessage());
        }
        $result['log'] = $Log->Get();
        return $result;
    }

    /**
     * Метод выполняющий хранимые процедуры sql-сервера
     * 
     * @return array
     */

    function Call()
    {
        $result = array('status' => 0);
        $parms = $this->params != null ? $this->params : null;
        $Log = new Log();

        if ($this->conn['driver'] == 'odbc' && count($parms) > 0)
            $parms = array_map(array($this, 'changeEncodingArrayElementsToUTF'), $parms);

        $result['status'] = $this->conn['status'] == 1 ? 1 : 0;
        if ($result['status'] == 0) return $result;

        $connection = $this->conn['connection'];

        try {
            $stmt = $connection->prepare($this->sql);
            $trxType = "G";
            $whse = "2125";
            $item = "601301561111000";
            $loc = "125-1";
            $lot = "J000025065_1607";

            $stmt->bindParam(':TrxType', $trxType, \PDO::PARAM_STR);
            $stmt->bindParam(':Whse', $whse, \PDO::PARAM_STR);
            $stmt->bindParam(':Item', $item, \PDO::PARAM_STR);
            $stmt->bindParam(':Loc', $loc, \PDO::PARAM_STR);
            $stmt->bindParam(':Lot', $lot, \PDO::PARAM_STR);

            if ($stmt->execute())
                $result['status'] = 1;

            // $out = '';
            // $stmt->bindColumn(1, $out, \PDO::PARAM_STR);
            // $stmt->fetch(\PDO::FETCH_BOUND);

            // $result['out'] = $out;

            $stmt = null;
            $connection = null;
        } catch (PDOException $e) {
            $Log->Add($e->getMessage());
        }
        if ($this->conn['driver'] == 'odbc')
            $result['log'] = array_map(array($this, 'changeEncodingArrayElementsTo1251'), $Log->Get());

        return $result;
    }
}

/**
 * Класс логирования
 */
class Log
{
    /**
     * Массив содержащий логи
     * @var array
     */
    var $log = array();

    /**
     * Метод добавляет сообщение в лог
     * @param string @message текст лога
     * @return void
     */

    function Add($message)
    {
        array_push($this->log, $message);
    }

    /**
     * Метод возвращает лог
     * 
     * @return array
     */

    function Get()
    {
        return $this->log;
    }

    /**
     * Метод очищает лог
     * 
     * @return void
     */
    function Erase()
    {
        $this->log = array();
    }

    /**
     * Метод получает число строк лога
     * 
     * @return int
     */

    function Count()
    {
        return count($this->log);
    }
}
