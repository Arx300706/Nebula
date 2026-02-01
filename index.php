<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NebulaStore - Accueil</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container auth-container">
        <h1><i class="fa-solid fa-cloud"></i> NebulaStore</h1>

        <div id="view-landing" class="view-section">
            <div class="buttons">
                <button id="btn-register" class="btn btn-primary">Créer un compte</button>
                <button id="btn-login" class="btn btn-outline">Se connecter</button>
            </div>
        </div>

        <div id="view-register" class="view-section hidden">
            <h2>Créer un compte</h2>
            <form id="form-register">
                <input type="text" id="reg-name" placeholder="Nom complet" required><br>
                <input type="email" id="reg-email" placeholder="Email" required><br>
                <input type="password" id="reg-pass" placeholder="Mot de passe" required><br>
                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </form>
            <button id="back-register" class="btn-link">Retour</button>
        </div>

        <div id="view-login" class="view-section hidden">
            <h2>Se connecter</h2>
            <form id="form-login">
                <input type="email" id="login-email" placeholder="Email" required><br>
                <input type="password" id="login-pass" placeholder="Mot de passe" required><br>
                <button type="submit" class="btn btn-primary">Connexion</button>
            </form>
            <button id="back-login" class="btn-link">Retour</button>
        </div>
    </div>

    <script src="/js/app.js"></script>
</body>
</html>