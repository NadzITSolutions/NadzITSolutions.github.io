<?php
include_once("models/AdminModel.php");

class AdminController{
	public $model;
	public $request;
	public $method;
	
	public function __construct($request,$method)  
    {  
        $this->model = new AdminModel();
		$this->request = $request;
		$this->method = $method;
    } 
	
	public function resolve()
	{
		$arguments = explode("/",$this->request);
		
		if((count($arguments)==1)||((count($arguments)==2)&&($arguments[1]=="")))
		{			
			if($this->method=="POST")
			{
				$this->model->addadmin();	
			}
			else if($this->method=="GET")
			{
				$this->model->listadmin();
			}
			else
			{
				$this->model->returnerror("Invalid Method");
			}
		}
		else if(((count($arguments)==2)&&($arguments[1]=="login"))||((count($arguments)==3)&&($arguments[2]=="")&&($arguments[1]=="login")))
		{
			if($this->method=="POST")
			{
				$this->model->dologin();
			}
			else if($this->method=="GET")
			{
				$this->model->islogged();
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