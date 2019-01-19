<?php

namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;
use Hcode\Mailer;

Class User Extends Model{

	const SESSION = "User";
	const ERROR = "UserError";
	const ERROR_REGISTER = "UserErrorRegister";
	const SECRET = 'senha';
	const SECRET_IV = '5678910111213441';
	const SUCCESS = 'UserSuccess';


	public static function getFromSession(){

		$user = new User();

		if(isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]["iduser"] > 0){

			$user->setData($_SESSION[User::SESSION]);


		}else{
			throw new \Exception("Error Processing Request");
			
		}

		return $user;
	}

	public static function checkLogin($inadmin = true){

		if(!isset($_SESSION[User::SESSION]) || !$_SESSION[User::SESSION] 
			|| !(int)$_SESSION[User::SESSION]["iduser"] > 0)		{
			
			return false;
		}else{
			if($inadmin === true && (bool)$_SESSION[User::SESSION]["inadmin"] === true){

				return true;
			}else if($inadmin === false){

				return true;
			}else{

				return false;
			}
		}
	}

	public static function Login($login, $password){
		
		$sql = new Sql();

		$res = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b ON a.idperson = b.idperson WHERE a.deslogin = :LOGIN", array(
			":LOGIN"=>$login
		));

		if(count($res) === 0){
			throw new \Exception("Usuário inexistente ou inválido");
		}

		$data = $res[0];

		$pass = $data["despassword"];

		//$passTrue = User::decrypt($pass);

		if($password ===  $pass){
			
			$user = new User();

			$user->setData($data);

			$_SESSION[User::SESSION] = $user->getData();

			return $user;

		}else{
			throw new \Exception("Usuário inexistente ou inválido");
			
		}
	}

	public static function verifyLogin($inadmin = true){

		if(!User::checkLogin($inadmin)){

			if($inadmin){
				header("Location: /admin/login");
				exit;
			}else{
				header("Location: /login");
				exit;
			}
		}
	}

	public static function logout(){
		$_SESSION[User::SESSION] = NULL;
	}

	public static function listAll(){

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
	}

	public function save(){

		$sql = new Sql();

		//$pass = User::crypt($this->getdespassword());

		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",
			array(
				":desperson"=>$this->getdesperson(),
				":deslogin"=>$this->getdeslogin(),
				":despassword"=>$this->getdespassword(),
				":desemail"=>$this->getdesemail(),
				":nrphone"=>$this->getnrphone(),
				":inadmin"=>$this->getinadmin()
		));

		$this->setData($results);
	}

	public function get($iduser){

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser",
			array(
			":iduser"=>$iduser
		));

		$this->setData($results[0]);

	}

	public function update(){
		$sql = new Sql();

		$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",
			array(
				":iduser"=>$this->getiduser(),
				":desperson"=>$this->getdesperson(),
				":deslogin"=>$this->getdeslogin(),
				"despassword"=>$this->getdespassword(),
				"desemail"=>$this->getdesemail(),
				"nrphone"=>$this->getnrphone(),
				"inadmin"=>$this->getinadmin()
		));

		$this->setData($results);
	}

	public function delete(){

		$sql = new Sql();

		$sql->query("CALL sp_users_delete(:iduser)", array(

			":iduser"=>$this->getiduser()
		));
	}

	public static function getForgot($email, $inadmin = true){

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :email;", array(
			":email"=>$email
		));

		if(count($results) === 0){

			throw new \Exception("Senha não recuperada!");
			
		}else{

			$data = $results[0];

			$res2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(

				":iduser"=>$data["iduser"],
				":desip"=>$_SERVER["REMOTE_ADDR"]
			));

			if(count($res2) === 0){

				throw new \Exception("Não foi possivel recuperar a senha!");
				
			}else{

				$dataRecovery = $res2[0];

				$code = $dataRecovery["idrecovery"];

				//$cod = User::crypt($code);

				if($inadmin === true){

					$link = "http://dev.com/admin/forgot/reset?code=$code";
				}else{

					$link = "http://dev.com/forgot/reset?code=$code";
				}

				$mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir a senha do E-commerce", "forgot", array(
					"name"=>$data["desperson"],
					"link"=>$link
				));

				$mailer->send();

				return $data;
			}
		}
	}

	public function validForgotDecrypt($idrecovery){

		//$idrecovery = User::decrypt($code);

		$sql = new Sql();

		$results = $sql->select("

				SELECT * FROM tb_userspasswordsrecoveries a
				INNER JOIN tb_users b USING(iduser)
				INNER JOIN tb_persons c USING(idperson)
				WHERE
					a.idrecovery = :idrecovery
					AND
					a.dtrecovery IS NULL
					AND
					DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();

			", array(
				"idrecovery"=>$idrecovery
			));

			if(count($results) === 0){

				throw new \Exception("Senha nao recuperada");
				
			}else{
				return $results[0];
			}
	}

	public static function setForgotUsed($idrecovery){

		$sql = new Sql();


		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(

				":idrecovery"=>$idrecovery
		));
	}

	public function setPassword($password){

		$sql = new Sql();

		//$pass = User::crypt($password);

		$sql->query("UPDATE tb_users SET despassword = :despassword WHERE iduser = :iduser", array(

			":despassword"=>$password,
			":iduser"=>$this->getiduser()
		));

	}

	public static function setMsgError($msg){

		$_SESSION[User::ERROR] = $msg;
	}

	public static function getMsgError(){

		$msg =  (isset($_SESSION[User::ERROR]) && $_SESSION[User::ERROR])?$_SESSION[User::ERROR]:"";
		
		User::clearMsgError();

		return $msg;
	}

	public static function clearMsgError(){

		$_SESSION[User::ERROR] = NULL;
	}

	public static function setRegisterError($msg){

		$_SESSION[User::ERROR_REGISTER] = $msg;
	}

	public static function getRegisterError(){

		$msg =  (isset($_SESSION[User::ERROR_REGISTER]) && $_SESSION[User::ERROR_REGISTER])?$_SESSION[User::ERROR_REGISTER]:"";
		
		User::clearRegisterError();

		return $msg;
	}

	public static function clearRegisterError(){

		$_SESSION[User::ERROR_REGISTER] = NULL;
	}

	public static function emailExists($email){

		$sql = new Sql();

		$res = $sql->select("SELECT * FROM tb_persons WHERE desemail = :desemail",[
			":desemail"=>$email
		]);

		return (count($res)>0);
	}

	public static function setMsgSuccess($msg){

		$_SESSION[User::SUCCESS] = $msg;
	}

	public static function getMsgSuccess(){

		$msg =  (isset($_SESSION[User::SUCCESS]) && $_SESSION[User::SUCCESS])?$_SESSION[User::SUCCESS]:"";
		
		User::clearMsgSuccess();

		return $msg;
	}

	public static function clearMsgSuccess(){

		$_SESSION[User::SUCCESS] = NULL;
	}
}

?>








