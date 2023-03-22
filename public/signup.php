<?php require("../actions/users/signupaction.php");
require '../src/bootstrap.php';
entete('Signup','Signup','0');
?>

        
        
        <div class="container text-center">
            <h1>Formulaire d'inscription</h1>
            
            <form method="post">
                
                <?php if(isset($errorMsg)){echo '<p>'.$errorMsg.'<p>';}?>
                
                <fieldset>
                    
                    <div class="mb-3 w-25 m-auto">
                    <label for="prenom" class="form-label">Prénom : </label>
                    <input type="text" name="prenom" class="form-control">
                    </div>
            
                    <div class="mb-3 w-25 m-auto">
                    <label for="nom" class="form-label">Nom : </label>
                    <input type="text" name="nom" class="form-control">
                    </div>
            
                    <div class="mb-3 w-25 m-auto">
                    <label for="pseudo" class="form-label">Pseudo : </label>
                    <input type="text" name="pseudo" class="form-control">
                    </div>
                    
                    <div class="mb-3 w-25 m-auto">
                    <label for="password" class="form-label">Mot de passe : </label>
                    <input type="password" name="password" class="form-control">
                    </div>
                
                </fieldset>
            
                <button type="submit" name="validate" class="mt-3 btn btn-outline-primary btn-custom">S'inscrire</button>
                
            </form>
            
            <a href="login.php" style="color: black"><p>J'ai déjà un compte, je me connecte ici !</p></a>
            
        </div>
        
        <?php include('../includes/footer.php');?>
    </body>
</html>