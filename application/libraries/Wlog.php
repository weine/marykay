<?php
/**
 * LOG函式庫
 * @Author: Weine
 * @date: 2015/06/10
 */
class Wlog
{
	public $debug;
	public $log_arr;
	public $CI;

	public function __construct()
	{
		$this->CI = &get_instance();
		$this->debug = TRUE;
		$this->log_arr = array();
	}

	public function set_debug_on()
	{
		$this->debug = TRUE;
	}

	public function set_debug_off()
	{
		$this->debug = FALSE;
	}

	public function debug_log($msg, $title = NULL)
	{
		if(empty($title))
		{
			$this->log_arr['title'] = __FUNCTION__;	
		}
		else
		{
			$this->log_arr['title'] = (string) trim($title);
		}
		
		$this->log_arr['info'] = (string) trim($msg);
		$this->log_arr['time'] = date('Y-m-d H:i:s');

		$this->output_log();
	}

	public function output_log()
	{
		$this->CI->load->model('Log_common');
		$this->CI->Log_common->set_log($this->log_arr);
	}
}

//End