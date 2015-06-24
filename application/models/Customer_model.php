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

		if($start == '_' && $offset == '_')
		{
			$sql = "SELECT CUS_ID FROM {$table} WHERE IS_DEL = '0'";

			$query = $this->db->query($sql);
		}
		else
		{
			$sql = "SELECT CUS_ID, NAME, PHONE, 
					   (CASE CAN_CALL WHEN '1' THEN '上午' 
					   				 WHEN '2' THEN '下午' 
					   				 ELSE '晚上' 
					   	END) CAN_CALL  
				FROM {$table} 
				WHERE IS_DEL = '0'
				ORDER BY CUS_ID 
				LIMIT :IDX, :OFFSET";

			$bind = array(":IDX" => $start, ":OFFSET" => $offset);

			$query = $this->db->query($sql, $bind);
		}
		
		if(FALSE === $query)
		{
			$this->wlog->debug_log($this->db->last_query(), __METHOD__);
			return FALSE;
		}
		elseif($query->num_rows() <= 0)
		{
			return '0';
		}

		if($start == '_' && $offset == '_')
		{
			$result = $query->num_rows();
		}
		else
		{
			$result = $query->result_array();	
		}

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

		$sql = "SELECT CUS_ID, NAME, DATE_FORMAT(BIRTHDAY, '%Y-%m-%d') BIRTHDAY, FB, PHONE, ADDR, CAN_CALL
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

	/* 刪除客戶資料 */
	public function del_cus($cus_id)
	{
		if(empty($cus_id))
		{
			return "-1";
		}

		$table = "CUSTOMER";

		$sql = "UPDATE {$table} SET IS_DEL='1', IS_DEL_DT=NOW() WHERE CUS_ID=:CUS_ID";

		$bind = array(
						":CUS_ID" => $cus_id,
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

	/* 取得題目 */
	public function get_qu()
	{
		$table = "QUESTION";
		$sql = "SELECT * FROM {$table}";
		$query = $this->db->query($sql);
		$qus = array();
		if(FALSE === $query)
		{
			$this->wlog->debug_log($this->db->last_query(), __METHOD__);
			$result = FALSE;
		}
		else
		{
			$result = $query->result_array();
			foreach($result as $k => $v)
			{
				$qus[$v['QUES_ID']] = $v['QUES_CONTENT'];
			}
		}

		return $qus;
	}

	/* 取得答案項目 */
	public function get_ans()
	{
		$table = "QUES_ANS";
		$sql = "SELECT * FROM {$table}";
		$query = $this->db->query($sql);
		if(FALSE === $query)
		{
			$this->wlog->debug_log($this->db->last_query(), __METHOD__);
			$result = FALSE;
		}
		else
		{
			$result = $query->result_array();
		}

		$ans = array();
		foreach($result as $k => $v)
		{
			$ans[$v['QUES_ID']."_A"][] = array('ANS_ID' => $v['QANS_ID'], 'ANS' => $v['QANS_INFO']);
		}

		return $ans;

	}
}

//End