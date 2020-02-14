<?php

/**
 * @author ShulgaSA
 * @version 1.0.0
 * @package QueryBuilder
 */

namespace QueryBuilder;

/**
 * Конструктор запросов к бд
 */

interface SqlQueryBuilder
{
    public function select(string $table, array $fields): SqlQueryBuilder;
    public function where(string $field, string $value, string $operator = '='): SqlQueryBuilder;
    public function limit(int $limit): SqlQueryBuilder;
    public function order(array $fields, string $direction = 'acs'): SqlQueryBuilder;

    public function getSql(): string;
}

/**
 * Построитель sql-запросов на Transact SQL
 */
class TSqlQueryBuilder implements SqlQueryBuilder
{
    protected $query;

    protected function reset(): void
    {
        $this->query = new \stdClass;
    }

    /**
     * Построение SELECT
     */
    public function select(string $table, array $fields): SqlQueryBuilder
    {
        $this->reset();
        $this->query->base = implode(", ", $fields) . " FROM " . $table;
        $this->query->type = 'select';

        return $this;
    }

    /**
     * Условие Where
     */
    public function where(string $field, string $value, string $operator = '='): \QueryBuilder\SqlQueryBuilder
    {
        if (!in_array($this->query->type, ['select', 'update', 'delete'])) {
            throw new \Exception('WHERE может использоваться только с запросами типа SELCT, UPDATE и DELETE');
        }
        $this->query->where[] = "{$field} {$operator} '{$value}'";

        return $this;
    }

    /**
     * Добавление ограничения TOP
     */
    public function limit(int $limit): \QueryBuilder\SqlQueryBuilder
    {
        if (!in_array($this->query->type, ['select'])) {
            throw new \Exception("TOP может использоваться только в запросах SELECT");
        }
        $this->query->limit = " TOP {$limit} ";

        return $this;
    }

    /**
     * Сортировка
     */
    public function order(array $fields, string $direction = 'asc'): \QueryBuilder\SqlQueryBuilder
    {
        if (!in_array($this->query->type, ['select'])) {
            throw new \Exception("ORDER может использоваться только в запросах SELECT");
        }
        $this->query->order = " ORDER BY " . implode(", ", $fields) . " {$direction}";

        return $this;
    }

    /**
     * Получить окончательный запрос
     */
    public function getSql(): string
    {
        $query = $this->query;
        $sql = "SELECT ";
        if (!empty($query->limit)) {
            $sql .= $query->limit;
        }
        $sql .= $query->base;
        if (!empty($query->where)) {
            $sql .= " WHERE " . implode(' AND ', $query->where);
        }
        if (!empty($query->order)) {
            $sql .= $query->order;
        }
        return $sql;
    }
}
