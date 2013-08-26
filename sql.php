<?php
/**
 * CodeIgniter SQL (All-in-One) Model - 08/08/2013
 *
 * Single CI Model for much easier and quicker database management. Read SQL_README.md for instructions.
 *
 * @package        	CodeIgniter
 * @subpackage    	Model
 * @category    	Model
 * @author        	Puneet Kalra (http://www.puneetk.com)
 * @license         	MIT License
 * @link		https://github.com/puneetkay/CodeIgniter
 */


class Sql extends CI_Model {

	private $_ci;				// Holds CI instance
	private $table = ""; 			// Holds table name
	private $primary = "id"; 		// Holds column name with primary key
	private $autoPrimary = true; 		// Auto fetch column name with primary key
	private $query = null; 			// Holds query object 
	private $resultType = 'OBJECT'; 	// Result type? Object or Array
	static private $primaryList = array(); 	// Holds Table/PrimaryKey map.


	function __construct(){
		parent::__construct();
		$this->_ci =& get_instance();
		log_message('debug','SQL Model class initialised.');
	}


	function setTable($table){
		$this->table = $table;
		if($this->autoPrimary != FALSE){
			if(isset(self::$primaryList[$this->table])){
				$this->setPrimary(self::$primaryList[$this->table]);
			}else{
				$this->setPrimary($this->findPrimary());
			}
		}
		return $this;
	}

	function setPrimary($key){
		$this->primary = $key;
		if(!isset(self::$primaryList[$this->table])){
			self::$primaryList[$this->table] = $key;
		}
		return $this;
	}

	function setAutoPrimary($bool){
		$this->autoPrimary = $bool;
		return $this;
	}

	function setResultType($string){
		$this->resultType = $string;
		return $this;
	}

	private function findPrimary(){
		$q = $this->db->query("SELECT column_name FROM information_schema.columns WHERE 
			TABLE_NAME = '".$this->table."' AND 
			TABLE_SCHEMA = '".$this->db->database."' AND 
			COLUMN_KEY = 'PRI'");
		if($q->num_rows() == 1){
			return $q->row()->column_name;
		}else{
			log_message('error',"No primary key found for table '".$this->table."'");
		}
	}

	function getQuery(){
		return $this->query;
	}

	function select($columns){
		$this->db->select($columns);
		return $this;
	}

	function get($id){
		$this->db->where(array($this->primary => $id));
		$this->query = $this->db->get($this->table);
		return $this;
	}

	function getAll(){
		$this->query = $this->db->get($this->table);
		return $this;
	}

	function getWhere($where = array()){
		$this->db->where($where);
		$this->query = $this->db->get($this->table);
		return $this;
	}

	function getNumRows(){

		if(method_exists($this->query, 'num_rows'))
			return $this->query->num_rows();
		else
			return false;
	}

	function getRow($index = null){
		if($this->resultType == 'ARRAY'){
			if($index == null)
				return $this->query->row_array();
			else
				return $this->query->row_array($index);
		}
		if($index == null)
			return $this->query->row();
		else
			return $this->query->row($index);
	}

	function getResult(){
		if(method_exists($this->query, 'result'))
			return $this->query->result();
		else
			return false;
	}

	function freeMemory(){
		$this->query->free_result();
		$this->query = null;
		return $this;
	}

	function insert($data = array()){
		$this->db->insert($this->table,$data);
		return $this;
	}

	function getInsertId(){
		return $this->db->insert_id();
	}

	function update($data, $primaryKey){
		$this->db->update($this->table,$data,array($this->primary => $primaryKey));
		return $this;
	}

	function updateWhere($data, $where){
		$this->db->update($this->table,$data,$where);
		return $this;
	}

	function save($record){
		$pri = $this->primary;
		if(!empty($record) && (is_array($record) || is_object($record))){

			if(	(is_array($record) && isset($record[$pri])) || 
				(isset($record->$pri) || property_exists($record,$pri))
				){
				// Update
				if(is_array($record) && isset($record[$pri]))
					$this->db->update($this->table,$record,array($pri => $record[$pri]));
				else 
					$this->db->update($this->table,$record,array($pri => $record->$pri));
			}else{
				// Insert
				$this->db->insert($this->table,$record);
			}

		}else{
			log_message('error',"Blank record parameter given.");
		}
		return $this;
	}	

	function delete($key){
		$this->db->delete($this->table, array($this->primary => $key));
		return $this;
	}

	function deleteWhere($where){
		$this->db->delete($this->table, $where);
		return $this;
	}

	function count(){
		return $this->db->count_all_results();
	}
}


?>
