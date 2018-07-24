<?php

namespace Gini\Model;

class Widget extends \Gini\View {

    function __construct($name, $vars=NULL) {
		$name = 'widgets/'.$name;
		parent::__construct($name, $vars);
	}
	static function factory($name, $vars=NULL) {

		$basename = basename($name);
		if($basename == $name) {

			$class_name = '\Gini\Model\Widgets\\'.$name;
			if (class_exists($class_name)) {
				return \Gini\IoC::construct($class_name, $vars);
			}
		}
		return \Gini\IoC::construct('\Gini\Model\Widget', $name, $vars);
	}
}
