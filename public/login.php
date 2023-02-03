<?php
require('../src/config.php');
require('../actions/users/loginAction.php');
require '../src/bootstrap.php';
entete('Login','Login','0');
?>
        
<div class="container text-center">
        
      
        <h1>Formulaire de connexion</h1>
        
        <?php if(isset($errorMsg)){echo '<p>'.$errorMsg.'<p>';}?>
        
            <form method="post">
                    <div class="mb-3 w-25 m-auto">
                      <label for="pseudo" class="form-label">Pseudo</label>
                      <input type="text" name="pseudo" class="form-control" id="InputPseudo" aria-describedby="emailHelp">
                      
                    </div>
                    <div class="mb-3 w-25 m-auto">
                      <label for="password" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="InputPassword1">
                    </div>
                    
                    <button type="submit" name="validate" class="stdbouton">Connexion</button>
            </form>
                
        <a href="signup.php" style="color: black"><p>Je n'ai pas encore de compte, je m'inscris ici !</p></a>
                          
       
</div> 
        
        <?php require '../includes/footer.php'; ?>