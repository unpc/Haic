<?php

namespace Gini\ORM;

class Hub extends Object
{
	public $key		= 'string:150';
	public $val 	= 'string:*';

	protected static $db_index = [
		'unique:key'
	];

}