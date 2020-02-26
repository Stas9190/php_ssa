<?php

/**
 * @version 1.0
 * @package Models
 */

namespace Models;

use DBConnection\Command;
use DBConnection\DBConnection;

/**
 * Класс моделей
 */

class human
{
    public $fields = [
        'id' => ['type' => 'int', 'pk' => true, 'ai' => true],
        'name' => ['type' => 'string', 'length' => 255, 'blank' => false],
        'fam' => ['type' => 'string', 'length' => 255, 'blank' => false],
        'info' => ['type' => 'string', 'length' => 255, 'blank' => false]
    ];
}
