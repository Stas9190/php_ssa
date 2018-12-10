<?php
/*
Объявляем глобальные переменные, если они нужны
Последовательно подключаем файл настроек, файл рендеринга страниц,
файл для соединения с бд,
файл с функциями, файл маршрутизации
*/

if (isset($_POST) || isset($_GET) || isset($_FILES)) $input = array_merge($_GET, $_POST, $_FILES);

/** Основные файлы */
include ("settings.php");
include ("classes/render/render.php");
include ("classes/mysqli/MySqlConnection.php"); //если mysql
require_once("bdConnect.php");
include ("functions.php");
include ("routing.php");
?>