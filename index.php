<?php
/*
Объявляем глобальные переменные, если они нужны
Последовательно подключаем файл настроек, файл рендеринга страниц,
файл для соединения с бд,
файл с функциями, файл маршрутизации
*/
//Имя шаблона
$TEMPLATE_NAME = "";
//Соединение с бд
$CONN = null;
//Возможные сообщения
$MESSAGE = "";

//define('FPDF_FONTPATH','classes/fpdf/font/');

/** Основные файлы */
include ("settings.php");
include ("classes/render/render.php");
include ("classes/mssql/SqlConnection.php");
include ("functions.php");
include ("routing.php");
?>