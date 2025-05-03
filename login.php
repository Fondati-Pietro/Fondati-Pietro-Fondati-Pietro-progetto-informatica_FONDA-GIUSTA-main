<?php 
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomeUtente = $_POST['username'];
    $password = $_POST['password'];
    
    // Verifica le credenziali dell'utente
    $stmt = $conn->prepare("SELECT * FROM utenti WHERE nomeUtente = ?");
    $stmt->execute([$nomeUtente]);
    $utente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($utente && $password === $utente['password']) {
        $_SESSION['user'] = [
            'id' => $utente['id'],
            'nomeUtente' => $utente['nomeUtente'],
            'ruolo' => $utente['ruolo']
        ];
        header("Location: negozio.php");
        exit();
    } else {
        $errore = "Credenziali non valide. Riprova.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <title>Login - Negozio Online</title>
    <style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4cc9f0;
        --light-color: #f8f9fa;
        --dark-color: #212529;
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
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .login-container {
        background-color: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 40px;
        width: 100%;
        max-width: 450px;
        transition: var(--transition);
    }

    .login-container:hover {
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    h2 {
        color: var(--primary-color);
        margin-bottom: 30px;
        font-weight: 700;
        text-align: center;
    }

    .form-control {
        height: 50px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding-left: 15px;
        margin-bottom: 20px;
        transition: var(--transition);
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
    }

    .btn-login {
        background-color: var(--primary-color);
        border: none;
        height: 50px;
        border-radius: 8px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        transition: var(--transition);
        margin-top: 10px;
    }

    .btn-login:hover {
        background-color: var(--secondary-color);
        transform: translateY(-3px);
    }

    .error-message {
        color: var(--error-color);
        text-align: center;
        margin-bottom: 20px;
        font-size: 0.9rem;
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        10%,
        30%,
        50%,
        70%,
        90% {
            transform: translateX(-5px);
        }

        20%,
        40%,
        60%,
        80% {
            transform: translateX(5px);
        }
    }

    .additional-links {
        text-align: center;
        margin-top: 20px;
    }

    .additional-links a {
        color: var(--primary-color);
        text-decoration: none;
        transition: var(--transition);
    }

    .additional-links a:hover {
        color: var(--secondary-color);
        text-decoration: underline;
    }

    .input-icon {
        position: relative;
    }

    .input-icon i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }

    .input-icon input {
        padding-left: 40px;
    }

    @media (max-width: 576px) {
        .login-container {
            padding: 30px 20px;
        }
    }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Accedi al tuo account</h2>

        <?php if(isset($errore)): ?>
        <div class="error-message"><?php echo $errore; ?></div>
        <?php endif; ?>



        <form method="POST">
            <div class="mb-3">
                <input type="text" name="username" placeholder="Nome utente" class="form-control" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" placeholder="Password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-login w-100">Accedi</button>
        </form>

        <div class="additional-links mt-3">
            <a href="register.php">Non hai un account? Registrati</a><br>
        </div>
    </div>



</body>

</html>