<?php
class Admin_model extends CI_Model {

    function __construct()
	{
        parent::__construct();
    }
	
	function check_user($login, $password)
    {
		$this->load->database();
		$sql = "SELECT * FROM user WHERE login='".$login."' AND password='".$password."';";
        $query = $this->db->query($sql);
		if($query->num_rows()>0){				
			$row = $query->row();
			return $row->id; 
		}
		else{
			return "-1";
		}
    }
	
	function check_for_user($login)
    {
		$this->load->database();
		$sql = "SELECT * FROM user WHERE login='".$login."';";
        $query = $this->db->query($sql);
		if($query->num_rows()>0){				
			$row = $query->row();
			return $row->id; 
		}
		else{
			return "-1";
		}
    }
	
	function getListOfArticles() {
		$this->load->database();
		
		$sql = "SELECT ar.id, ar.name, cr.name_en, ar.my_order ".
				"FROM article_en as ar, category as cr ".
				"WHERE ar.category_id = cr.id ".
				"ORDER BY ar.category_id, ar.my_order";
		
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	function getAmountOfArticlesInCategory($category_id){
		$this->load->database();
		$query = $this->db->query("SELECT * FROM article_en WHERE category_id=".$category_id.";");
		return $query->num_rows();
	}
	
	function getListOfCategories() {
		$this->load->database();
		
		$sql = "SELECT * ".
				"FROM category;";
		
		$query = $this->db->query($sql);
		return $query->result();
	}

	function getArticle($id, $lang){
		$this->load->database();
		
		$table = "";
		
		if($lang=="en"){
			$table = "article_en";
		}
		else if($lang=="ru"){
			$table = "article_ru";
		}
		else if($lang=="kz"){
			$table = "article_kz";
		}
		else {
			return NULL;
		}
		
		$sql = "SELECT * ".
				"FROM ".$table." ". 
				"WHERE id=".$id.";";
		
		$query = $this->db->query( $sql );
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		else {
			return NULL;
		}
	}
	
	function addArticle($name, $category_id, $definition, $example, $task, $my_order) {
		$this->load->database();
		
		$id = $this->db->query("SELECT max(id) as id_max FROM article_en")->row(0)->id_max+1;
		
		$sql_en = "INSERT INTO article_en(id, name, definition, example, task, category_id, my_order ) 
				VALUES ( ?, ?, ?, ?, ?, ?, ? );";
		$sql_ru = "INSERT INTO article_ru(id, name, definition, example, task, category_id, my_order ) 
				VALUES ( ?, ?, ?, ?, ?, ?, ? );";
		$sql_kz = "INSERT INTO article_kz(id, name, definition, example, task, category_id, my_order ) 
				VALUES ( ?, ?, ?, ?, ?, ?, ? );";
		
		
		$query = $this->db->query( $sql_en, array($id, $name, $definition, $example, $task, $category_id, $my_order));
		$query = $this->db->query( $sql_ru, array($id, $name, $definition, $example, $task, $category_id, $my_order));
		$query = $this->db->query( $sql_kz, array($id, $name, $definition, $example, $task, $category_id, $my_order));
		
		return true;
	}
	
	function editArticle($id, $name, $category_id, $definition, $example, $task, $my_order, $lang) {
		$this->load->database();
		
		$sql = "UPDATE article_".$lang." 
				SET name = ?, definition = ?, example = ?, task = ?, category_id = ?, my_order = ?
				WHERE id =".$id.";";
				
		$sql_en = "UPDATE article_en SET category_id=".$category_id." WHERE id =".$id.";";
		$sql_ru = "UPDATE article_ru SET category_id=".$category_id." WHERE id =".$id.";";
		$sql_kz = "UPDATE article_kz SET category_id=".$category_id." WHERE id =".$id.";";
		$query = $this->db->query( $sql, array($name, $definition, $example, $task, $category_id, $my_order) );
		$query_en = $this->db->query( $sql_en );
		$query_ru = $this->db->query( $sql_ru );
		$query_kz = $this->db->query( $sql_kz );
		return true;
		
	}
	
	function deleteArticle($id){
		$this->load->database();
		$sql_en = "DELETE ".
				"FROM article_en ". 
				"WHERE id=".$id.";";
		$sql_ru = "DELETE ".
				"FROM article_ru ". 
				"WHERE id=".$id.";";
		$sql_kz = "DELETE ".
				"FROM article_kz ". 
				"WHERE id=".$id.";";
		
		$query_en = $this->db->query( $sql_en );
		$query_ru = $this->db->query( $sql_ru );
		$query_kz = $this->db->query( $sql_kz );
		
		return true;
	}
	
	/*
		Function that assort articles in category
	*/
	function sortasc($category_id) {
		$this->load->database();
		
		$sql =  "SELECT * FROM article_en ".
				"WHERE category_id=".$category_id." ".
				"ORDER BY my_order ASC";
		
		$query = $this->db->query($sql);
		
		$count = 1;
		foreach ($query->result() as $row) {
			$sql_en = "UPDATE article_en SET my_order=".$count." WHERE id =".$row->id.";";
		    $sql_ru = "UPDATE article_ru SET my_order=".$count." WHERE id =".$row->id.";";
			$sql_kz = "UPDATE article_kz SET my_order=".$count." WHERE id =".$row->id.";";
		   
			$query_en = $this->db->query( $sql_en );
			$query_ru = $this->db->query( $sql_ru );
			$query_kz = $this->db->query( $sql_kz );
			$count = $count + 1;
		}
		return true;
	}
	
	function changeOrderOfArticles($ida, $my_ordera, $idb, $my_orderb) {
		$this->load->database();
		
		$sql_ena = "UPDATE article_en SET my_order=".$my_ordera." WHERE id =".$ida.";";
		$sql_rua = "UPDATE article_ru SET my_order=".$my_ordera." WHERE id =".$ida.";";
		$sql_kza = "UPDATE article_kz SET my_order=".$my_ordera." WHERE id =".$ida.";";
		
		$sql_enb = "UPDATE article_en SET my_order=".$my_orderb." WHERE id =".$idb.";";
		$sql_rub = "UPDATE article_ru SET my_order=".$my_orderb." WHERE id =".$idb.";";
		$sql_kzb = "UPDATE article_kz SET my_order=".$my_orderb." WHERE id =".$idb.";";
		
		$query_ena = $this->db->query( $sql_ena );
		$query_rua = $this->db->query( $sql_rua );
		$query_kza = $this->db->query( $sql_kza );
		
		$query_enb = $this->db->query( $sql_enb );
		$query_rub = $this->db->query( $sql_rub );
		$query_kzb = $this->db->query( $sql_kzb );
		
		
		return true;
	}
	
	function getArticleByOrder($order, $category_id) {
		$this->load->database();
		
		$sql = "SELECT * ".
				"FROM article_en ". 
				"WHERE my_order=".$order." ".
				"AND category_id=".$category_id.";";
		
		$query = $this->db->query( $sql );
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		else {
			return NULL;
		}
	}
	
}
?>