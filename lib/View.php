<?php

class View {

	function __construct($base) {
		$this->base = $base;
	}

	public function render($viewname, $args) {
		foreach ($args as $_name=>$_value) {
			$$_name = $_value;
		}
		ob_start();
		require(dirname($this->base) . '/views/' . $viewname);
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
}