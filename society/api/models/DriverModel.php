<?php

class DriverModel{
	
	public function registerdriver()
	{
		include 'core/config.php';
		
		$firstname=$_POST['firstname'];
		$lastname=$_POST['lastname'];
		$emailid=$_POST['emailid'];
		$dob=$_POST['dob'];
		$gender=$_POST['gender'];
		$address=$_POST['address'];
		$vehiclenumber=$_POST['vehiclenumber'];
		$licensenumber=$_POST['licensenumber'];
		$handset=$_POST['handset'];
		
		$parts = explode('/', $dob);
		$date  = "$parts[2]-$parts[1]-$parts[0]";
		
		$licensefile_ext=strrchr($_FILES["license"]["name"],'.');
		$pancardfile_ext=strrchr($_FILES["pancard"]["name"],'.');
		$insurancefile_ext=strrchr($_FILES["insurance"]["name"],'.');
		$bikepicfile_ext=strrchr($_FILES["bikepic"]["name"],'.');
		$addressprooffile_ext=strrchr($_FILES["addressproof"]["name"],'.');
		
		$licensefile=$content_path.date("Y-m-d").'/'.md5(basename($_FILES["license"]["name"])).$licensefile_ext;
		$pancardfile=$content_path.date("Y-m-d").'/'.md5(basename($_FILES["pancard"]["name"])).$pancardfile_ext;
		$insurancefile=$content_path.date("Y-m-d").'/'.md5(basename($_FILES["insurance"]["name"])).$insurancefile_ext;
		$bikepicfile=$content_path.date("Y-m-d").'/'.md5(basename($_FILES["bikepic"]["name"])).$bikepicfile_ext;
		$addressprooffile=$content_path.date("Y-m-d").'/'.md5(basename($_FILES["addressproof"]["name"])).$addressprooffile_ext;
		
		$this->verifyimage();
		
		$curuploaddir = $content_path.date("Y-m-d").'/';
		
		if(file_exists($curuploaddir))
		{}
		else
		{
			mkdir($curuploaddir,0777);
		}
		
		if(move_uploaded_file($_FILES["license"]["tmp_name"],$licensefile))
		{
			if(move_uploaded_file($_FILES["pancard"]["tmp_name"],$pancardfile))
			{
				if(move_uploaded_file($_FILES["insurance"]["tmp_name"],$insurancefile))
				{
					if(move_uploaded_file($_FILES["bikepic"]["tmp_name"],$bikepicfile))
					{
						if(!(move_uploaded_file($_FILES["addressproof"]["tmp_name"],$addressprooffile)))
						{
							unlink($bikepicfile);
							unlink($insurancefile);
							unlink($pancardfile);
							unlink($licensefile);
							$this->returnerror("Error uploading Address File");
						}
						
					}
					else
					{
						unlink($insurancefile);
						unlink($pancardfile);
						unlink($licensefile);
						$this->returnerror("Error uploading Bike File");
					}
				}
				else
				{
					unlink($pancardfile);
					unlink($licensefile);
					$this->returnerror("Error uploading Insurance File");
				}
			}
			else
			{
				unlink($licensefile);
				$this->returnerror("Error uploading Pancard File");
			}
		}
		else
		{
			$this->returnerror("Error uploading License File");
		}
		
		$dbconnect = new mysqli($sql_host, $sql_user, $sql_password, $sql_db);
		
		if (mysqli_connect_errno()) 
		{
			$this->returnerror("Database error");
		}
		
		$query = sprintf("INSERT INTO rob_drivers (firstname,lastname,emailid,dob,gender,address,vechilereg,licensenum,handset,licensedoc,pandoc,insurancedoc,bikepicdoc,addressdoc) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
					   $this->GetSQLValueString($firstname, "text"),
					   $this->GetSQLValueString($lastname, "text"),
                       $this->GetSQLValueString($emailid, "text"),
					   $this->GetSQLValueString($date, "date"),
                       $this->GetSQLValueString($gender, "text"),
					   $this->GetSQLValueString($address, "text"),
					   $this->GetSQLValueString($vehiclenumber, "text"),
					   $this->GetSQLValueString($licensenumber, "text"),
                       $this->GetSQLValueString($handset, "text"),
                       $this->GetSQLValueString($licensefile, "text"),
					   $this->GetSQLValueString($pancardfile, "text"),
					   $this->GetSQLValueString($insurancefile, "text"),
                       $this->GetSQLValueString($bikepicfile, "text"),
					   $this->GetSQLValueString($addressprooffile, "text"));
					   
		$result = $dbconnect->query($query) or die(($dbconnect->error.__LINE__) && $this->returnerror("SQL Error"));
		
		header('HTTP/1.1 200 Success');
		header('Content-Type: text/xml; charset=UTF-8');
		echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		echo("<message>Driver Registered</message>");
		
		mysqli_close($dbconnect);
		return;
	}
	
	public function listapplications()
	{
		include 'core/config.php';
		
		$dbconnect = new mysqli($sql_host, $sql_user, $sql_password, $sql_db);
		
		if (mysqli_connect_errno()) 
		{
			$this->returnerror("Database error");
		}
		
		$query = "SELECT rob_drivers.* FROM rob_drivers LEFT OUTER JOIN rob_a_drivers on rob_drivers.id=rob_a_drivers.id WHERE rob_a_drivers.id is null";
		
		$result = $dbconnect->query($query) or die(($dbconnect->error.__LINE__) && $this->returnerror("SQL Error"));
		
		header('HTTP/1.1 200 Success');
		header('Content-Type: text/xml; charset=UTF-8');
		
		$driversXML = new SimpleXMLElement("<drivers></drivers>");
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$driverXML = $driversXML->addChild('driver');
				$driverXML->addChild('id',stripslashes($row['id']));
				$driverXML->addChild('firstname',stripslashes($row['firstname']));
				$driverXML->addChild('lastname',stripslashes($row['lastname']));
				$driverXML->addChild('emailid',stripslashes($row['emailid']));
				$from = new DateTime(stripslashes($row['dob']));
				$age = $this->calculateAge($from);
				$driverXML->addChild('age',$age);
				$driverXML->addChild('gender',stripslashes($row['gender']));
				$driverXML->addChild('address',stripslashes($row['address']));
			}
		}
		else {
			$driversXML->addChild('error','No Results');	
		}
		
		mysqli_close($dbconnect);
		
		echo $driversXML->asXML();
	}
	
	public function listdrivers()
	{
		include 'core/config.php';
		
		$dbconnect = new mysqli($sql_host, $sql_user, $sql_password, $sql_db);
		
		if (mysqli_connect_errno()) 
		{
			$this->returnerror("Database error");
		}
		
		$query = "SELECT rob_a_drivers.id,rob_a_drivers.latitude,rob_a_drivers.longitude,rob_a_drivers.status,rob_a_drivers.profilepic,rob_drivers.firstname,rob_drivers.lastname,rob_drivers.gender FROM rob_a_drivers,rob_drivers WHERE rob_a_drivers.id = rob_drivers.id";
		
		$result = $dbconnect->query($query) or die(($dbconnect->error.__LINE__) && $this->returnerror("SQL Error"));
		
		header('HTTP/1.1 200 Success');
		header('Content-Type: text/xml; charset=UTF-8');
		
		$driversXML = new SimpleXMLElement("<drivers></drivers>");
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$driverXML = $driversXML->addChild('driver');
				$driverXML->addChild('id',stripslashes($row['id']));
				$driverXML->addChild('firstname',stripslashes($row['firstname']));
				$driverXML->addChild('lastname',stripslashes($row['lastname']));
				$driverXML->addChild('latitude',stripslashes($row['latitude']));
				$driverXML->addChild('longitude',stripslashes($row['longitude']));
				$driverXML->addChild('status',stripslashes($row['status']));
				$driverXML->addChild('gender',stripslashes($row['gender']));
			}
		}
		else {
			$driversXML->addChild('error','No Results');	
		}
		
		mysqli_close($dbconnect);
		
		echo $driversXML->asXML();
	}
	
	public function locations()
	{
		include 'core/config.php';
		
		$dbconnect = new mysqli($sql_host, $sql_user, $sql_password, $sql_db);
		
		if (mysqli_connect_errno()) 
		{
			$this->returnerror("Database error");
		}
		
		$query = "SELECT rob_a_drivers.latitude,rob_a_drivers.longitude,rob_drivers.firstname,rob_drivers.lastname FROM rob_a_drivers,rob_drivers WHERE rob_a_drivers.id = rob_drivers.id AND rob_a_drivers.status='available'";
		
		$result = $dbconnect->query($query) or die(($dbconnect->error.__LINE__) && $this->returnerror("SQL Error"));
		
		header('HTTP/1.1 200 Success');
		header('Content-Type: text/xml; charset=UTF-8');
		
		$driversXML = new SimpleXMLElement("<drivers></drivers>");
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$driverXML = $driversXML->addChild('driver');
				$driverXML->addChild('firstname',stripslashes($row['firstname']));
				$driverXML->addChild('lastname',stripslashes($row['lastname']));
				$driverXML->addChild('latitude',stripslashes($row['latitude']));
				$driverXML->addChild('longitude',stripslashes($row['longitude']));
			}
		}
		else {
			$driversXML->addChild('error','No Results');	
		}
		
		mysqli_close($dbconnect);
		
		echo $driversXML->asXML();
		
	}
	
	public function getdriver($identifier)
	{
		include 'core/config.php';
		
		$dbconnect = new mysqli($sql_host, $sql_user, $sql_password, $sql_db);
		
		if (mysqli_connect_errno()) 
		{
			$this->returnerror("Database error");
		}
		
		$query = "SELECT * FROM rob_drivers WHERE id='".$identifier."'";
		
		$result = $dbconnect->query($query) or die(($dbconnect->error.__LINE__) && $this->returnerror("SQL Error"));
		
		header('HTTP/1.1 200 Success');
		header('Content-Type: text/xml; charset=UTF-8');
		
		$driverXML = new SimpleXMLElement("<driver></driver>");
		if($result->num_rows === 1) {
			while($row = $result->fetch_assoc()) {
				$driverXML->addChild('id',stripslashes($row['id']));
				$driverXML->addChild('firstname',stripslashes($row['firstname']));
				$driverXML->addChild('lastname',stripslashes($row['lastname']));
				$driverXML->addChild('emailid',stripslashes($row['emailid']));
				$from = new DateTime(stripslashes($row['dob']));
				$date = $from->format('d-m-Y');
				$driverXML->addChild('dob',$date);
				$driverXML->addChild('gender',stripslashes($row['gender']));
				$driverXML->addChild('address',stripslashes($row['address']));
				$driverXML->addChild('vechile',stripslashes($row['vechilereg']));
				$driverXML->addChild('license',stripslashes($row['licensenum']));
				$driverXML->addChild('handset',stripslashes($row['handset']));
				$driverXML->addChild('licensedoc',$row['licensedoc']);
				$driverXML->addChild('pandoc',$row['pandoc']);
				$driverXML->addChild('insurancedoc',$row['insurancedoc']);
				$driverXML->addChild('bikepicdoc',$row['bikepicdoc']);
				$driverXML->addChild('addressdoc',$row['addressdoc']);
			}
		}
		else {
			$driverXML->addChild('error','Driver Not Found');	
		}
		
		mysqli_close($dbconnect);
		
		echo $driverXML->asXML();
		
	}
	
	public function dologin()
	{
		echo "Loggin in...";
	}
	
	public function approvedriver($id)
	{
		include 'core/config.php';
		
		$password = $this->generatePassword();
		$date = new DateTime('today');
		
		$dbconnect = new mysqli($sql_host, $sql_user, $sql_password, $sql_db);
		
		if (mysqli_connect_errno()) 
		{
			$this->returnerror("Database error");
		}
		
		$query = sprintf("INSERT INTO rob_adrivers (id,password,dataArrivedTime) VALUES (%s,%s,$date)",
					   $this->GetSQLValueString($id, "int"),
					   $this->GetSQLValueString($password, "text"));
					   
		$result = $dbconnect->query($query) or die(($dbconnect->error.__LINE__) && $this->returnerror("SQL Error"));
		
		header('HTTP/1.1 200 Success');
		header('Content-Type: text/xml; charset=UTF-8');
		
		$driversXML = new SimpleXMLElement("<result></result>");
		
		$driversXML->addChild('status','approved');
		$driversXML->addChild('password',$password);
		
		mysqli_close($dbconnect);
		echo $driversXML->asXML();
	}
	
	public function generatePassword($length = 6)
	{
		$chars = 'bcdfghjkmnpqrstvwxyz0123456789';
		$count = mb_strlen($chars);
		
		for($i = 0,$result = '';$i < $length; $i++)
		{
			$index = rand(0,$count-1);
			$result .= mb_substr($chars, $index, 1);
		}
		
		return $result;
	}
	
	public function returnerror($errorstring)
	{
		header('HTTP/1.1 400 Bad Request');
		header('Content-Type: text/xml; charset=UTF-8');
		echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		echo("<error>".$errorstring."</error>");
		die();
	}
	
	private function verifyimage()
	{
		
		$imageflag = getimagesize($_FILES["license"]["tmp_name"]);
		if($imageflag === false)$this->returnerror("Invalid Image");
		$imageflag = getimagesize($_FILES["pancard"]["tmp_name"]);
		if($imageflag === false)$this->returnerror("Invalid Image");
		$imageflag = getimagesize($_FILES["insurance"]["tmp_name"]);
		if($imageflag === false)$this->returnerror("Invalid Image");
		$imageflag = getimagesize($_FILES["bikepic"]["tmp_name"]);
		if($imageflag === false)$this->returnerror("Invalid Image");
		$imageflag = getimagesize($_FILES["addressproof"]["tmp_name"]);
		if($imageflag === false)$this->returnerror("Invalid Image");
	}
	
	private function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	{
	  if (PHP_VERSION < 6) {
		$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
	  }

	  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

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
	
	private function calculateAge(DateTime $birthDate, DateTime $now = null) {
        if ($now == null) {
            $now = new DateTime;
        }

        $age = $now->format('Y') - $birthDate->format('Y');
        $dm = $now->format('m') - $birthDate->format('m');
        $dd = $now->format('d') - $birthDate->format('d');

        if ($dm < 0 || ($dm == 0 && $dd < 0)) {
            $age--;
        }

        return $age;
    }
}
?>