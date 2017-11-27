<?php

class View {

	public static function render($viewname, $args) {
		foreach ($args as $_name=>$_value) {
			$$_name = $_value;
		}
		ob_start();
		require(dirname(__FILE__).'/../views/'.$viewname);
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
}