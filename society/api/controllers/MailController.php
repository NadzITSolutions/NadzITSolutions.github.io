<?php
include_once("models/MailModel.php");

class MailController{
	public $model;
	public $request;
	public $method;
	
	public function __construct($request,$method)  
    {  
        $this->model = new MailModel();
		$this->request = $request;
		$this->method = $method;
    } 
	
	public function resolve()
	{
		$arguments = explode("/",$this->request);
		
		if((count($arguments)==1)||((count($arguments)==2)&&($arguments[1]=="")))
		{			
			$this->model->returnerror("Invalid Request");
		}
		else if(((count($arguments)==2)&&($arguments[1]=="subscribe"))||((count($arguments)==3)&&($arguments[2]=="")&&($arguments[1]=="email")))
		{
			if($this->method=="POST")
			{
				$this->model->Email($arguments[1]);
			}
			else if($this->method=="GET")
			{
				$this->model->returnerror("Invalid Method");
			}
			else
			{
				$this->model->returnerror("Invalid Method");
			}
		}
		else if(((count($arguments)==2)&&($arguments[1]=="enquiry"))||((count($arguments)==3)&&($arguments[2]=="")&&($arguments[1]=="email")))
		{
			if($this->method=="POST")
			{
				$this->model->Email($arguments[1]);
			}
			else if($this->method=="GET")
			{
				$this->model->returnerror("Invalid Method");
			}
			else
			{
				$this->model->returnerror("Invalid Method");
			}
		}
		else
		{
			$this->model->returnerror("Invalid Request");
		}
	}
}
?>