<?php

class Queue {
	private $batch;
	
	public function beginNewBatch() {
		$this->batch = createId(3, "queue", "batch", true);
	}
	
	public function setBatch($batch) {
		$this->batch = $batch;
	}
	
	public function addJob($job, $additional) {
		global $_db;
		
		$_db->query('INSERT INTO queue VALUES (null, ?, ?, ?)', array($this->batch, $job, $additional));
	}
	
	public function exec($script) {
		exec("php " . $script . ".php --batch=" . $this->batch . " --domain=" . $_SERVER['SERVER_NAME'] . " >> /dev/null 2>&1 &");
	}
	
	public function getJobs() {
		global $_db;
		
		$result = $_db->query('SELECT * FROM queue WHERE batch = ?', array($this->batch));
		return $result->fetchAll();
	}
	
	public function finishJob($id) {
		global $_db;
		
		$_db->query('DELETE FROM queue WHERE id = ? AND batch = ?', array($id, $this->batch));
	}
}

?>