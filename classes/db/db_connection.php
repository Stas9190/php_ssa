<?php

/** Подключения к бд */
class DBConnection
{
    //Параметры подключения к бд
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

    function Connect()
    {
        $Database = $this->DATABASES[$this->DATABASE_PROFILE_NAME]["NAME"];
        $serverName = $this->DATABASES[$this->DATABASE_PROFILE_NAME]["HOST"];
        $CharacterSet = $this->DATABASES[$this->DATABASE_PROFILE_NAME]["CHARSET"];
        $UID = $this->DATABASES[$this->DATABASE_PROFILE_NAME]["USER"];
        $PWD = $this->DATABASES[$this->DATABASE_PROFILE_NAME]["PASSWORD"];
        $DRIVER = isset($this->DATABASES[$this->DATABASE_PROFILE_NAME]['DRIVER']) ? $this->DATABASES[$this->DATABASE_PROFILE_NAME]['DRIVER'] : "mysql";
        $result = array();
        $Log = new Log();

        if (!empty($this->getDrivers())) {

            try {
                if (!in_array($DRIVER, $this->getDrivers(), TRUE)) {
                    throw new PDOException("Драйвер " . $DRIVER . " не установлен");
                }
            } catch (PDOException $ex) {
                $Log->Add("Ошибка подключения к базе данных: <br> {$ex->getMessage()}");
                $result["status"] = 0;
            }

            if ($DRIVER == 'mysql')
                $dsn = $DRIVER . ':dbname=' . $Database . ';host=' . $serverName;

            if (isset($dsn)) {
                try {
                    $dbh = new PDO(
                        $dsn,
                        $UID,
                        $PWD,
                        array(PDO::MYSQL_ATTR_INIT_COMMAND => $CharacterSet, PDO::FETCH_ASSOC)
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

    //Получаем доступные драйверы
    function getDrivers()
    {
        return PDO::getAvailableDrivers();
    }
}

class Command
{
    var $conn; //connection
    var $sql; //Запрос или массив запросов
    var $params; //параметры

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

    //select
    function Execute()
    {
        $result = array('status' => 0); //результирующая выборка
        $parms = $this->params != null ? $this->params : null;
        $Log = new Log();

        try {
            $stmt = $this->conn->prepare($this->sql);
            if ($stmt->execute($parms)) {
                $res = array();
                $i = 0;
                $result['status'] = 1;
                while ($row = $stmt->fetch()) {
                    $res[$i] = $row;
                    $i++;
                }
                $result['data'] = $res;
            }

            $stmt = null;
            $this->conn = null;
        } catch (PDOException $e) {
            $Log->Add($e->getMessage());
        }

        return $result;
    }

    //insert update delete
    function ExecuteNonQuery()
    {
        $result = array('status' => 0);
        $parms = $this->params != null ? $this->params : null;
        $Log = new Log();

        try {
            $stmt = $this->conn->prepare($this->sql);

            if ($stmt->execute($parms))
                $result['status'] = 1;

            $stmt = null;
            $this->conn = null;
        } catch (PDOException $e) {
            $Log->Add($e->getMessage());
        }
        $result['log'] = $Log->Get();
        return $result;
    }
}

class Log
{

    var $log = array();

    function Add($message)
    {
        array_push($this->log, $message);
    }

    function Get()
    {
        return $this->log;
    }

    function Erase()
    {
        $this->log = array();
    }

    function Count()
    {
        return count($this->log);
    }
}
