<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Negozio Online</title>
    <link rel="Website Icon" type="image/png" href="./images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body id="body-index">
    <div id="container-index">
        <div class="container text-center">
            <h1 class="display-4 mb-3">Benvenuto nel Negozio Online</h1>
            <p class="lead text-muted">Trova i migliori prodotti a prezzi convenienti.</p>

            <div class="logo-wrapper">
                <img src="./images/logo.png" class="logo img-fluid">
            </div>

            <div class="d-flex justify-content-center mt-4 flex-wrap">
                <a href="login.php" class="btn btn-primary mx-2 my-1">Accedi</a>
                <a href="register.php" class="btn btn-success mx-2 my-1">Registrati</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Ol3HoJqUtYdSRaZqBAlmPsTmF2+E34I37v0HidUkPjzjJ7oF47r5xxWry1yftxF5" crossorigin="anonymous">
    </script>
</body>

</html>
