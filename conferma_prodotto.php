<?php
session_start();
include 'config.php';

// Mostra errori PDO per il debug
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Controlla che l'utente sia loggato e sia amministratore
if (!isset($_SESSION['user']) || $_SESSION['user']['ruolo'] !== 'amministratore') {
    header("Location: login.php");
    exit();
}

// Messaggio di stato per feedback
$alert = ['type' => '', 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['prodotto_id'])) {
    $prodotto_id = $_POST['prodotto_id'];

    // Recupera il prodotto temporaneo dal database
    $stmt = $conn->prepare("SELECT * FROM prodotti_temp WHERE id = ?");
    if (!$stmt->execute([$prodotto_id])) {
        $alert = ['type' => 'danger', 'message' => 'Errore nel recupero del prodotto'];
    } else {
        $prodotto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($prodotto) {
            // Avvia transazione per operazioni atomiche
            $conn->beginTransaction();

            try {
                // Se è stato premuto "Conferma"
                if (isset($_POST['conferma'])) {
                    $insert = $conn->prepare("INSERT INTO prodotti (nome, descrizione, prezzo, quantita_disponibile, categoria_id) 
                                              VALUES (?, ?, ?, ?, ?)");
                    if (!$insert->execute([
                        $prodotto['nome'],
                        $prodotto['descrizione'],
                        $prodotto['prezzo'],
                        $prodotto['quantita_disponibile'],
                        $prodotto['categoria_id']
                    ])) {
                        throw new Exception("Errore nell'inserimento del prodotto");
                    }

                    $new_id = $conn->lastInsertId();

                    // Inserisce l'immagine associata, se presente
                    if (!empty($prodotto['immagine_url'])) {
                        $img = $conn->prepare("INSERT INTO immagini_prodotti (prodotto_id, immagine_url) VALUES (?, ?)");
                        if (!$img->execute([$new_id, $prodotto['immagine_url']])) {
                            throw new Exception("Errore nell'inserimento dell'immagine");
                        }
                    }

                    $alert = ['type' => 'success', 'message' => 'Prodotto aggiunto al negozio!'];
                } 
                // Se è stato premuto "Annulla"
                elseif (isset($_POST['annulla'])) {
                    $alert = ['type' => 'warning', 'message' => 'Prodotto rifiutato e rimosso'];
                }

                // Elimina comunque il prodotto dalla tabella temporanea
                $delete = $conn->prepare("DELETE FROM prodotti_temp WHERE id = ?");
                if (!$delete->execute([$prodotto_id])) {
                    throw new Exception("Errore nell'eliminazione del prodotto temporaneo");
                }

                $conn->commit();
            } catch (Exception $e) {
                $conn->rollBack(); // Annulla tutte le modifiche in caso di errore
                $alert = ['type' => 'danger', 'message' => 'Errore: ' . $e->getMessage()];
            }
        } else {
            $alert = ['type' => 'danger', 'message' => 'Prodotto non trovato'];
        }
    }
}

// Recupera tutti i prodotti in attesa di approvazione
$stmt = $conn->prepare("SELECT * FROM prodotti_temp");
$stmt->execute();
$prodotti_da_approvare = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Conferma Prodotti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); }
        .product-card { transition: all 0.3s ease; }
        .fade-out { opacity: 0; height: 0; overflow: hidden; margin: 0; padding: 0; }
        .alert-fixed { position: fixed; top: 20px; right: 20px; z-index: 1000; min-width: 300px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <div class="navbar-nav">
            <a class="nav-link" href="negozio.php">Torna al negozio</a>
        </div>
    </div>
</nav>

<div class="container">
    <!-- Mostra eventuali messaggi di alert -->
    <?php if ($alert['message']): ?>
        <div class="alert alert-<?= $alert['type'] ?> alert-fixed alert-dismissible fade show" role="alert">
            <?= $alert['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <h2 class="mb-4">Prodotti in attesa di approvazione</h2>

    <!-- Se non ci sono prodotti da approvare -->
    <?php if (empty($prodotti_da_approvare)): ?>
        <div class="alert alert-info">Nessun prodotto in attesa di approvazione</div>
    <?php else: ?>
        <!-- Elenco prodotti in schede -->
        <div class="row" id="products-container">
            <?php foreach ($prodotti_da_approvare as $prodotto): ?>
                <div class="col-md-4 mb-4 product-card" id="product-<?= $prodotto['id'] ?>">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($prodotto['immagine_url']) ?>" class="card-img-top"
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($prodotto['nome']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($prodotto['descrizione']) ?></p>
                            <div class="mt-auto">
                                <p class="card-text"><strong>Prezzo:</strong> <?= number_format($prodotto['prezzo'], 2) ?>€</p>
                                <p class="card-text"><strong>Quantità:</strong> <?= $prodotto['quantita_disponibile'] ?></p>
                                <!-- Form per confermare o rifiutare -->
                                <form method="post" class="d-flex justify-content-between product-form"
                                      data-product-id="<?= $prodotto['id'] ?>">
                                    <input type="hidden" name="prodotto_id" value="<?= $prodotto['id'] ?>">
                                    <button type="submit" name="conferma" class="btn btn-success">Conferma</button>
                                    <button type="submit" name="annulla" class="btn btn-danger">Annulla</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Aggiunge evento a ogni form di prodotto
        document.querySelectorAll('.product-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Impedisce il submit classico

                const formData = new FormData(this);
                const button = e.submitter; // Ottiene il bottone cliccato
                const productId = this.dataset.productId;
                const productCard = document.getElementById(`product-${productId}`);

                // Aggiunge il nome del bottone al formData
                if (button && button.name) {
                    formData.append(button.name, true);
                }

                // Anima la rimozione della card
                productCard.classList.add('fade-out');

                // Dopo l'animazione, invia il form via fetch e ricarica la pagina
                setTimeout(() => {
                    fetch('', {
                        method: 'POST',
                        body: formData
                    }).then(() => location.reload())
                      .catch(error => console.error('Errore:', error));
                }, 300);
            });
        });

        // Chiude gli alert dopo 3 secondi
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.classList.remove('show');
                alert.classList.add('fade');
            });
        }, 3000);
    });
</script>
</body>
</html>
