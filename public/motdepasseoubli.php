<html>  
<head>  
    <title>Réinitialisation du Mot de passe</title>  
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
</head>
<style>
 .box
 {
  width:100%;
  max-width:600px;
  background-color:#f9f9f9;
  border:1px solid #ccc;
  border-radius:5px;
  padding:16px;
  margin:0 auto;
 }
 input.parsley-success,
 select.parsley-success,
 textarea.parsley-success {
   color: #468847;
   background-color: #DFF0D8;
   border: 1px solid #D6E9C6;
 }

 input.parsley-error,
 select.parsley-error,
 textarea.parsley-error {
   color: #B94A48;
   background-color: #F2DEDE;
   border: 1px solid #EED3D7;
 }

 .parsley-errors-list {
   margin: 2px 0 3px;
   padding: 0;
   list-style-type: none;
   font-size: 0.9em;
   line-height: 0.9em;
   opacity: 0;

   transition: all .3s ease-in;
   -o-transition: all .3s ease-in;
   -moz-transition: all .3s ease-in;
   -webkit-transition: all .3s ease-in;
 }

 .parsley-errors-list.filled {
   opacity: 1;
 }
 
 .parsley-type, .parsley-required, .parsley-equalto{
  color:#ff0000;
 }
.error
{
  color: red;
  font-weight: 700;
} 
</style>
<?php
include_once("../actions/db.php");
include_once("../src/App/Users.php");

$email='';
if(isset($_REQUEST['pwdrst']))
{ 
  $email=($_REQUEST['email']);
  $pseudo = $_REQUEST['pseudo'];
  $check_pseudo = $db->prepare("SELECT pseudo FROM users WHERE pseudo='$pseudo'");
  $check_pseudo->execute();
  $Pseudoinfo=$check_pseudo->fetch();



  if($Pseudoinfo>0)
  {
    if($_SERVER["HTTP_HOST"]=='localhost:8888'):
      $message = '<div>
      <p><b>Bonjour!</b></p>
      <p>Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation du mot de passe pour votre compte.</p>
      <br>
      <p><button class="btn btn-primary"><a href="http://localhost:8888/benevoles-ressource-brie/public/reset-password.php?secret='.base64_encode($pseudo).'">Cliquer ici pour reinitialiser votre mot de passe</a></button></p>
      <br>
      </div>';
    else:
      $message = '<div>
      <p><b>Bonjour!</b></p>
      <p>Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation du mot de passe pour votre compte.</p>
      <br>
      <p><button class="btn btn-primary"><a href="http://benevoles.ressourcebrie.fr/public/reset-password.php?secret='.base64_encode($pseudo).'">Cliquer ici pour reinitialiser votre mot de passe</a></button></p>
      <br>
      </div>';
    endif;

include_once ("../PHPMailer/src/Exception.php");
include_once("../PHPMailer/src/PHPMailer.php");
include_once("../PHPMailer/src/SMTP.php");
$email = $email; 
$mail = new PHPMailer\PHPMailer\PHPMailer();//true
$mail->IsSMTP();
//$mail->SMTPDebug = 2;
$mail->SMTPAuth = true;                 
$mail->SMTPSecure = "ssl";  //ssl    
$mail->Host='smtp.ouvaton.coop';
$mail->Port = 465; 
$mail->Username = 'president@ressourcebrie.fr';   //Entrez votre email
$mail->Password = 'President7#';   //Entrez votre mot de passe
$mail->FromName = "ressourcebrie";
$mail->From='president@ressourcebrie.fr'; //Re-entrez votre email
$mail->AddAddress($email);
$mail->Subject = "Réinitialisation du Mot de passe";
$mail->isHTML( TRUE );
$mail->Body =$message;
if($mail->send())
{
  $msg = "Nous avons envoyé un lien de réinitialisation de votre mot de passe par e-mail!";
}
}
else
{
  $msg = "Nous ne trouvons pas d'utilisateur avec cette pseudo";
}
}

?>
<body>
<div class="container">  
    <div class="table-responsive">  
    <h3 align="center">Réinitialisation du Mot de passe</h3><br/>
    <div class="box">
     <form id="validate_form" method="post" >  
       <div class="form-group">
       <label for="pseudo">Pseudo </label>
       <input type="text" name="pseudo" id="pseudo" placeholder="Entrer votre pseudo" required data-parsley-type="email" data-parsley-trigg
       er="keyup" class="form-control" /> 
       <label for="email">Email </label>
       <input type="text" name="email" id="email" placeholder="Entrer votre email" required 
       data-parsley-type="email" data-parsley-trigg
       er="keyup" class="form-control" />
      </div>
      <div class="form-group">
       <input type="submit" id="login" name="pwdrst" value="Envoyer le lien de réinitialisation du mot de passe" class="btn btn-success" />
       </div>
       
       <p class="error"><?php if(!empty($msg)){ echo $msg; } ?></p>
     </form>


     </div>
   </div>  
  </div>
</body>
</html>