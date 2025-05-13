<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';


$search = $_GET['search'] ?? ''; // Inizializza la variabile di ricerca
$stmt = $conn->prepare("SELECT prodotti.*, immagini_prodotti.immagine_url FROM prodotti LEFT JOIN immagini_prodotti ON prodotti.id = immagini_prodotti.prodotto_id WHERE prodotti.nome LIKE ?");
$stmt->execute(["%$search%"]);
$prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Negozio</title>
    <link rel="Website Icon" type="jpeg" href="./images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="negozio.php">
            <img src="./images/logo.png" alt="Logo" class="logo me-2">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link active" href="negozio.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="carrello.php">Carrello</a></li>
                <li class="nav-item"><a class="nav-link" href="saldo.php">Aggiungi saldo</a></li>
                <li class="nav-item"><a class="nav-link" href="aggiungi_prodotto.php">Aggiungi prodotto</a></li>
                <?php if ($_SESSION['user']['ruolo'] === 'amministratore'): ?>
                <li class="nav-item"><a class="nav-link" href="conferma_prodotto.php">Conferma prodotto</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex">
                <span class="navbar-text text-white me-3">
                    Ciao, <?php echo htmlspecialchars($_SESSION['user']['nomeUtente']); ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </div>
</nav>

<!-- Barra di ricerca -->
<div class="search-container container mt-4 mb-4">
    <form method="get" class="d-flex w-100 justify-content-center">
        <input type="text" name="search" class="search-bar" placeholder="Cerca un prodotto" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="search-button">Cerca</button>
    </form>
</div>

<!-- Prodotti -->
<div class="container">
    <div class="row">
        <?php if (count($prodotti) === 0): ?>
            <div class="col-12 text-center">
                <p>Nessun prodotto trovato.</p>
            </div>
        <?php endif; ?>
        <?php foreach ($prodotti as $prodotto): ?>
            <div class="col-lg-4 col-md-6 mb-4 d-flex align-items-stretch">
                <div class="card w-100 shadow-sm">
                    <?php if ($prodotto['immagine_url']): ?>
                        <img src="<?php echo htmlspecialchars($prodotto['immagine_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>">
                    <?php else: ?>
                        <img src="path/to/default-image.jpg" class="card-img-top" alt="Immagine non disponibile">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($prodotto['nome']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($prodotto['descrizione']); ?></p>
                        <p class="card-text">Prezzo: <strong><?php echo number_format($prodotto['prezzo'], 2); ?>€</strong></p>
                        <form method="post" action="aggiungi_al_carello.php" class="mt-auto">
                            <input type="hidden" name="prodotto_id" value="<?php echo $prodotto['id']; ?>">
                            <input type="hidden" name="nome" value="<?php echo htmlspecialchars($prodotto['nome']); ?>">
                            <input type="hidden" name="prezzo" value="<?php echo $prodotto['prezzo']; ?>">
                            <input type="hidden" name="immagine_url" value="<?php echo $prodotto['immagine_url']; ?>">
                            <button type="submit" class="btn btn-success w-100">Aggiungi al carrello</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white py-3 mt-auto">
    <div class="container text-center">
        <p>&copy; 2025 Negozio Online. Tutti i diritti riservati.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
