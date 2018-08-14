<?php

namespace Gini\Model;

class Widget extends \Gini\View
{
    public function __construct($name, $vars=null)
    {
        $name = 'widgets/'.$name;
        parent::__construct($name, $vars);
    }
    public static function factory($name, $vars=null)
    {
        $basename = basename($name);
        if ($basename == $name) {
            $class_name = '\Gini\Model\Widgets\\'.$name;
            if (class_exists($class_name)) {
                return \Gini\IoC::construct($class_name, $vars);
            }
        }
        return \Gini\IoC::construct('\Gini\Model\Widget', $name, $vars);
    }
}
