<?php
include 'config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Prodotto - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --dark-color: #212529;
        --light-color: #f8f9fa;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --transition: all 0.3s ease;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
        color: var(--dark-color);
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);


    }

    .navbar {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
        font-weight: 700;
        color: var(--primary-color) !important;
    }

    .nav-link {
        transition: var(--transition);
    }

    .nav-link:hover {
        color: var(--primary-color) !important;
    }

    .product-form {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        padding: 30px;
        margin-top: 30px;
    }

    .form-title {
        color: var(--primary-color);
        margin-bottom: 30px;
        font-weight: 700;
        text-align: center;
    }

    .form-control,
    .form-select {
        height: 50px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding-left: 15px;
        transition: var(--transition);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
    }

    textarea.form-control {
        height: auto;
        min-height: 120px;
        resize: vertical;
    }

    .btn-submit {
        background-color: var(--primary-color);
        border: none;
        height: 50px;
        border-radius: 8px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: var(--transition);
        width: 100%;
        margin-top: 20px;
    }

    .btn-submit:hover {
        background-color: var(--secondary-color);
        transform: translateY(-3px);
    }

    .category-options {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
        margin: 15px 0;
    }

    .form-check {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 8px;
        transition: var(--transition);
    }

    .form-check:hover {
        background: #e9ecef;
    }

    .form-check-input {
        margin-top: 0.3em;
    }

    .preview-image {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin-top: 10px;
        display: none;
        border: 1px solid #dee2e6;
    }

    @media (max-width: 768px) {
        .product-form {
            padding: 20px;
        }

        .category-options {
            grid-template-columns: 1fr;
        }
    }

    .preview-image {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin-top: 10px;
        display: none;
        border: 1px solid #dee2e6;
    }
    </style>
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

    <div class="container py-5">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nome = $_POST['nome'];
            $descrizione = $_POST['descrizione'];
            $prezzo = floatval($_POST['prezzo']);
            $quantita = intval($_POST['quantita']);
            $categorie = $_POST['categoria_id'] ?? [];
            $immagine_path = $_POST['immagine_path'];

            // Aggiungi controllo per l'estensione dell'immagine
            $imageFileType = strtolower(pathinfo($immagine_path, PATHINFO_EXTENSION));
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                echo '<div class="alert alert-danger text-center">Spiacente, solo immagini JPG, JPEG, PNG & GIF sono permessi.</div>';
            } else {
                if (!empty($categorie) && !empty($immagine_path)) {
                    $success_count = 0;
                    
                    // Se admin, inserimento diretto
                    if ($_SESSION['user']['ruolo'] === 'amministratore') {
                        foreach ($categorie as $cat_id) {
                            $cat_id = intval($cat_id);
                            
                            $query = "INSERT INTO prodotti (nome, descrizione, prezzo, quantita_disponibile, categoria_id)
                                      VALUES (:nome, :descrizione, :prezzo, :quantita, :categoria_id)";
                            $stmt = $conn->prepare($query);
                            $stmt->execute([
                                ':nome' => $nome,
                                ':descrizione' => $descrizione,
                                ':prezzo' => $prezzo,
                                ':quantita' => $quantita,
                                ':categoria_id' => $cat_id
                            ]);

                            $prodotto_id = $conn->lastInsertId();

                            $query_img = "INSERT INTO immagini_prodotti (prodotto_id, immagine_url)
                                          VALUES (:prodotto_id, :immagine_url)";
                            $stmt_img = $conn->prepare($query_img);
                            $stmt_img->execute([
                                ':prodotto_id' => $prodotto_id,
                                ':immagine_url' => $immagine_path
                            ]);

                            $success_count++;
                        }
                        $message = '<div class="alert alert-success alert-dismissible fade show text-center">
                            <strong>Successo!</strong> ' . $success_count . ' prodotto(i) aggiunto(i) direttamente al negozio.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          </div>';
                    } else {
                        // Se utente normale, inserimento in coda di approvazione
                        foreach ($categorie as $cat_id) {
                            $cat_id = intval($cat_id);
                            
                            $query = "INSERT INTO prodotti_temp (nome, descrizione, prezzo, quantita_disponibile, categoria_id, immagine_url)
                                      VALUES (:nome, :descrizione, :prezzo, :quantita, :categoria_id, :immagine_url)";
                            $stmt = $conn->prepare($query);
                            $stmt->execute([
                                ':nome' => $nome,
                                ':descrizione' => $descrizione,
                                ':prezzo' => $prezzo,
                                ':quantita' => $quantita,
                                ':categoria_id' => $cat_id,
                                ':immagine_url' => $immagine_path
                            ]);
                            
                            $success_count++;
                        }
                        $message = '<div class="alert alert-info alert-dismissible fade show text-center">
                            <strong>In attesa di approvazione!</strong> ' . $success_count . ' prodotto(i) inviato(i) per approvazione.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          </div>';
                    }

                    echo $message;
                } else {
                    echo '<div class="alert alert-warning text-center">Seleziona almeno una categoria e inserisci il percorso immagine.</div>';
                }
            }
        }
        ?>

        <!-- Form prodotto -->
        <div class="product-form mx-auto">
            <h2 class="form-title">Aggiungi Nuovo Prodotto</h2>
            <form action="aggiungi_prodotto.php" method="POST" id="productForm">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome Prodotto</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>

                <div class="mb-3">
                    <label for="descrizione" class="form-label">Descrizione</label>
                    <textarea class="form-control" id="descrizione" name="descrizione" rows="3" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="prezzo" class="form-label">Prezzo (€)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="prezzo" name="prezzo"
                            required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="quantita" class="form-label">Quantità Disponibile</label>
                        <input type="number" min="0" class="form-control" id="quantita" name="quantita" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Categorie</label>
                    <div class="category-options">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="categoria_id[]" value="1" id="cat1">
                            <label class="form-check-label" for="cat1">Elettronica</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="categoria_id[]" value="2" id="cat2">
                            <label class="form-check-label" for="cat2">Abbigliamento</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="categoria_id[]" value="3" id="cat3">
                            <label class="form-check-label" for="cat3">Accessori</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="categoria_id[]" value="4" id="cat4">
                            <label class="form-check-label" for="cat4">Casa e cucina</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="immagine_path" class="form-label">Percorso immagine sul server</label>
                    <input type="text" class="form-control" id="immagine_path" name="immagine_path"
                        placeholder="es: immagini/prodotto1.jpg" required>
                    <img id="imagePreview" class="preview-image" alt="Anteprima immagine">
                </div>

                <button type="submit" class="btn btn-primary btn-submit">Aggiungi Prodotto</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Mostra anteprima immagine dal percorso inserito
    document.getElementById('immagine_path').addEventListener('input', function() {
        const path = this.value;
        const preview = document.getElementById('imagePreview');
        if (path) {
            preview.src = path;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    });

    // Validazione categorie
    document.getElementById('productForm').addEventListener('submit', function(e) {
        const checkboxes = document.querySelectorAll('input[name="categoria_id[]"]:checked');
        if (checkboxes.length === 0) {
            alert('Seleziona almeno una categoria');
            e.preventDefault();
        }
    });
    </script>
</body>

</html>