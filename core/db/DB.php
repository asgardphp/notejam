<?php
namespace Coxis\Core\DB;

class DBException extends \Exception {}

class DB {
	private $db;
	private $config;

	public function __construct($config, $db=null) {
		$this->config = $config;
		if(!$db) {
			$this->db = new \PDO('mysql:host='.$config['host'].';dbname='.$config['database'], 
				$config['user'],
				$config['password'],
				array(\PDO::MYSQL_ATTR_FOUND_ROWS => true)
			);
		}
		else
			$this->db = $db;
		$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->query("SET NAMES 'utf8'");
	}
	
	public function getDB() {
		return $this->db;
	}
	
	public function import($file) {
		$host = $this->config['host'];
		$user = $this->config['user'];
		$pwd = $this->config['password'];
		$db = $this->config['database'];
		$cmd = 'mysql -h '.$host.' -u '.$user.($pwd ? ' -p'.$pwd:'').' '.$db.' < '.$file;
		exec($cmd);
	}

	public function query($sql, $args=array()) {
		return new Query($this->db, $sql, $args);
	}

	public function id() {
		return $this->db->lastInsertId();
	}
}

class Query {
	private $db;
	private $rsc;

	public function __construct($db, $sql, $args=array()) {
		$this->db = $db;
		try {
			if($args) {
				$rsc = $db->prepare($sql);
				$rsc->execute($args);
			}
			else
				$rsc = $db->query($sql);
			$this->rsc = $rsc;
		} catch(\PDOException $e) {
			throw new DBException($e->getMessage().'<br/>'."\n".'SQL: '.$sql.'<br>('.implode(', ', $args).')');
		}
	}
	
	public function next() {
		return $this->rsc->fetch(\PDO::FETCH_ASSOC);
	}

	public function affected() {
		return $this->rsc->rowCount();
	}

	public function count() {
		return $this->rsc->rowCount();
	}

	public function first() {
		return $this->rsc->fetch(\PDO::FETCH_ASSOC);
	}

	public function all() {
		return $this->rsc->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function id() {
		return $this->db->lastInsertId();
	}
}