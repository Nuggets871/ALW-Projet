<?php

require_once "Utils/User.php";
require_once "Utils/FileStorage.php";
require_once "Utils/UserRepository.php";

session_start();
$error = null;

$repo = new UserRepository("Data/users.json");
// exemples d'utilisation :
// $user = $repo->get($login);
// $users = $repo->getAll();

// var_dump($_SERVER);
// var_dump($_SERVER['REQUEST_METHOD']);

// TODO: gérer ici la connexion lors de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = $repo->get($username) ?? '';

    if ($user && password_verify($password, $user->password_hash)) {
        $_SESSION['user']=$user;

        $saveFile = 'Data/Saves/' . $user->login . '.json';
        if (!file_exists($saveFile)) {
            copy('Data/Config/save_initial.json', $saveFile);
        }

        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Identifiants incorrect';
    }
}

?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <a href="dashboard.php">
            <button >Go to dashboard</button>
        </a>
    </div>

    <?php endif ?>
</body>
</html>