<?php

$EmailFrom = "chriscoyier@gmail.com";
$EmailTo = "CHANGE-THIS@YOUR-DOMAIN.com";
$Subject = "Email From ndiesslin.com";
$Name = Trim(stripslashes($_POST['Name'])); 
$Tel = Trim(stripslashes($_POST['Tel'])); 
$Email = Trim(stripslashes($_POST['Email'])); 
$Message = Trim(stripslashes($_POST['Message'])); 

// validation
$validationOK=true;
if (!$validationOK) {
  print "<meta http-equiv=\"refresh\" content=\"0;URL=error.htm\">";
  exit;
}

// prepare email body text
$Body = "";
$Body .= "name: ";
$Body .= $name;
$Body .= "\n";
$Body .= "email: ";
$Body .= $email;
$Body .= "\n";
$Body .= "message: ";
$Body .= $message;
$Body .= "\n";

// send email 
$success = mail($EmailTo, $Subject, $Body, "From: <$EmailFrom>");

// redirect to success page 
if ($success){
  print "<meta http-equiv=\"refresh\" content=\"0;URL=contactthanks.php\">";
}
else{
  print "<meta http-equiv=\"refresh\" content=\"0;URL=error.htm\">";
}
?>