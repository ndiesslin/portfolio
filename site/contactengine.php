<?php

$EmailFrom = "nicholas@ndiesslin.com";
$EmailTo = "nicholasdiesslin@gmail.com";
$Subject = "Email From ndiesslin.com";

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
if (!$email) {
	// return with error
}
$name = trim(stripslashes($_POST['name'])); 
$email = trim(stripslashes($_POST['email'])); 
$message = trim(stripslashes($_POST['message'])); 

// validation
/*$validationOK=true;
if (!$validationOK) {
  print "<meta http-equiv=\"refresh\" content=\"0;URL=error.htm\">";
  exit;
}*/

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
  print "<meta http-equiv=\"refresh\" content=\"0;URL=index.html\">";
}
else{
  print "<meta http-equiv=\"refresh\" content=\"0;URL=error.htm\">";
}
?>