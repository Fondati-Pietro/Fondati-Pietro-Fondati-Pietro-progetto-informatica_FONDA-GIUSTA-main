<?php
session_start();
include 'config.php';

// Verifica se l'utente è loggato e che $_SESSION['user'] sia un array
if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Controlla se l'utente esiste nella tabella utenti
$stmt = $conn->prepare("SELECT id FROM utenti WHERE id = ?");
$stmt->execute([$user_id]);
if ($stmt->rowCount() == 0) {
    die("Errore: L'utente non esiste nel database.");
}

// Gestione dell'aggiunta del saldo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['importo'])) {
    $importo = floatval($_POST['importo']);

    if ($importo <= 0) {
        $errore = "L'importo deve essere maggiore di zero";
    } else {
        try {
            // Controlla se esiste già un saldo
            $stmt = $conn->prepare("SELECT id FROM saldo WHERE id = ?");
            $stmt->execute([$user_id]);

            if ($stmt->rowCount() > 0) {
                // Aggiorna il saldo esistente
                $sql = "UPDATE saldo SET saldo = saldo + ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$importo, $user_id]);
            } else {
                // Crea un nuovo record (ordine corretto dei parametri)
                $sql = "INSERT INTO saldo (id, saldo) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$user_id, $importo]);
            }

            $successo = "Saldo aggiunto con successo!";
        } catch (PDOException $e) {
            $errore = "Errore: " . $e->getMessage();
        }
    }
}

// Recupera il saldo attuale
$saldo = 0;
$stmt = $conn->prepare("SELECT saldo FROM saldo WHERE id = ?");
$stmt->execute([$user_id]);
if ($stmt->rowCount() > 0) {
    $saldo = $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Saldo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4bb543;
            --error-color: #ff6b6b;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
         
        }
        </style>
    
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-black navbar-dark navbar-center">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="negozio.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="carrello.php">Il tuo carrello</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="saldo.php">Aggiungi saldo al conto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="aggiungi_prodotto.php">Aggiungi prodotto</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="text-center">Il Tuo Saldo: €<?= number_format($saldo, 2) ?></h3>
                    </div>
                    <div class="card-body">
                        <?php if(isset($errore)): ?>
                        <div class="alert alert-danger"><?= $errore ?></div>
                        <?php endif; ?>

                        <?php if(isset($successo)): ?>
                        <div class="alert alert-success"><?= $successo ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label for="importo" class="form-label">Importo da Aggiungere</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input type="number" class="form-control" id="importo" name="importo" min="0.01"
                                        step="0.01" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Aggiungi Saldo</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>