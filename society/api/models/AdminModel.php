<?php

class AdminModel{
	
	public function addadmin()
	{
		include 'core/config.php';
		
		$username=$_POST['username'];
		$password=$_POST['password'];
		$firstname=$_POST['firstname'];
		$lastname=$_POST['lastname'];
		$emailid=$_POST['emailid'];
		$dob=$_POST['dob'];
		$gender=$_POST['gender'];
		$address=$_POST['address'];
		$profilepic=$content_path.basename($_FILES["profilepic"]["name"]);
		
		move_uploaded_file($_FILES["profilepic"]["tmp_name"],$profilepic);
		
		$dbconnect = new mysqli($sql_host, $sql_user, $sql_password, $sql_db);
		
		if (mysqli_connect_errno()) 
		{
			$this->returnerror("Database error");
		}
		
		$query = sprintf("INSERT INTO rob_admins (username,password,firstname,lastname,emailid,dob,gender,address,addressdoc) VALUES (%s,%s,%s,%s,%s,%s,%s)",
					   $this->GetSQLValueString($dbconnect,$username, "text"),
					   $this->GetSQLValueString($dbconnect,$password, "text"),
					   $this->GetSQLValueString($dbconnect,$firstname, "text"),
					   $this->GetSQLValueString($dbconnect,$lastname, "text"),
                       $this->GetSQLValueString($dbconnect,$emailid, "text"),
					   $this->GetSQLValueString($dbconnect,$dob, "date"),
                       $this->GetSQLValueString($dbconnect,$gender, "text"),
					   $this->GetSQLValueString($dbconnect,$address, "text"),
					   $this->GetSQLValueString($dbconnect,$profilepic, "text"));
					   
		$result = $dbconnect->query($query) or die(($dbconnect->error.__LINE__) && $this->returnerror("SQL Error"));
		
		header('HTTP/1.1 201 Created');
		header('Content-Type: text/xml; charset=UTF-8');
		echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		echo("<success>Admin User Created</success>");
		
		mysqli_close($dbconnect);
		return;
	}
	
	public function listadmin()
	{
		include 'core/config.php';
		
		$dbconnect = new mysqli($sql_host, $sql_user, $sql_password, $sql_db);
		
		if (mysqli_connect_errno()) 
		{
			$this->returnerror("Database error");
		}
		
		$query = "SELECT * FROM rob_admins";
		
		$result = $dbconnect->query($query) or die(($dbconnect->error.__LINE__) && $this->returnerror("SQL Error"));
		
		header('HTTP/1.1 200 Success');
		header('Content-Type: text/xml; charset=UTF-8');
		echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		echo "<admins>\n";
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				echo "<admin>\n<id>".stripslashes($row['id'])."</id>\n<firstname>".stripslashes($row['firstname'])."</firstname>\n<lastname>".stripslashes($row['lastname'])."</lastname>\n<emailid>".stripslashes($row['emailid'])."</emailid>\n<dob>".stripslashes($row['dob'])."</dob>\n<gender>".stripslashes($row['gender'])."</gender>\n<address>".stripslashes($row['address'])."</address>\n</admin>\n";
			}
		}
		else {
			echo "<error>NO RESULTS</error>\n";	
		}
		echo "</admins>\n";
		
		mysqli_close($dbconnect);
	}
	
	public function dologin()
	{
		include 'core/config.php';
		
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		$dbconnect = new mysqli($sql_host, $sql_user, $sql_password, $sql_db);
		
		if (mysqli_connect_errno()) 
		{
			$this->returnerror("Database error");
		}
		
//		$query = "SELECT * FROM rob_admins WHERE username=".$username."AND password=".$password;
		
		$query = sprintf("SELECT id,username, password FROM rob_admins WHERE username=%s AND password=%s",
				$this->GetSQLValueString($dbconnect,$username, "text"), 
				$this->GetSQLValueString($dbconnect,$password, "text"));
		
		$result = $dbconnect->query($query) or die(($dbconnect->error.__LINE__) && $this->returnerror("SQL Error"));
		
		header('HTTP/1.1 200 Success');
		header('Content-Type: text/xml; charset=UTF-8');
		echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		echo "<logindata>\n";
		if($result->num_rows == 1) 
		{
			$row = $result->fetch_assoc();
			$this->setsession(stripslashes($row['id']));
			echo "<result>login successfull</result>";
			echo "<user>admin</user>";
			echo "<target></target>";
		}
		else {
			echo "<error>NO RESULTS</error>\n";	
		}
		echo "</logindata>\n";
		return;
	}
	
	public function islogged()
	{
		if(isset($_SESSION['username']))
		{
			header('HTTP/1.1 200 Success');
			header('Content-Type: text/xml; charset=UTF-8');
			echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
			echo("<user>".$_SESSION['username']."</user>");
			return;
		}
		else
		{
			header('HTTP/1.1 403 Unauthorized');
			header('Content-Type: text/xml; charset=UTF-8');
			echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
			echo("<error>Unauthorized</error>");
			return;
		}
	}
	
	public function returnerror($errorstring)
	{
		header('HTTP/1.1 400 Bad Request');
		header('Content-Type: text/xml; charset=UTF-8');
		echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		echo("<error>".$errorstring."</error>");
		return;
	}
	private function setsession($id)
	{
		$_SESSION['username']=$_POST['username'];
		$_SESSION['id']=$id;
		$_SESSION['priv']="cce";
	}
	
	private function unsetsession()
	{
		
	}
	
	private function GetSQLValueString($mysqlicon,$theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	{
	  if (PHP_VERSION < 6) {
		$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
	  }

	  $theValue = $mysqlicon->real_escape_string($theValue);

	  switch ($theType) {
		case "text":
		  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
		  break;    
		case "long":
		case "int":
		  $theValue = ($theValue != "") ? intval($theValue) : "NULL";
		  break;
		case "double":
		  $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
		  break;
		case "date":
		  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
		  break;
		case "defined":
		  $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
		  break;
	  }
	  return $theValue;
	}
}
?>