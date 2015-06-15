<?php
/**
 * 預載MODEL
 * @author Weine
 * @date(2015/06/14)
 */
class Star_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database('star');
	}

	public function get_id($func_id)
	{

		if(empty($func_id))
		{
			return FALSE;
		}

		$table = "GET_FID";

		$sql = "SELECT CURR_C, FORMAT_C FROM {$table} WHERE FUNC_NAME=:FUNC_NAME";
		$bind = array(":FUNC_NAME" => $func_id);
		
		$query = $this->db->query($sql, $bind);

		if(FALSE === $query)
		{
			$this->wlog->debug_log($this->db->last_query(),__METHOD__);
			return FALSE;
		}
		elseif($query->num_rows() <= 0)
		{
			return FALSE;
		}

		$res = $query->row_array();

		$get_use_id = sprintf("{$res['FORMAT_C']}", (10000 + $res['CURR_C']));

		$sql = "UPDATE {$table} SET CURR_C= CURR_C+1 WHERE FUNC_NAME=:FUNC_NAME";
		$query2 = $this->db->query($sql, $bind);

		if(FALSE === $query2)
		{
			return FALSE;
		}
		else
		{
			return $get_use_id;
		}
	}
}