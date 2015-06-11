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
		$sql = "INSERT INTO {$log_table}(FUNC_NAME, LOG_INFO, EXE_TIME, INS_DT) ";
		$sql .= "VALUES(':FUNC_NAME', ':LOG_INFO', ':EXE_TIME', NOW())";
		
		if(empty($data))
		{
			$bind = array(
							':FUNC_NAME' => 'LOG ERROR',
							':LOG_INFO' => 'LOG 輸入錯誤，[input]: ' . var_export($data, TRUE),
							':EXE_TIME' => date('Y-m-d H:i:s')
						);
			$this->db->query($sql, $bind);
		}
		else
		{
			$bind = array(
						':FUNC_NAME' => $data['title'],
						':LOG_INFO' => $data['info'],
						':EXE_TIME' => $data['time']
					);
		}

		$query = $this->db->query($sql, $bind);
		if(FALSE === $query)
		{
			$bind = array(
							':FUNC_NAME' => 'DB ERROR',
							':LOG_INFO' => 'LOG DB 操作錯誤，[last_query]: ' . var_export($this->db->last_query(), TRUE),
							':EXE_TIME' => date('Y-m-d H:i:s')
						);
			$this->db->query($sql, $bind);
		}

	}
}

//End