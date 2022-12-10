<?php
class Companies extends model {

	private $companyInfo;

	public function __construct($id) {
		parent::__construct();

		$sql = $this->db->prepare("SELECT * FROM companies WHERE id = :id");
		$sql->bindValue(':id', $id);
		$sql->execute();

		if($sql->rowCount() > 0) {
			$this->companyInfo = $sql->fetch();
		}
	}

	public function getName() {
		if(isset($this->companyInfo['name'])) {
			return $this->companyInfo['name'];
		} else {
			return '';
		}
	}

	public function getNextNFE() {
		$nfe_number = $this->companyInfo['nfe_number'];
		$nfe_number++;

		return $nfe_number;
	}

	public function setNFE($cNF, $id) {
		$sql = $this->db->prepare("UPDATE companies SET nfe_number = :nfe_number WHERE id = :id");
		$sql->bindValue(":nfe_number", $cNF);
		$sql->bindValue(":id", $id);
		$sql->execute();
	}














}