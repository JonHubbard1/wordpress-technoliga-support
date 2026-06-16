<?php

namespace Technoliga_Support;

class Main {

	private static $instance = null;

	private function __construct() {
		$this->init();
	}

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function init(): void {
		Admin_Menu::register();
	}
}
