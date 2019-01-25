<?php

namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;
use Hcode\Mailer;
use Hcode\Model\User;

Class Address Extends Model{

	const ADDRESS = "ErrorAddress";

	public static function getCep($cep){

		$nrcep = str_replace("-", "", $cep);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://viacep.com.br/ws/$nrcep/json/");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$data = json_decode(curl_exec($ch), true);

		curl_close($ch);

		return $data;
	}

	public function loadFromCEP($cep){

		$data = Address::getCep($cep);

		if(isset($data["logradouro"]) && $data["logradouro"]){

			$this->setdesaddress($data["logradouro"]);
			$this->setdescomplement($data["complemento"]);
			$this->setdesdistrict($data["bairro"]);
			$this->setdescity($data["localidade"]);
			$this->setdesstate($data["uf"]);
			$this->setdescountry("Brasil");
			$this->setdeszipcode($cep);

		}
	}

	public function save(){

		$sql = new Sql();

		$res = $sql->select("CALL sp_addresses_save(:idaddress, :idperson, :desaddress, :descomplement, :descity, :desstate, :descountry, :deszipcode, :desdistrict)",[

			":idaddress"=>$this->getidaddress(),
			":idperson"=>$this->getidperson(),
			":desaddress"=>utf8_decode($this->getdesaddress()),
			":descomplement"=>$this->getdescomplement(),
			":descity"=>utf8_decode($this->getdescity()),
			":desstate"=>utf8_decode($this->getdesstate()),
			":descountry"=>utf8_decode($this->getdescountry()),
			":deszipcode"=>$this->getdeszipcode(),
			":desdistrict"=>utf8_decode($this->getdesdistrict())
		]);

		if(count($res) > 0){

			$this->setData($res[0]);
		}
	}

	public static function setMsgError($msg){

		$_SESSION[Address::ADDRESS] = $msg;
	}

	public static function getMsgError(){

		$msg =  (isset($_SESSION[Address::ADDRESS]) && $_SESSION[Address::ADDRESS])?$_SESSION[Address::ADDRESS]:"";
		
		Address::clearMsgError();

		return $msg;
	}

	public static function clearMsgError(){

		$_SESSION[Address::ADDRESS] = NULL;
	}
}

?>

