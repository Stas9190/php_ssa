<?php

namespace model_handler;

class model_handler
{
    public function Create($model, $values = [])
    {
        $qb = new query_builder('create');
        $qb->generate_sql_by_model($model);
        return $values;
    }

    public function Update()
    { }

    public function Delete()
    { }
}
class query_builder
{
    private $type = '';

    function __construct()
    {
        $this->type = func_get_arg(0);
    }

    public function generate_sql_by_model($model)
    { }
}
