<?php 
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomeUtente = $_POST['username'];
    $password = $_POST['password'];

    // Verifica se l'utente esiste già
    $stmt = $conn->prepare("SELECT * FROM utenti WHERE nomeUtente = ?");
    $stmt->execute([$nomeUtente]);
    $user = $stmt->fetch();

    if ($user) {
        $errore = "Nome utente già in uso. Scegli un altro nome";
    } else {
        // MODIFICATO: Salva la password in chiaro invece di fare l'hash
        $stmt = $conn->prepare("INSERT INTO utenti (nomeUtente, password, ruolo) VALUES (?, ?, 'cliente')");
        if ($stmt->execute([$nomeUtente, $password])) {
            $successo = "Registrazione completata! Ora puoi accedere";
        } else {
            $errore = "Errore nella registrazione. Riprova più tardi";
        }
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
    <title>Registrazione - Negozio Online</title>
    <style>
        /* [MANTENUTO LO STILE ORIGINALE] */
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
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .register-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            transition: var(--transition);
        }

        .register-container:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        h2 {
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 700;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            height: 50px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding-left: 15px;
            width: 100%;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        .btn-register {
            background-color: var(--primary-color);
            color: white;
            border: none;
            height: 50px;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: var(--transition);
            width: 100%;
            margin-top: 10px;
        }

        .btn-register:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }

        .success-message {
            background-color: rgba(75, 181, 67, 0.2);
            color: var(--success-color);
        }

        .error-message {
            background-color: rgba(255, 107, 107, 0.2);
            color: var(--error-color);
            animation: shake 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .additional-links {
            text-align: center;
            margin-top: 20px;
        }

        .additional-links a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
            display: inline-block;
            margin-top: 10px;
        }

        .additional-links a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .password-strength {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease;
        }

        @media (max-width: 576px) {
            .register-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Crea un account</h2>
        
        <?php if(isset($errore)): ?>
            <div class="message error-message"><?php echo $errore; ?></div>
        <?php endif; ?>
        
        <?php if(isset($successo)): ?>
            <div class="message success-message"><?php echo $successo; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <input type="text" name="username" placeholder="Nome utente" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" placeholder="Password" class="form-control" required>
                <div class="password-strength">
                    <div class="strength-bar" id="strength-bar"></div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-register">Registrati</button>
        </form>
        
        <div class="additional-links">
            <p>Hai già un account? <a href="login.php">Accedi qui</a></p>
            <a href="negozio.php">Torna al negozio</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Indicatore forza password
        // Modifica il colore della barra in base alla forza della password
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strength-bar');
            let strength = 0;
            
            if (password.length > 0) strength += 1;
            if (password.length >= 6) strength += 1;
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            const width = strength * 20;
            strengthBar.style.width = width + '%';
            strengthBar.style.backgroundColor = 
                strength < 2 ? '#ff4d4d' : 
                strength < 4 ? '#ffa500' : '#4bb543';
        });
    </script>
</body>
</html>