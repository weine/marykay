<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {


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

		$this->api_output->json_output('data', $a);
	}
}


//End