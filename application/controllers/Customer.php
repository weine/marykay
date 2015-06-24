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
		$this->load->library('pagination');
		$this->data = array();

		$this->data['base_url'] = base_url();
		$this->data['js'] = base_url() . 'asset/js';
		$this->data['css'] = base_url() . 'asset/css';
		$this->data['title'] = '【客戶資料系統】';
	}

	public function index()
	{
		$start = 0;
		$offset = 20;

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
		$total_info = $this->total();
		$this->data['total_pages'] = ceil($total_info['data']/$offset);

		$content = $this->parser->parse('customer.html', $this->data, TRUE);
		$this->data['content'] = $content;

		$this->parser->parse('page_outer.html', $this->data);
	}

	public function p($page = 1)
	{
		$offset = 20;
		if(empty($page))
		{
			$start = 0;
		}
		else
		{
			$page = (int) trim($page);

			$start = ($page - 1)*$offset;
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

		$total_info = $this->total();
		$this->data['total_pages'] = ceil($total_info['data']/$offset);

		$content = $this->parser->parse('customer.html', $this->data, TRUE);
		$this->data['content'] = $content;

		$this->parser->parse('page_outer.html', $this->data);
	}

	public function total()
	{
		/* load model */
		$this->load->model('Customer_model');

		$result = $this->Customer_model->get_list('_', '_');

		$output = array();
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
			$output['data'] = (int)$result;
		}

		return $output;
	}

	/* 新增客戶view */
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

	/**
	 * 編輯問卷
	 * @action /Customer/question
	 */
	public function question($cus_id)
	{
		if(empty($cus_id))
		{
			$cus_id = 99999;
		}

		$cus_id = (string) trim($cus_id);

		/* load model */
		$this->load->model('Customer_model');

		$result = $this->Customer_model->get_qus($cus_id);

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
		elseif("0" === $result)
		{
			$output['status'] = "103";
			$output['api_msg'] = "查無資料";
		}
		else
		{
			$output['status'] = "100";
			$output['api_msg'] = "查詢成功";
			$output['data'] = $result;
		}

		$this->data = array_merge($output, $this->data);

		$content = $this->parser->parse('question.html', $this->data, TRUE);
		$this->data['content'] = $content;

		$this->parser->parse('page_outer.html', $this->data);
	}

	/**
	 * 刪除客戶
	 * @action /Customer/del
	 */
	public function delcus()
	{
		$data = $this->api_input->json_input($this->input->post("data"));

		$cus_id = (string) trim($data['cus_id']);

		/* load model */
		$this->load->model('Customer_model');

		$output = array();
		$result = $this->Customer_model->del_cus($cus_id);

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
			$output['api_msg'] = "刪除成功";
		}

		$json = json_encode($output);
		echo $json;
	}

	public function cinfo($cus_id)
	{
		if(empty($cus_id))
		{
			$this->index();
		}

		$cus_id = (string) trim($cus_id);

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

		/* 取得題目 */
		$res_qu = $this->Customer_model->get_qu();
		if(FALSE === $res_qu)
		{
			$output['status'] = "101";
			$output['api_msg'] = "資料庫操作失敗!";
		}
		else
		{
			$this->data = array_merge($res_qu, $this->data);
		}

		/* 取得答案 */
		$res_ans = $this->Customer_model->get_ans();
		if(FALSE === $res_ans)
		{
			$output['status'] = "101";
			$output['api_msg'] = "資料庫操作失敗!";
		}
		else
		{
			$this->data = array_merge($res_ans, $this->data);
		}


		$this->data = array_merge($result, $this->data);
		$this->data = array_merge($output, $this->data);

		$content = $this->parser->parse('customer_info.html', $this->data, TRUE);
		$this->data['content'] = $content;

		$this->parser->parse('page_outer.html', $this->data);
	}

	public function testpost()
	{
		$data = $this->api_input->json_input($this->input->post("data"));


		$this->wlog->debug_log(var_export($data, TRUE), __METHOD__);

		echo json_encode($data);
	}
}

//End