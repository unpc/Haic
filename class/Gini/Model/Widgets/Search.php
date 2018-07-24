<?php

namespace Gini\Model\Widgets;

class Search extends \Gini\Model\Widget {

    function __construct($vars){
        parent::__construct('search', $vars);
    }

    static function reset_field($form=array()) {
        $reset_field = $form['reset_field'];
        $form_token = $form['form-token'];
        if($reset_field == 1){
            unset($_SESSION[$form_token]);
        }
        elseif($reset_field){
            $reset_field = explode(',', $form['reset_field']);

            foreach ($reset_field as $f) {
                unset($_SESSION[$form_token][$f]);
            }
        }
    }

    function add_buttons($buttons) {
        foreach((array)$buttons as $button) {
            $this->_vars['buttons'][] = $button;
        }
    }

    function add_fields($fields) {
        foreach((array)$fields as $key=>$field) {
            $this->_vars['fields'][$key] = $field;
        }
    }

    static function search_fields($fields, $form_token = NULL) {
        if(!$form_token) return;
        $search_field = [];

        foreach ((array)$fields as $key => $field) {
            //判断session中是否存在对应搜索的字段
            if($_SESSION[$form_token][$key]) {
                $search_field[$key] = $field;
                continue;
            }

            //filed可能包含多个字段，例如 ctime包含ctime-form，ctime-to
            if($field['field']) $connect_fields = explode(',', $field['field']);
            foreach ((array) $connect_fields as $f) {
                if($_SESSION[$form_token][$f]) {
                    $search_field[$key] = $field;
                    break;
                }
            } 
        }
        return $search_field;
    }

}
