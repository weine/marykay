<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	public function get_date()
	{
		echo date('Y-m-d H:i:s');
	}

	public function get_list()
	{
		$this->load->model('Test_model');
		$result = $this->Test_model->get_list();

		$json_output = json_encode($result);

		echo $json_output;
	}

	public function show_data()
	{
		$json = $this->api_input->json_input($this->input->post('data'));

		var_export($json);
	}

	public function show_json()
	{
		$a = array('it' => 'ggwp', 'seq' => '1124');

		$this->wlog->debug_log('API輸入錯誤!', __FUNCTION__);

		$this->api_output->set_data('data', $a);
		$this->api_output->json_output();
	}

	public function bind_test()
	{
		$bind_mark = ":";
		$sql = "INSERT INTO ABC(NAME, AGE, ADDR) VALUES(:NAME, :AGE, :ADDR)";

		$bind_mark = '/' . preg_quote($bind_mark, '/') . '/i';

		$bind = array(":NAME" => "mary", ":AGE" => "20", ":ADDR" => "火星上");
		$bind_count = count($bind);


		// if($c = preg_match_all("/:[^:]*[^:|,| |)]/i", $sql, $matches))
		// {
		// 	foreach($bind as $k => $v)
		// 	{
		// 		str_replace($k, $this->db->escape($v), $sql);
		// 	}
		// }
		if($c = preg_match_all("/:[^:]*[^:|,| |)]/i", $sql, $matches,PREG_OFFSET_CAPTURE) != $bind_count)
		{
			var_export($sql);
			exit;
		}

		do
		{
			$c--;
			$escaped_value = $this->escape($bind[$matches[0][$c][0]]);
			if (is_array($escaped_value))
			{
				$escaped_value = '('.implode(',', $escaped_value).')';
			}
			$sql = substr_replace($sql, $escaped_value, $matches[0][$c][1], strlen($matches[0][$c][0]));
		}
		while ($c !== 0);

		var_export($sql);
	}
}


//End