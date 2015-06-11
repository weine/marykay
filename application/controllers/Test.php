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

		$this->wlog->debug_log('test');

		$this->api_output->set_data('data', $a);
		$this->api_output->json_output();
	}

	public function bind_test()
	{
		$sql = "INSERT INTO ABC(NAME, AGE, ADDR) VALUES(:NAME, :AGE, :ADDR)";
		$sql = preg_quote('/' . ':', '/');

		$bind = array(":NAME" => "mary", ":AGE" => "20", ":ADDR" => "火星上");

		//str_replace(":NAME", "mary", $sql);

		// foreach($bind as $k => $v)
		// {
		// 	str_replace($k, $this->db->escape($v), $sql);
		// }

		var_export($sql);
	}
}


//End