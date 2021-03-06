<?php

namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;

class Order extends Model{

	const SUCCESS = "Order-Success";
	const ERROR = "Order-Error";

	public function save(){

		$sql = new Sql();

		$res = $sql->select("CALL sp_orders_save(:idorder, :idcart, :iduser, :idstatus, :idaddress, :vltotal)",[

			":idorder"=>$this->getidorder(),
			":idcart"=>$this->getidcart(),
			":iduser"=>$this->getiduser(),
			":idstatus"=>$this->getidstatus(),
			":idaddress"=>$this->getidaddress(),
			":vltotal"=>$this->getvltotal()
		]);

		if(count($res)>0){

			$this->setData($res[0]);
		}
	}

	public function delete(){

		$sql = new Sql();

		$sql->query("DELETE FROM tb_orders WHERE idorder = :idorder",[
			"idorder"=>$this->getidorder()
		]);

	}

	public function get($idorder){

		$sql = new Sql();

		$res = $sql->select("SELECT * FROM tb_orders a 
			INNER JOIN tb_ordersstatus b USING(idstatus)
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING(idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.idperson
			WHERE a.idorder = :idorder", [

				":idorder"=>$idorder
			]);

		if(count($res)>0){

			$this->setData($res[0]);
		}
	}

	public static function listAll(){

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_orders a 
			INNER JOIN tb_ordersstatus b USING(idstatus)
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING(idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.idperson
			ORDER BY a.dtregister DESC");
	}


	public function getCart():Cart{

		$cart = new Cart();

		$cart->get((int)$this->getidcart());

		return $cart;
	}

	public static function setMsgSuccess($msg){

		$_SESSION[Order::SUCCESS] = $msg;
	}

	public static function getMsgSuccess(){

		$msg =  (isset($_SESSION[Order::SUCCESS]) && $_SESSION[Order::SUCCESS])?$_SESSION[Order::SUCCESS]:"";
		
		Order::clearMsgSuccess();

		return $msg;
	}

	public static function clearMsgSuccess(){

		$_SESSION[Order::SUCCESS] = NULL;
	}

	public static function setMsgError($msg){

		$_SESSION[Order::ERROR] = $msg;
	}

	public static function getMsgError(){

		$msg =  (isset($_SESSION[Order::ERROR]) && $_SESSION[Order::ERROR])?$_SESSION[Order::ERROR]:"";
		
		Order::clearMsgError();

		return $msg;
	}

	public static function clearMsgError(){

		$_SESSION[Order::ERROR] = NULL;
	}

	public function getPage($page = 1,$itemsPerPage=1){

		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();

		$res = $sql->select("

			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_orders a
			INNER JOIN tb_ordersstatus b USING(idstatus)
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING(idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.idperson
			ORDER BY a.dtregister DESC
			LIMIT $start, $itemsPerPage;
		");

		$resTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal");

		return [
			"data"=>$res,
			"total"=>(int)$resTotal[0]["nrtotal"],
			"pages"=>ceil($resTotal[0]["nrtotal"] / $itemsPerPage)
		];
	}

	public function getPageSearch($search,$page=1,$itemsPerPage=1){

		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();

		$res = $sql->select("

			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_orders a
			INNER JOIN tb_ordersstatus b USING(idstatus)
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING(idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.idperson
			WHERE a.idorder = :id OR f.desperson LIKE :search
			ORDER BY a.dtregister DESC
			LIMIT $start, $itemsPerPage;
		",[
			":search"=>"%".$search."%",
			":id"=>$search
		]);

		$resTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal");

		return [
			"data"=>$res,
			"total"=>(int)$resTotal[0]["nrtotal"],
			"pages"=>ceil($resTotal[0]["nrtotal"] / $itemsPerPage)
		];
	}
}



?>