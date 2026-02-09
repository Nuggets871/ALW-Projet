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
    <style>
        /*
            Palette :
            main blue #0bbbef
            dark blue #0086b7
            light blue #c7f8ff
            brown #783300
        */
        :root {
            color-scheme: dark;
            background-color: #333;
            font-family: sans;
        }
        a, a:visited {
            color: #783300;
        }
        code, pre {
            color: #4AF626;
            background-color: black;
            font-weight: bold;
            padding: .1em .3em;
            border-radius: .3em;
        }
        .loginForm {
            background-color: black;
            width: 25em;
            border: .1em solid #0086b7;
            border-radius: .5em;
            margin: 2em auto;
            padding: 1em;
        }
        h1, h2, h3, h4, h5, h6 {
            color: #0bbbef;
        }
        form h2 {
            margin: 0 0 .5em;
        }
        form button {
            margin: .5em 0 0;
        }
        input {
            display: block;
            width: 100%;
            box-sizing: border-box;
            margin: .5em 0;
            font-size: 1em;
        }
        button {
            font-size: 1em;
        }
        .error {
            border-radius: .2em;
            background-color: darkred;
            padding: 0 .5em;
            font-style: italic;
        }
        .error::before {
            content: "⚠ ";
            font-weight: bold;
        }
        .loggedIn {
            background-color: #F7F7F7;
            border-radius: 5px;
            width: 20%;
            color: black;
            padding: 2rem;
            margin: auto auto;
        }
    </style>
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