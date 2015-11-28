<?php
include_once("models/ValidationModel.php");

class ValidationController{
	public $model;
	public $request;
	public $method;
	
	public function __construct($request,$method)  
    {  
        $this->model = new ValidationModel();
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
		else if(((count($arguments)==2)&&($arguments[1]=="email"))||((count($arguments)==3)&&($arguments[2]=="")&&($arguments[1]=="email")))
		{
			if($this->method=="POST")
			{
				$this->model->ValidateEmail();
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