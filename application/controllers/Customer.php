<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 客戶相關系統
 * @Author: Weine
 */
class Customer extends CI_Controller
{
	public $data;
	public function __construct()
	{
		parent::__construct();
		$this->data = array();

		$this->data['base_url'] = base_url();
		$this->data['js'] = base_url() . 'asset/js';
		$this->data['css'] = base_url() . 'asset/css';
		$this->data['title'] = '【客戶資料系統】';
	}

	public function index()
	{
		$data = $this->api_input->json_input($this->input->post("data"));

		if(FALSE === $data)
		{
			$start = 0;
			$offset = 20;
		}
		else
		{
			$start = (int) trim($data['start']);
			$offset = (int) trim($data['offset']);
		}


		/* load model */
		$this->load->model('Customer_model');

		$output = array();
		$result = $this->Customer_model->get_list($start, $offset);
		if(FALSE === $result)
		{
			$output['status'] = "101";
			$output['api_msg'] = "資料庫操作失敗!";
			$output['data'] = array();
		}
		elseif('0' === $result)
		{
			$output['status'] = "102";
			$output['api_msg'] = "查無資料!";
			$output['data'] = array();
		}
		else
		{
			$output['status'] = "100";
			$output['api_msg'] = "查詢成功";
			$output['data'] = $result;
		}
		
		$this->data = array_merge($output, $this->data);

		$content = $this->parser->parse('customer.html', $this->data, TRUE);
		$this->data['content'] = $content;

		$this->parser->parse('page_outer.html', $this->data);
	}

	public function add()
	{
		$content = $this->parser->parse('addcus.html', $this->data, TRUE);
		$this->data['content'] = $content;

		$this->parser->parse('page_outer.html', $this->data);	
	}

	/**
	 * 新增客戶
	 * @action: /Customer/addcus
	 * @post_param name 姓名
	 * @post_param birthday 生日
	 * @post_param fb facebook
	 * @post_param phone 電話
	 * @post_param addr 地址
	 * @post_param can_call 可以聯絡的時間(1:上午/2:下午/3:晚上)
	 * 
	 * @return status 狀態碼
	 */
	public function addcus()
	{
		$data = $this->api_input->json_input($this->input->post("data"));

		foreach($data as $k => $v)
		{
			$data[$k] = (string) trim($v);
		}


		/* load model */
		$this->load->model('Customer_model');

		$output = array();
		$result = $this->Customer_model->add_cus($data);

		if(FALSE === $result)
		{
			$output['status'] = "101";
			$output['api_msg'] = "資料庫操作失敗!";
		}
		elseif("-1" === $result)
		{
			$output['status'] = "102";
			$output['api_msg'] = "輸入參數錯誤";
		}
		else
		{
			$output['status'] = "100";
			$output['api_msg'] = "新增成功";
		}

		$json = json_encode($output);
		echo $json;
	}

	/**
	 * 查詢客戶
	 * @action /Customer/getcus
	 */
	public function getcus()
	{
		$data = $this->api_input->json_input($this->input->post("data"));

		$cus_id = (string) trim($data['cus_id']);

		/* load model */
		$this->load->model('Customer_model');

		$result = $this->Customer_model->get_cus($cus_id);

		$output = array();
		if(FALSE === $result)
		{
			$output['status'] = "101";
			$output['api_msg'] = "資料庫操作失敗!";
		}
		elseif("-1" === $result)
		{
			$output['status'] = "102";
			$output['api_msg'] = "輸入參數錯誤";
		}
		else
		{
			$output['status'] = "100";
			$output['api_msg'] = "查詢成功";
			$output['data'] = $result;
		}

		$json = json_encode($output);
		echo $json;
	}

	public function testpost()
	{
		$data = $this->api_input->json_input($this->input->post("data"));


		$this->wlog->debug_log(var_export($data, TRUE), __METHOD__);

		echo json_encode($data);
	}
}

//End