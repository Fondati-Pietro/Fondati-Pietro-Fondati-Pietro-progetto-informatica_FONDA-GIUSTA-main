<?php
include 'config.php'; 
session_start();

// Recupera il carrello dalla sessione (o array vuoto se non esiste)
$carrello = $_SESSION['carrello'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/style.css">
    <title>Conferma Acquisto</title>
</head>

<body>


    <nav class="navbar navbar-expand-lg bg-black navbar-dark navbar-center">
        <div class="container">
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="negozio.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="carrello.php">Il tuo carrello</a></li>
                    <li class="nav-item"><a class="nav-link" href="saldo.php">Aggiungi saldo al conto</a></li>
                    <li class="nav-item"><a class="nav-link" href="aggiungi_prodotto.php">Aggiungi prodotto</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <?php 
    $user_id = $_SESSION['user']['id'] ?? null;
    $prezzoTot = 0;

    // Calcola il totale del carrello
    if (count($carrello) > 0) {   
        foreach ($carrello as $item) {
            $prezzoTot += $item['prezzo']; 
        }
    }

    // Ottieni il saldo utente
    $saldo = 0;
    $stmt = $conn->prepare("SELECT saldo FROM saldo WHERE id = ?");
    $stmt->execute([$user_id]);
    if ($stmt->rowCount() > 0) {
        $saldo = $stmt->fetchColumn();
    }

    $contoFinale = $saldo - $prezzoTot;

    // Controlla se l'acquisto è possibile
if ($contoFinale >= 0) {
    // Aggiorna il saldo
    $sql = "UPDATE saldo SET saldo = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$contoFinale, $user_id]);

    // Inserisci l'ordine nella tabella ordini
    $quantita_totale = count($carrello); // oppure somma delle quantità se ne prevedi
    $stmt = $conn->prepare("INSERT INTO ordini (utente_id, totale, quantita) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $prezzoTot, $quantita_totale]);
    $ordine_id = $conn->lastInsertId();

    // Inserisci ogni prodotto nel carrello nella tabella dettagli_ordine
    $stmt = $conn->prepare("INSERT INTO dettagli_ordine (ordine_id, prodotto_id, quantita, prezzo) VALUES (?, ?, ?, ?)");
    foreach ($carrello as $item) {
        $stmt->execute([
            $ordine_id,
            $item['id'],
            $item['quantita'] ?? 1,
            $item['prezzo']
        ]);
    }

    // Svuota il carrello
    unset($_SESSION['carrello']);

    // Messaggio di successo
    echo "<center>";
    echo "<div class='alert alert-success'>";
    echo "<h4>Acquisto effettuato con successo!</h4>";
    echo "<p>Il tuo saldo rimanente è di: <strong>€" . number_format($contoFinale, 2, ',', '.') . "</strong></p>";
    echo "</div>";
    echo "</center>";

    // Optional: header("refresh:3;url=grazie.php");
} else {
    echo "<center>";
    echo "<div class='alert alert-danger'>";
    echo "<h4>Saldo insufficiente</h4>";
    echo "<p>Non è stato possibile completare l'acquisto.</p>";
    echo "</div>";
    echo "</center>";
}
    ?>
    <footer class="bg-dark text-white py-3 mt-5">
        <div class="container text-center bottom-0">
            <p>&copy; 2025 Negozio Online. Tutti i diritti riservati.</p>
        </div>
    </footer>

</body>

</html>