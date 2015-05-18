<?php 

namespace albus\Core;

class Request {

	public function __construct() {
		
	}

	public function getBody() {
		return file_get_contents('php://input');
	}

	public function getParams() {
		return $_GET;
	}
}