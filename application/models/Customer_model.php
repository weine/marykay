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

	/* 修改客戶資料 */
	public function edit_cus($customer)
	{
		if(!is_array($customer) || count($customer) == 0)
		{
			return "-1";
		}

		$table_cus = "CUSTOMER";
		$table_cans = "CUS_ANSWER";

		/* 修改資料 */
		$cus_sql = "UPDATE {$table_cus} 
					SET NAME=:NAME, BIRTHDAY=str_to_date(:BIRTHDAY, '%Y-%m-%d'), FB=:FB, PHONE=:PHONE, ADDR=:ADDR, CAN_CALL=:CAN_CALL, NOTE=:NOTE 
					WHERE CUS_ID=:CUS_ID";

		/* 查詢是否有問卷紀錄 */
		$chk_qu_sql = "SELECT * FROM {$table_cans} WHERE CUS_ID=:CUS_ID";

		/* 新增客戶問卷 */
		$ins_qu_sql = "INSERT INTO {$table_cans}(CUS_ID, QUES_A1, QUES_A2, QUES_A3, QUES_A4, QUES_A5, QUES_B1, QUES_B2, QUES_B3) 
						VALUES(:CUS_ID, :QUES_A1, :QUES_A2, :QUES_A3, :QUES_A4, :QUES_A5, :QUES_B1, :QUES_B2, :QUES_B3)";

		/* 修改客戶問卷 */
		$upd_qu_sql = "UPDATE {$table_cans} SET QUES_A1=:QUES_A1, QUES_A2=:QUES_A2, QUES_A3=:QUES_A3, QUES_A4=:QUES_A4, QUES_A5=:QUES_A5, QUES_B1=:QUES_B1, QUES_B2=:QUES_B2, QUES_B3=:QUES_B3 
						WHERE CUS_ID=:CUS_ID";

		
		/* transaction */
		$this->db->trans_begin();

		/* 客戶資料 */
		if($customer['type'] == '1')
		{
			$cus_bind = array(
						":CUS_ID" => $customer['cus_id'],
						":NAME" => $customer['name'],
						":BIRTHDAY" => $customer['birthday'],
						":FB" => $customer['fb'],
						":PHONE" => $customer['phone'],
						":ADDR" => $customer['addr'],
						":CAN_CALL" => $customer['can_call'],
						":NOTE" => $customer['note'],
					);

			$query = $this->db->query($cus_sql, $cus_bind);
			
		}

		/* 問卷調查 */
		elseif($customer['type'] == '2')
		{
			/* 檢查是否有問卷紀錄 */
			$query_chk = $this->db->query($chk_qu_sql, array(":CUS_ID" => $customer['cus_id']));
			if(FALSE === $query_chk)
			{
				$this->wlog->debug_log($this->db->last_query(), __METHOD__);
				$this->trans_rollback();
				return FALSE;
			}

			$record_num = $query_chk->num_rows();

			$qu_bind = array(
							":CUS_ID" => $customer['cus_id'],
							":QUES_A1" => $customer['qu_a1'],
							":QUES_A2" => $customer['qu_a2'],
							":QUES_A3" => $customer['qu_a3'],
							":QUES_A4" => $customer['qu_a4'],
							":QUES_A5" => $customer['qu_a5'],
							":QUES_B1" => $customer['qu_b1'],
							":QUES_B2" => $customer['qu_b2'],
							":QUES_B3" => $customer['qu_b3']
						);

			if($record_num <= 0)
			{
				$query = $this->db->query($ins_qu_sql, $qu_bind);
			}
			else
			{
				$query = $this->db->query($upd_qu_sql, $qu_bind);
			}

		}
		else
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		if(FALSE === $query)
		{
			$this->wlog->debug_log($this->db->last_query(), __METHOD__);
			$this->db->trans_rollback();
			return FALSE;
		}
		else
		{
			$this->db->trans_commit();
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
		$table_cus = 'CUSTOMER';
		$table_cans = 'CUS_ANSWER';

		$sql = "SELECT t.CUS_ID, t.NAME, DATE_FORMAT(t.BIRTHDAY, '%Y-%m-%d') BIRTHDAY, t.FB, t.PHONE, t.ADDR, t.CAN_CALL, t.NOTE, 
						a.QUES_A1 CUS_Q_A1, a.QUES_A2 CUS_Q_A2, a.QUES_A3 CUS_Q_A3, a.QUES_A4 CUS_Q_A4, a.QUES_A5 CUS_Q_A5, a.QUES_B1 CUS_Q_B1, a.QUES_B2 CUS_Q_B2, a.QUES_B3 CUS_Q_B3 
				FROM {$table_cus} t
				LEFT JOIN {$table_cans} a ON a.CUS_ID=t.CUS_ID 
				WHERE t.CUS_ID=:CUS_ID";
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

	/* 客戶好友功能 */
	public function cusfriend($data)
	{
		if(!is_array($data) || count($data) == 0)
		{
			return "-1";
		}

		$table = "CUS_FRIEND";

		/* 新增 */
		$ins_sql = "INSERT INTO {$table}(CUS_ID, CF_NAME, CF_TEL, INS_DT) VALUES(:CUS_ID, :CF_NAME, :CF_TEL, NOW())";

		/* 刪除 */
		$del_sql = "UPDATE {$table} SET ISDEL = 1, ISDEL_DT=NOW() WHERE CF_SEQ=:CF_SEQ";

		/* 修改 */
		$upd_sql = "UPDATE {$table} SET CF_NAME=:CF_NAME, CF_TEL=:CF_TEL WHERE CUS_ID=:CUS_ID AND CF_SEQ=:CF_SEQ";

		if($data['type'] == '1')
		{
			$bind = array(
							":CUS_ID" => $data['cus_id'],
							":CF_NAME" => $data['cfriName'],
							":CF_TEL" => $data['cfriTel']
						);

			$query = $this->db->query($ins_sql, $bind);
		}
		elseif($data['type'] == '2')
		{
			$bind = array(":CF_SEQ" => $data['cus_seq']);

			$query = $this->db->query($del_sql, $bind);
		}
		elseif($data['type'] == '3')
		{
			$bind = array(
							":CUS_ID" => $data['cus_id'],
							":CF_SEQ" => $data['cus_seq'],
							":CF_NAME" => $data['fname'],
							":CF_TEL" => $data['ftel']
						);

			$query = $this->db->query($upd_sql, $bind);
		}
		else
		{
			return "-1";
		}

		if(FALSE === $query)
		{
			$this->wlog->debug_log($this->db->last_query(), __METHOD__);
			return FALSE;
		}
		elseif($this->db->affected_rows() <= 0)
		{
			$this->wlog->debug_log('資料庫無處理', __METHOD__);
			return 0;
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
		$sql = "SELECT * FROM {$table} ORDER BY SORT_ID";
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