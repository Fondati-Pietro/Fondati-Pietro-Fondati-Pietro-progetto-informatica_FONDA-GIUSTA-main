<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['prodotto_id'];
    $nome = $_POST['nome'];
    $prezzo = $_POST['prezzo'];
    $immagine = $_POST['immagine_url'];

    // Inizializza il carrello se non esiste
    if (!isset($_SESSION['carrello']) || !is_array($_SESSION['carrello'])) {
        $_SESSION['carrello'] = [];
    }

    // Se il prodotto è già nel carrello, aumenta la quantità
    if (isset($_SESSION['carrello'][$id])) {
        $_SESSION['carrello'][$id]['quantita'] += 1;
    } else {
        // Aggiunge il prodotto con quantità 1
        $_SESSION['carrello'][$id] = [
            'id' => $id,
            'nome' => $nome,
            'prezzo' => $prezzo,
            'immagine_url' => $immagine,
            'quantita' => 1
        ];
    }
}

header("Location: negozio.php");
exit();
?>