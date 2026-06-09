<html>  
<head>  
    <title>Réinitialisez votre mot de passe</title>  
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
include_once('../actions/db.php');

$secret = $_GET['secret'] ?? '';
$pseudoNormalise = base64_decode($secret, true);
$pseudoNormalise = is_string($pseudoNormalise) ? $pseudoNormalise : '';

if(isset($_REQUEST['pwdrst']))
{
  $pwd = password_hash($_REQUEST['pwd'], PASSWORD_DEFAULT);
  $cpwd = $_REQUEST['cpwd'];

  if(password_verify($cpwd,$pwd))
  {
    $reset_pwd = $db->prepare("UPDATE users SET password = :password WHERE pseudo_normalise = :pseudo_normalise");
    $reset_pwd->execute([
      ':password' => $pwd,
      ':pseudo_normalise' => $pseudoNormalise,
    ]);
  
    if($reset_pwd->rowCount() > 0)
    {
      $msg1= 'Votre mot de passe a été modifié avec succès <a href="login.php"> Cliquez ici</a> pour se connecter';
    }
    else
    {
      $msg= 'Erreur lors de la modification du mot de passe';
    }
  }
  else
  {
    $msg= 'Les mot de passes ne correspondent pas';
  }
}

if($pseudoNormalise !== '')
{
  $check_details = $db->prepare("SELECT 1 FROM users WHERE pseudo_normalise = :pseudo_normalise");
  $check_details->execute([
    ':pseudo_normalise' => $pseudoNormalise,
  ]);
  $res = $check_details->fetch();

  if($res)
  {?>
  


<body>
<div class="container">  
    <div class="table-responsive">  
    <h3 align="center">Réinitialisation du Mot de passe</h3><br/>
     <form id="validate_form" method="post" >  
      <div class="form-group">
       <label for="pwd">Mot de passe</label>
       <input type="password" name="pwd" id="pwd" placeholder="Entrer votre nouveau mot de passe" required 
       data-parsley-type="pwd" data-parsley-trigg
       er="keyup" class="form-control"/>
      </div>
      <div class="form-group">
       <label for="cpwd">Confirmer votre mot de passe</label>
       <input type="password" name="cpwd" id="cpwd" placeholder="Confirmer votre mot de passe" required data-parsley-type="cpwd" data-parsley-trigg
       er="keyup" class="form-control"/>
      </div>
      <div class="form-group">
       <input type="submit" id="login" name="pwdrst" value="Modifier le mot de passe" class="btn btn-success" />
       </div>
     </form>
     </div>
   </div>  
  </div>
  <?php if(isset($msg)):
  ?>
  <div class="alert alert-danger" role="alert">
    <?=$msg;?>
  </div>
  <?php
  endif;
  if(isset($msg1)):
  ?>
  <div class="alert alert-success" role="alert">
    <?=$msg1;?>
  </div>
  <?php
  endif;
  ?>
  <?php } } ?> 
</body>
</html>
