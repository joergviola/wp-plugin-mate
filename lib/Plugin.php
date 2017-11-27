<?php

require('View.php');
require('PostType.php');
require('ShortCode.php');

abstract class Plugin {
	function __construct($base) {
		$this->base = $base;
		add_action( 'init', [$this, 'init']);
	}

	abstract function init();


	public function includeScripts($scripts, $admin=false) {
		add_action( $admin?'admin_enqueue_scripts':'wp_enqueue_scripts', function () use ($scripts) {
			foreach ($scripts as $script) {
				if (substr($script, -3)==='css') {
					wp_enqueue_style( basename($script), plugins_url($script, $this->base) );
				} else {
					wp_enqueue_script( basename($script), plugins_url( $script, $this->base ), array(), '1.0.0', true );
				}
			}
		});
	}
	public function includeAdminScripts($scripts) {
		$this->includeScripts($scripts, true);
	}

	public function registerScripts($scripts) {
		add_action( 'wp_enqueue_scripts', function () use ($scripts) {
			foreach ($scripts as $script) {
				if (substr($script, -3)==='css') {
					wp_register_style( basename($script), plugins_url($script, $this->base) );
				} else {
					wp_register_script( basename($script), plugins_url( $script, $this->base ), array(), '1.0.0', true );
				}
			}
		});
	}

	public function enqueueScripts($scripts) {
		foreach ($scripts as $script) {
			if (substr($script, -3)==='css') {
				wp_enqueue_style( basename($script), plugins_url($script, $this->base) );
			} else {
				wp_enqueue_script( basename($script), plugins_url( $script, $this->base ), array(), '1.0.0', true );
			}
		}
	}

}