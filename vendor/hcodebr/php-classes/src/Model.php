<?php

namespace Hcode;

class Model{

	private $values = [];

	public function __call($name,$args){

		$method = substr($name, 0, 3);
		$nameField = substr($name, 3, strlen($name));

		switch($method){

			case "get":
				return $this->values[$nameField];
			break;

			case "set":
				$this->values[$nameField] = $args[0];
			break;
		}
	}

	public function setData($data = array()){

		foreach ($data as $key => $value) {
			$this->{"set".$key}($value);
		}
	}

	public function getData(){

		return $this->values;
	}
}

?>