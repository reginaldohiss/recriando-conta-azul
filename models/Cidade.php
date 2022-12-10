<?php
class Cidade extends model {

	public function getStates() {
		$array = array();

		$sql = "SELECT Uf FROM Cidade GROUP BY Uf";
		$sql = $this->db->query($sql);

		if($sql->rowCount() > 0) {
			$array = $sql->fetchAll();
		}

		return $array;
	}

	public function getCityList($state) {
		$array = array();

		$sql = "SELECT Nome, CodigoMunicipio FROM Cidade WHERE Uf = :uf";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(":uf", $state);
		$sql->execute();

		if($sql->rowCount() > 0) {
			$array = $sql->fetchAll();
		}

		return $array;
	}

	public function getCity($city_code) {
		$sql = "SELECT Nome FROM Cidade WHERE CodigoMunicipio = :codigo";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(":codigo", $city_code);
		$sql->execute();

		if($sql->rowCount() > 0) {
			$sql = $sql->fetch();
			return $sql['Nome'];
		}

	}

}











