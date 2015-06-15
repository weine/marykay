<?php
/**
 * 共用TOOLS
 */
class Pub
{
	public $CI;
	public function __construct()
	{
		$this->CI = & get_instance();
	}

	public function get_id($func_id)
	{
		if(empty($func_id))
		{
			return FALSE;
		}

		/* load model */
		$this->CI->load->model('Star_model');

		$result = $this->CI->Star_model->get_id($func_id);

		if(FALSE)
		{
			$this->wlog->debug_log("資料庫取號錯誤");
			return FALSE;
		}

		return $result;
	}
}
//End