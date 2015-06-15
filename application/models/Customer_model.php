<?php
/**
 * 客戶資料模組MODEL
 * @Author: Weine
 * @date 2015/06/14
 */
class Customer_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* 取得客戶列表 */
	public function get_list($start = 0, $offset = 20)
	{
		$table = "CUSTOMER";

		$sql = "SELECT CUS_ID, NAME, PHONE, 
					   (CASE CAN_CALL WHEN '1' THEN '上午' 
					   				 WHEN '2' THEN '下午' 
					   				 ELSE '晚上' 
					   	END) CAN_CALL  
				FROM {$table} 
				ORDER BY CUS_ID 
				LIMIT :IDX, :OFFSET";

		$bind = array(":IDX" => $start, ":OFFSET" => $offset);

		$query = $this->db->query($sql, $bind);
		if(FALSE === $query)
		{
			$this->wlog->debug_log($this->db->last_query(), __METHOD__);
			return FALSE;
		}
		elseif($query->num_rows() <= 0)
		{
			return '0';
		}

		$result = $query->result_array();

		return $result;
	}

	/* 新增客戶資料 */
	public function add_cus($customer)
	{
		if(!is_array($customer) || count($customer) == 0)
		{
			return "-1";
		}

		/* 取客戶編號 */
		$cus_id = $this->pub->get_id("CUSTOMER.CUS_ID");
		if(FALSE === $cus_id)
		{
			return FALSE;
		}

		$table = "CUSTOMER";

		$sql = "INSERT INTO {$table}(CUS_ID, NAME, BIRTHDAY, FB, PHONE, ADDR, CAN_CALL, INS_DT) 
				VALUES(:CUS_ID, :NAME, str_to_date(:BIRTHDAY, '%Y-%m-%d %H:%i:%s'), :FB, :PHONE, :ADDR, :CAN_CALL, NOW())";

		$bind = array(
						":CUS_ID" => $cus_id,
						":NAME" => $customer['name'],
						":BIRTHDAY" => $customer['birthday'],
						":FB" => $customer['fb'],
						":PHONE" => $customer['phone'],
						":ADDR" => $customer['addr'],
						":CAN_CALL" => $customer['can_call']
					);

		$query = $this->db->query($sql, $bind);
		if(FALSE === $query)
		{
			$this->wlog->debug_log($this->db->last_query(), __METHOD__);
			return FALSE;
		}
		else
		{
			return TRUE;
		}

	}

	/* 查詢客戶細項 */
	public function get_cus($cus_id = NULL)
	{
		if(empty($cus_id))
		{
			return '-1';
		}

		/* table */
		$table = 'CUSTOMER';

		$sql = "SELECT CUS_ID, NAME, DATE_FORMAT(BIRTHDAY, '%Y/%m/%d') BIRTHDAY, FB, PHONE, ADDR, CAN_CALL
				FROM {$table} 
				WHERE CUS_ID=:CUS_ID";
		$bind = array(':CUS_ID' => $cus_id);
		$query = $this->db->query($sql, $bind);
		if(FALSE === $query)
		{
			$this->wlog->debug_log($this->db->last_query(), __METHOD__);
			$result = FALSE;
		}
		else
		{
			$result = $query->row_array();
		}

		return $result;
	}
}

//End