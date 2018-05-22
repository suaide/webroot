<?php
session_start();
include("conf.php");
include("checa.php");


$EmailFrom = Trim(stripslashes($_POST['EmailFrom'])); 
$EmailTo = $ADMIN;
$Subject = "[WEBROOT] ".Trim(stripslashes($_POST['Subject']));;
$Name = Trim(stripslashes($_POST['Name'])); 
$Message = Trim(stripslashes($_POST['Message'])); 

// validation
$validationOK=true;

if (Trim($EmailFrom)=="") $validationOK=false;
if (Trim($Name)=="")      $validationOK=false;
if (Trim($Subject)=="")   $validationOK=false;
if (!$validationOK) {
  header("Location: index.php?action=contacta&err=vazio");
    exit;
}
else
{

// prepare email body text
$Body = "";
$Body .= "Nome: ";
$Body .= $Name;
$Body .= "\n\n";
$Body .= "Assunto: ";
$Body .= $Subject;
$Body .= "\n\n";
$Body .= "Texto: ";
$Body .= $Message;
$Body .= "\n";

// send email 
$success = mail($EmailTo, $Subject, $Body, "From: <$EmailFrom>");

// redirect to success page 
if ($success){
  header("Location: index.php?action=contacta&err=ok");
}
else{
  header("Location: index.php?action=contacta&err=email");
}
}
?>
