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

function normalizePseudo(string $pseudo): string
{
  $pseudo = mb_strtolower(trim($pseudo), 'UTF-8');
  $translit = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $pseudo);

  if ($translit !== false) {
    $pseudo = $translit;
  }

  return preg_replace('/[^a-z0-9]/', '', $pseudo) ?? '';
}

$pseudo = '';
$email = '';
if (isset($_REQUEST['pwdrst']))
{ 
  $pseudo = trim($_REQUEST['pseudo'] ?? '');
  $email = trim($_REQUEST['email'] ?? '');
  $pseudoNormalise = normalizePseudo($pseudo);

  if ($pseudoNormalise === '') {
    $msg = "Veuillez saisir votre pseudo";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $msg = "Veuillez saisir une adresse e-mail valide";
  } else {
  $check_pseudo = $db->prepare("SELECT pseudo_normalise FROM users WHERE pseudo_normalise = :pseudo_normalise");
  $check_pseudo->execute([
    ':pseudo_normalise' => $pseudoNormalise,
  ]);
  $Pseudoinfo = $check_pseudo->fetch();



  if ($Pseudoinfo) {
    if ($_SERVER["HTTP_HOST"] == 'localhost:8888') {
        $url = 'http://localhost:8888/benevoles-ressource-brie/reset-password.php?secret=' . urlencode(base64_encode($Pseudoinfo['pseudo_normalise']));
    } else {
        $url = 'https://benevoles.ressourcebrie.fr/reset-password.php?secret=' . urlencode(base64_encode($Pseudoinfo['pseudo_normalise']));
    }

    $message = '
    <div>
      <p><b>Bonjour !</b></p>
      <p>Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation du mot de passe pour votre compte.</p>
      <br>
      <p>
        <a href="' . $url . '" 
           style="display:inline-block;padding:12px 18px;background:#0d6efd;color:#ffffff;text-decoration:none;border-radius:6px;">
           Cliquer ici pour réinitialiser votre mot de passe
        </a>
      </p>
      <br>
    </div>';
include_once ("../PHPMailer/src/Exception.php");
include_once("../PHPMailer/src/PHPMailer.php");
include_once("../PHPMailer/src/SMTP.php");
$smtpHost = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
$smtpPort = (int)($_ENV['MAIL_PORT'] ?? 465);
$smtpSecure = $_ENV['MAIL_ENCRYPTION'] ?? 'ssl';
$smtpUser = $_ENV['MAIL_USERNAME'] ?? '';
$smtpPassword = $_ENV['MAIL_PASSWORD'] ?? '';
$fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? $smtpUser;
$fromName = $_ENV['MAIL_FROM_NAME'] ?? 'ressourcebrie';

if ($smtpUser === '' || $smtpPassword === '' || $fromAddress === '') {
  $msg = "La configuration d'envoi d'e-mail est incomplète";
} else {
  try {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = $smtpSecure;
    $mail->Host = $smtpHost;
    $mail->Port = $smtpPort;
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPassword;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom($fromAddress, $fromName);
    $mail->addAddress($email);
$mail->Subject = "Réinitialisation du Mot de passe";
    $mail->isHTML(true);
    $mail->Body = $message;
    $mail->send();
  $msg = "Nous avons envoyé un lien de réinitialisation de votre mot de passe par e-mail!";
  } catch (PHPMailer\PHPMailer\Exception $e) {
    error_log('Erreur envoi e-mail mot de passe oublie : ' . ($mail->ErrorInfo ?: $e->getMessage()));
    $msg = "Erreur lors de l'envoi de l'e-mail de réinitialisation";
  }
}
}
else
{
  $msg = "Nous ne trouvons pas d'utilisateur avec ce pseudo";
}
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
       <input type="text" name="pseudo" id="pseudo" placeholder="Entrer votre pseudo"
       value="<?php echo htmlspecialchars($pseudo, ENT_QUOTES, 'UTF-8'); ?>" required
       data-parsley-trigger="keyup" class="form-control" />
       <label for="email">Email </label>
       <input type="email" name="email" id="email" placeholder="Entrer votre email"
       value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required
       data-parsley-type="email" data-parsley-trigger="keyup" class="form-control" />
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
