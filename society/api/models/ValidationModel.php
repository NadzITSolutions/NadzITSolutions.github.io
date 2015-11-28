<?php

class ValidationModel{

	public function ValidateEmail()
	{
		include 'core/config.php';
		
		$valid = true;
		
		if (isset($_POST['emailid'])) 
		{
			$email = $_POST['emailid'];
			$dbconnect = new mysqli($sql_host, $sql_user, $sql_password, $sql_db);
			
			if (mysqli_connect_errno()) 
			{
				$this->returnerror("Database error");
			}
			
			$query = "SELECT * FROM rob_drivers WHERE emailid=\"".$email."\"";
			
			$result = $dbconnect->query($query) or die(($dbconnect->error.__LINE__) && $this->returnerror("SQL Error"));
			
			header('Content-type: application/json');
			
			if($result->num_rows > 0) 
			{
				$valid = false;
			}
		}
		
		echo json_encode(array(
		'valid' => $valid,
		));

	}
	
	public function returnerror($errorstring)
	{
		header('HTTP/1.1 400 Bad Request');
		header('Content-Type: text/xml; charset=UTF-8');
		echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		echo("<error>".$errorstring."</error>");
		return;
	}
}

?>