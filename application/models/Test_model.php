<?php
/**
 * 
 */
class Test_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_list()
	{
		$table = 'CUSTOMER';

		$sql = "SELECT * FROM {$table}";
		$query = $this->db->query($sql);

		if(FALSE === $query)
		{
			return FALSE;
		}
		else
		{
			$result = $query->result_array();

			return $result;
		}
	}
}
//End