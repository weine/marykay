<?php
class Log_common extends CI_Model
{
	public  function __construct()
	{
		parent::__construct();
	}

	public function set_log($data = array())
	{
		$bind = array();
		$log_table = 'LOG';
		$sql = "INSERT INTO {$log_table}(FUNC_NAME, LOG_INFO, EXE_TIME, INS_DT) VALUES(:FUNC_NAME, :LOG_INFO, str_to_date(:EXE_TIME,'%Y-%m-%d %H:%i:%s'), NOW())";
		
		if(empty($data))
		{
			$bind[':FUNC_NAME'] = 'LOG ERROR';
			$bind[':LOG_INFO'] = 'LOG 輸入錯誤，[input]: ' . var_export($data, TRUE);
			$bind[':EXE_TIME'] = date('Y-m-d H:i:s');
		}
		else
		{
			$bind[':FUNC_NAME'] = $data['title'];
			$bind[':LOG_INFO'] = $data['info'];
			$bind[':EXE_TIME'] = $data['time'];
		}

		$query = $this->db->query($sql, $bind);
		if(FALSE === $query)
		{
			$bind[':FUNC_NAME'] = 'DB ERROR';
			$bind[':LOG_INFO'] = 'LOG DB 操作錯誤，[last_query]: ' . var_export($this->db->last_query(), TRUE);
			$bind[':EXE_TIME'] = date('Y-m-d H:i:s');

			$this->db->query($sql, $bind);
		}

	}
}

//End File