<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CE_Dashboard extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{	

		$this->load->model("customer_manager_model");
		
		if(!$this->customer_manager_model->has_customer_logged_in())
			return redirect(get_link("customer_login"));

		$this->lang->load('ce_dashboard',$this->language->get());

		$info=$this->customer_manager_model->get_logged_customer_info();
		$this->data['customer_name']=$info['customer_name'];

		$this->data['message']=get_message();

		$this->data['lang_pages']=get_lang_pages(get_link("customer_dashboard",TRUE));

		$this->data['header_title']=$this->lang->line("header_title").$this->lang->line("header_separator").$this->data['header_title'];
		$this->data['header_meta_description']="";
		$this->data['header_meta_keywords']="";
		$this->data['header_meta_robots']="noindex";

		if("student" === $info['customer_type'])
			return $this->student_dashboard($info);

		if("teacher" === $info['customer_type'])
			return $this->teacher_dashboard($info);

		if("parent" === $info['customer_type'])
			return $this->parent_dashboard($info);
	}

	private function student_dashboard($customer_info)
	{
		$this->load->model("class_manager_model");
		$this->data['class_info']=$this->class_manager_model->get_class_info($customer_info['customer_class_id']);

		$this->send_customer_output("dashboard_student");

		return;
	}

	private function teacher_dashboard($customer_info)
	{
		$teacher_id=$customer_info['customer_id'];

		$this->load->model("reward_manager_model");
		$this->data['prize_teacher']=$this->reward_manager_model->is_prize_teacher($teacher_id);

		$this->load->model("class_manager_model");
		$this->data['classes']=$this->class_manager_model->get_teacher_classes($teacher_id);

		$this->send_customer_output("dashboard_teacher");

		return;
	}

	private function parent_dashboard($customer_info)
	{
		$parent_id=$customer_info['customer_id'];
		
		$this->load->model("message_manager_model");
		$this->data['groups']=$this->message_manager_model->get_customer_groups($parent_id);
		
		$this->send_customer_output("dashboard_parent");

		return;
	}
}