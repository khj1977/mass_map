<?php

require_once("framework/Env.php");

// This base class actually do nothing at all since model is plain object and
// connection and cache objects are holded by instance of Env class.
class BaseModel {

	public function __construct() {
	}

}

?>