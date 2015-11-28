<?php
include_once("models/DriverModel.php");

class DriverController{
	public $model;
	public $request;
	public $method;
	
	public function __construct($request,$method)  
    {  
        $this->model = new DriverModel();
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
				$this->model->returnerror("Invalid Method");
			}
			else if($this->method=="GET")
			{
				$this->model->listdrivers();
				return;
			}
		}
		else if(((count($arguments)==2)&&($arguments[1]=="login"))||((count($arguments)==3)&&($arguments[2]=="")&&($arguments[1]=="login")))
		{
			if($this->method=="POST")
			{
				$this->model->dologin();
				return;
			}
			else
			{
				$this->model->returnerror("Invalid Method");
			}
		}
		else if(((count($arguments)==2)&&($arguments[1]=="applications"))||((count($arguments)==3)&&($arguments[2]=="")&&($arguments[1]=="applications")))
		{
			if($this->method=="POST")
			{
				$this->model->registerdriver();	
				return;
			}
			else
			{
				$this->model->listapplications();
				return;
			}
		}
		else if(((count($arguments)==2)&&($arguments[1]=="locations"))||((count($arguments)==3)&&($arguments[2]=="")&&($arguments[1]=="locations")))
		{
			if($this->method=="POST")
			{
				$this->model->returnerror("Invalid Method");
			}
			else
			{
				$this->model->locations();
				return;
			}
		}
		else if((count($arguments)==2)||((count($arguments)==3)&&($arguments[2]=="")))
		{
			if($this->method=="POST")
			{
				$this->model->returnerror("Invalid Method");
			}
			else
			{
				$identifier = rtrim($arguments[1],"/");
				if(is_numeric($identifier))
				{
					$this->model->getdriver((int)$arguments[1]);
				}
				else
				{
					$this->model->returnerror("Invalid Identifier");
				}
			}
		}
		else if(((count($arguments)==3)&&($arguments[1]=="approve"))||((count($arguments)==4)&&($arguments[3]=="")&&($arguments[1]=="approve")))
		{
			if($this->method=="POST")
			{
				$this->model->approvedriver($arguments[2]);
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