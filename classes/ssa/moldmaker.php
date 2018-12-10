<?php
/** Создание формы на основании класса из forms.php */

/** Формы */
include ("forms.php");

class MoldMaker
{
    /** класс формы */
    var $_class;
    var $_header;
    var $_model_name;

    function __construct()
    {
        $objects = func_get_args();
        $i = func_num_args();
        if ($i > 0)
        {
            $this -> _class = new $objects[0];
            $this -> _header = $objects[1];
            $this -> _model_name = $objects[2];
        }
    }

    /** Конструирование формы создания */
    function CreateView()
    {
        $form_presentation = $this -> _class -> form_presentation;

        $count = count($form_presentation);

        if ($count > 0)
        {
            $top = '<div class="row">' .
                        '<div class="col-md-6">' .
                            '<div class="box box-primary">' .
                                '<div class="box-header with-border">' .
                                    '<h3 class="box-title">' . $this->_header . '</h3>' .
                                '</div>' .
                                '<form action="" role="form" method="POST">';

            $middle = '<div class="box-body">';
            foreach ($form_presentation as $key => $val)
            {
                if ($val["type"] == "text")
                {
                    $middle .= '<div class="form-group">';
                    $middle .= '<label for='.$key.'>'.$val["label_text"].'</label>';
                    $middle .= '<input type="'.$val["type"].'" name="'.$key.'" class="form-control" id="'.$key.'" autofocus required>';
                    $middle .= '<input type="hidden" name="model_name" value='.$this -> _model_name.'>';
                    $middle .= '</div>';
                }
            }
            $middle .= '</div>';
            $middle .= '<div class="box-footer">';
            $middle .= '<button type="submit" class="btn btn-primary" name="AddPostData">Добавить</button>';
            $middle .= '</div>';

            $bottom = '</form>' .
                        '</div>' .
                        '</div>' .
                        '</div>';

            $content = $top . $middle . $bottom;
            
            $render = new Render($content, null, "html");
            $render -> renderPage();
        }
        else
            echo 'Не удается найти класс формы';
    }

     /** Конструирование формы редактирования */
     function EditView($context, $unique_key = 'id')
     {
        $form_presentation = $this -> _class -> form_presentation;

        $count = count($form_presentation);

        if ($count > 0)
        {
            $top = '<div class="row">' .
                        '<div class="col-md-6">' .
                            '<div class="box box-primary">' .
                                '<div class="box-header with-border">' .
                                    '<h3 class="box-title">' . $this->_header . '</h3>' .
                                '</div>' .
                                '<form action="" role="form" method="POST">';

            $middle = '<div class="box-body">';
            $middle .= '<input type="hidden" name="unique_id" value='.$context["data"][0][$unique_key].'>';
            foreach ($form_presentation as $key => $val)
            {
                if ($val["type"] == "text")
                {
                    $middle .= '<div class="form-group">';
                    $middle .= '<label for='.$key.'>'.$val["label_text"].'</label>';
                    $middle .= '<input type="'.$val["type"].'" name="'.$key.'" class="form-control" id="'.$key.'" value="'.$context["data"][0][$key].'" autofocus required>';
                    $middle .= '</div>';
                }
            }
            $middle .= '<input type="hidden" name="model_name" value='.$this -> _model_name.'>';
            $middle .= '</div>';
            $middle .= '<div class="box-footer">';
            $middle .= '<button type="submit" class="btn btn-primary" name="EditPostData">Обновить</button>';
            $middle .= '</div>';

            $bottom = '</form>' .
                        '</div>' .
                        '</div>' .
                        '</div>';

            $content = $top . $middle . $bottom;
            
            $render = new Render($content, null, "html");
            $render -> renderPage();
        }
        else
            echo 'Не удается найти класс формы';
     }
}
?>