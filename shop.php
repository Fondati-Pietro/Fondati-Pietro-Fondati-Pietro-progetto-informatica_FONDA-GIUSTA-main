<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">S
   
</head>
<body>
    
</body>
</html>
<?php
include 'config.php';
$prodotti = $conn->query("SELECT prodotti.*, immagini_prodotti.immagine_url FROM prodotti LEFT JOIN immagini_prodotti ON prodotti.id = immagini_prodotti.prodotto_id")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shop</title>
</head>
<body>
    <h1>Negozio Online</h1>
    <?php foreach ($prodotti as $prodotto): ?>
        <div>
            <h2><?php echo htmlspecialchars($prodotto['nome']); ?></h2>
            <p><?php echo htmlspecialchars($prodotto['descrizione']); ?></p>
            <p>Prezzo: <?php echo $prodotto['prezzo']; ?>€</p>
            <?php if ($prodotto['immagine_url']): ?>
                <img src="<?php echo htmlspecialchars($prodotto['immagine_url']); ?>" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>" width="150">
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>
</html>