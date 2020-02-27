<?php

/**
 * @author ShulgaSA
 * @version 1.1.0
 * @package DBConnection
 */

namespace DBConnection;

use Exception;
use PDOException;

/** Подключения к бд */
class DBConnection
{
    /**
     * Переменная с параметрами подключения к бд
     * @var array
     */
    var $DATABASES;
    /**
     * Имя профиля подключения к бд по умолчанию
     */
    var $DATABASE_PROFILE_NAME = "DEFAULT";

    /**
     * Статическое поле для хранения экземпляра объекта
     */
    private static $instances = [];

    /** Конструктор класса */
    protected function __construct($a, $i)
    {
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
     * Запрещаем клонирование и десериализацию 
     * */
    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new Exception("Запрещена десериализация");
    }

    /** 
     * Метод для получения экземпляра класса
     */
    public static function getInstance(): DBConnection
    {
        /**
         * Получаем аргументы ф-ции
         */
        $a = \func_get_args();
        /**
         * Получаем кол-во аргументов ф-ции
         */
        $i = \func_num_args();
        $dbc = static::class;
        /** 
         * Если экземпляр не существует, то создаем новый 
         */
        if (!isset(static::$instances[$dbc])) {
            static::$instances[$dbc] = new static($a, $i);
        }

        return (static::$instances[$dbc]);
    }

    /** 
     * Метод подключения к бд 
     * @return array
     */
    public function Connect()
    {
        /**
         * Наименование профиля
         * @var string
         */
        $Database = $this->DATABASES[$this->DATABASE_PROFILE_NAME]["NAME"];
        /**
         * Хост
         * @var string
         */
        $serverName = $this->DATABASES[$this->DATABASE_PROFILE_NAME]["HOST"];
        /**
         * Кодировка
         * @var string
         */
        $CharacterSet = $this->DATABASES[$this->DATABASE_PROFILE_NAME]["CHARSET"];
        /**
         * Имя пользователя
         * @var string
         */
        $UID = $this->DATABASES[$this->DATABASE_PROFILE_NAME]["USER"];
        /**
         * Пароль
         * @var string
         */
        $PWD = $this->DATABASES[$this->DATABASE_PROFILE_NAME]["PASSWORD"];
        /**
         * Драйвер
         * @var string
         */
        $DRIVER = isset($this->DATABASES[$this->DATABASE_PROFILE_NAME]['DRIVER']) ? $this->DATABASES[$this->DATABASE_PROFILE_NAME]['DRIVER'] : "mysql";
        /**
         * Массив, в котором возвращается результат выполнения метода
         * @var array
         */
        $result = array();
        /**
         * Создаем экземпляр класса для логирования
         * @var Log
         */
        $Log = new Log();

        /**
         *  Проверяем наличие какого-либо драйвера 
         */
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

            /**
             * Массив с настройками
             * @var  array
             */
            $settings = array();

            /**
             * В зависимости от указанного драйвера создаем строку подключения
             */
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
            if ($DRIVER == 'sqlsrv') {
                $dsn = "{$DRIVER}:Server={$serverName};Database={$Database}";
            }

            /**
             *  Создаем подключение 
             */
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

        /** 
         * Получаем лог 
         */
        $result['log'] = $Log->Get();

        /**
         * Возвращаем результат
         */
        return $result;
    }

    /**
     * Получаем доступные драйверы
     * 
     * @return array
     */
    function getDrivers()
    {
        return \PDO::getAvailableDrivers();
    }
}

class Command
{
    /**
     * Объект подключения
     * @var object
     */
    var $conn;
    /**
     * Строка с запросом
     * @var string
     */
    var $sql;
    /**
     * Массив с параметрами запроса
     * @var array
     */
    var $params;

    /**
     * Конструктор, в котором подключаем аргументы
     */
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
     * Метод, реализующий выполнение запросов выборки из бд
     * 
     * @return array
     */
    function Execute()
    {
        /**
         * Результирующая выборка
         * 
         * @var array
         */
        $result = array('status' => 0);
        /**
         * Параметры
         * 
         * @var array
         */
        $parms = $this->params != null ? $this->params : null;
        $Log = new Log();

        /** Проверяем существует ли подключение к бд */
        $connection = $this->conn['connection'];
        if ($this->conn['status'] != 1) {
            $result['status'] = 0;
            return $result;
        }

        /**
         * Подготовавливаем запрос, привязываем параметры, и исполняем
         */
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

        /**
         * Возвращаем результат
         */
        return $result;
    }

    /**
     *  Метод для изменения кодировки строки ('Windows-1251 => UTF-8')
     * @param string входная строка
     * 
     * @return string
     */

    function changeEncodingArrayElementsTo1251($str)
    {
        return iconv('Windows-1251', 'UTF-8', $str);
    }

    /**
     *  Метод для изменения кодировки строки ('UTF-8 => Windows-1251')
     * @param string $str входная строка
     * 
     * @return string
     */
    function changeEncodingArrayElementsToUTF($str)
    {
        return iconv('UTF-8', 'Windows-1251', $str);
    }

    /**
     * Метод для выполнения операций вставки, обновления и удаления данных из бд
     * 
     * @return array
     */
    function ExecuteNonQuery()
    {
        /**
         * Результат
         */
        $result = array('status' => 0);
        /**
         * Параметры
         */
        $parms = $this->params != null ? $this->params : null;
        $Log = new Log();

        /**
         *  Если драйвер odbc, то перекодируем 
         */
        if ($this->conn['driver'] == 'odbc')
            $parms = array_map(array($this, 'changeEncodingArrayElementsToUTF'), $parms);

        /** 
         * Получаем объект подключения
         */
        $connection = $this->conn['connection'];

        /**
         *  Статус 
         */
        $result['status'] = $this->conn['status'] == 1 ? 1 : 0;
        if ($result['status'] == 0) return $result;


        /**
         * Подготавливаем запрос и выполняем
         */
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
        /**
         * Возвращаем результат
         */
        return $result;
    }
}

/**
 * Класс для логирования
 */
class Log
{

    /**
     * Массив содержащий логи
     * @var array
     */
    var $log = array();

    /**
     * Добавляем сообщение В лог
     * @param string @message текст лога
     *
     *  @return void
     */
    function Add($message)
    {
        array_push($this->log, $message);
    }

    /**
     * Возвращаем лог
     * 
     * @return array
     */
    function Get()
    {
        return $this->log;
    }

    /**
     * Очищение лога
     * 
     * @return void
     */
    function Erase()
    {
        $this->log = array();
    }

    /**
     * Число строк
     * 
     * @return int
     */
    function Count()
    {
        return count($this->log);
    }
}
