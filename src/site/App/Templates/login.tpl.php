<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>

    <link rel="stylesheet" href="Public/style.css">
</head>
<body>
<?php if(!isset($_SESSION['user'])):  ?>
<form action="" method="POST" class="loginForm">
    <h2>Connexion</h2>
    <input type="text" name="username" placeholder="Nom d'utilisateur" required autocomplete="off">
    <input type="password" name="password" placeholder="Mot de passe" required autocomplete="off">

    <?php if (!empty($error)) { ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <button type="submit">Se connecter</button>
</form>
<?php else: ?>
<div class="loggedIn">
    <span> Connexion réussie !</span>
    <br>
    <a href="<?php echo $this->buildRoute('/dashboard'); ?>">
        <button >Allez au tableau de bord</button>
    </a>
</div>
<?php endif; ?>
</body>
</html>
