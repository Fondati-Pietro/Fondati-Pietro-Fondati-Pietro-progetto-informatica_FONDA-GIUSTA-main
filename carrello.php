<?php
session_start();
$carrello = $_SESSION['carrello'] ?? [];

// Controlla se è stata inviata una richiesta per rimuovere un prodotto
if (isset($_POST['rimuovi']) && isset($_POST['id_prodotto'])) {
    $id_prodotto = $_POST['id_prodotto'];
    
    // Decrementa la quantità
    if (isset($carrello[$id_prodotto])) {
        if ($carrello[$id_prodotto]['quantita'] > 1) {
            $carrello[$id_prodotto]['quantita'] -= 1;
        } else {
            unset($carrello[$id_prodotto]);
        }
        $_SESSION['carrello'] = $carrello; // Aggiorna la sessione
    }
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Carrello</title>
    <link rel="stylesheet" href="./css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

    <div class="container mt-4">
        <h2>Prodotti nel tuo carrello</h2>
        <div class="row">
            <?php if (count($carrello) > 0): ?>
            <?php
            $totale_carrello = 0;
            foreach ($carrello as $id => $item):
                $totale_prodotto = $item['prezzo'] * $item['quantita'];
                $totale_carrello += $totale_prodotto;
            ?>
            <div class="col-md-4 mb-4">
                <div class="card" style="width: 18rem;">
                    <?php if (!empty($item['immagine_url'])): ?>
                    <img src="<?php echo htmlspecialchars($item['immagine_url']); ?>" class="card-img-top"
                        alt="<?php echo htmlspecialchars($item['nome']); ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($item['nome']); ?></h5>
                        <p class="card-text">Prezzo unitario: <?php echo number_format($item['prezzo'], 2); ?>€</p>
                        <p class="card-text">Quantità: <?php echo $item['quantita']; ?></p>
                        <p class="card-text"><strong>Totale: <?php echo number_format($totale_prodotto, 2); ?>€</strong></p>

                        <!-- Form per rimuovere una unità del prodotto dal carrello -->
                        <form action="carrello.php" method="POST">
                            <input type="hidden" name="id_prodotto" value="<?php echo $id; ?>">
                            <button type="submit" name="rimuovi" class="btn btn-warning">Rimuovi</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <p>Il tuo carrello è vuoto.</p>
            <?php endif; ?>
        </div>

        <?php if (count($carrello) > 0): ?>
        <div class="mt-4 text-center">
            <h4>Totale Carrello: <?php echo number_format($totale_carrello, 2); ?>€</h4>
        </div>
        <form action="acquisto.php" method="POST" class="mt-3">
            <input type="submit" value="Paga" class="btn btn-primary w-100">
        </form>
        <?php endif; ?>
    </div>
</body>

</html>
