<?php

namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;

Class User Extends Model{

	const SESSION = "User";

	public static function Login($login, $password){
		$sql = new Sql();

		$res = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
			":LOGIN"=>$login
		));

		if(count($res) === 0){
			throw new \Exception("Usuário inexistente ou inválido");
		}

		$data = $res[0];

		if(password_verify($password, $data["despassword"]) === true){
			
			$user = new User();

			$user->setData($data);

			$_SESSION[User::SESSION] = $user->getData();

			return $user;

		}else{
			throw new \Exception("Usuário inexistente ou inválido");
			
		}
	}

	public static function verifyLogin($inadmin = true){

		if(!isset($_SESSION[User::SESSION]) || !$_SESSION[User::SESSION] 
			|| !(int)$_SESSION[User::SESSION]["iduser"] || (bool)$_SESSION[User::SESSION] !== $inadmin)
		{
			header("Location: /admin/login");
			exit;
		}
	}

	public static function logout(){
		$_SESSION[User::SESSION] = NULL;
	}
}

?>