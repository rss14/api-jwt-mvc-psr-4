<?php
namespace Models;

use \Core\Model;



class History extends Model {



	public function new_action($id_user, $action) {
		$data = Date("Y-m-d");
		$hora = Date("H:i:s");

		$sql = "INSERT INTO history SET id_user=:id_user, action=:action, data=:data, hora=:hora";
		$sql = $this->db->prepare($sql);
		$sql->bindValue('id_user', $id_user);
		$sql->bindValue('action', $action);	
		$sql->bindValue('data', $data);	
		$sql->bindValue('hora', $hora);	
		$sql->execute();		
	}

	public function get_history($id_user){

		$array = array();

		$sql = "SELECT * FROM history WHERE id_user=:id_user";
		$sql = $this->db->prepare($sql);
		$sql->bindValue('id_user', $id_user);
		$sql->execute();

		if($sql->rowCount() > 0){

			$array = $sql->fetchAll(\PDO::FETCH_ASSOC);
		}


		return $array;

	}

	

}


















