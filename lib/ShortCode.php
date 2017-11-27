<?php

abstract class ShortCode {

	function __construct($plugin) {
		$this->plugin = $plugin;
		add_shortcode( 'table',  [$this, "execute"] );
	}

	protected abstract function execute($atts);
}