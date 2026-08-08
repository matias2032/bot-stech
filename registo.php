<?php
// ============================================================
//  REGISTO.PHP — Formulário e processamento de registo
// ============================================================

require_once 'auth.php';
iniciarSessao();

// Já logado? Redireciona
if (estaLogado()) {
    header('Location: menu.php');
    exit;
}

require_once 'conexao.php';
require_once 'google_oauth.php';

$erro       = '';
$campo_erro = ''; // para destacar o campo com erro

// ── Processar formulário ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome            = trim($_POST['nome']            ?? '');
    $email           = trim($_POST['email']           ?? '');
    $senha           = $_POST['senha']                ?? '';
    $senha_confirmar = $_POST['senha_confirmar']      ?? '';

    // Validações no servidor
    if ($nome === '' || $email === '' || $senha === '' || $senha_confirmar === '') {
        $erro = 'Preenche todos os campos.';
    } elseif (mb_strlen($nome) < 2) {
        $erro = 'O nome deve ter pelo menos 2 caracteres.';
        $campo_erro = 'nome';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Endereço de email inválido.';
        $campo_erro = 'email';
    } elseif (mb_strlen($senha) < 8) {
        $erro = 'A palavra-passe deve ter pelo menos 8 caracteres.';
        $campo_erro = 'senha';
    } elseif ($senha !== $senha_confirmar) {
        $erro = 'As palavras-passe não coincidem.';
        $campo_erro = 'senha_confirmar';
    } else {
        $pdo = obterConexao();

        // Verificar se o email já está em uso
        $stmt = $pdo->prepare("SELECT id_utilizador FROM utilizadores WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $erro = 'Este email já está registado.';
            $campo_erro = 'email';
        } else {
            // Tudo ok — criar utilizador
            $hash = password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);

            $stmt = $pdo->prepare("
                INSERT INTO utilizadores (nome, email, senha_hash, perfil)
                VALUES (:nome, :email, :hash, 'utilizador')
            ");
            $stmt->execute([
                ':nome'  => $nome,
                ':email' => $email,
                ':hash'  => $hash,
            ]);

            header('Location: login.php?registado=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar conta no ChatBot</title>
    <link rel="icon" type="image/svg+xml" href="logo.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo.css">
   

</head>
<body>

<div class="auth-card">
    <div class="auth-logo">
        <div class="auth-logo-icone">
            <svg width="22" height="22" viewBox="0 0 28 28" fill="none">
                <circle cx="14" cy="14" r="13" stroke="var(--cor-acento)" stroke-width="1.5"/>
                <path d="M8 14c0-3.3 2.7-6 6-6s6 2.7 6 6-2.7 6-6 6" stroke="var(--cor-acento)" stroke-width="1.5" stroke-linecap="round"/>
                <circle cx="14" cy="14" r="2.5" fill="var(--cor-acento)"/>
            </svg>
        </div>
        <span class="auth-logo-nome">ChatBot</span>
    </div>

    <h1 class="auth-titulo">Criar conta</h1>
    <p class="auth-subtitulo">Regista-te para começar a usar o chatbot</p>

    <?php if ($erro): ?>
        <div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="registo.php" novalidate>
        <div class="campo-grupo">
            <label for="nome">Nome completo</label>
            <input
                type="text" id="nome" name="nome"
                value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                placeholder="O teu nome"
                class="<?= $campo_erro === 'nome' ? 'erro-campo' : '' ?>"
                autocomplete="name" required
            >
        </div>

        <div class="campo-grupo">
            <label for="email">Email</label>
            <input
                type="email" id="email" name="email"
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                placeholder="o-teu@email.com"
                class="<?= $campo_erro === 'email' ? 'erro-campo' : '' ?>"
                autocomplete="email" required
            >
        </div>

        <div class="campo-grupo">
            <label for="senha">Palavra-passe</label>
            <input
                type="password" id="senha" name="senha"
                placeholder="Mínimo 8 caracteres"
                class="<?= $campo_erro === 'senha' ? 'erro-campo' : '' ?>"
                autocomplete="new-password" required
            >
            <p class="dica-senha">Mínimo de 8 caracteres.</p>
        </div>

        <div class="campo-grupo">
            <label for="senha_confirmar">Confirmar palavra-passe</label>
            <input
                type="password" id="senha_confirmar" name="senha_confirmar"
                placeholder="Repete a palavra-passe"
                class="<?= $campo_erro === 'senha_confirmar' ? 'erro-campo' : '' ?>"
                autocomplete="new-password" required
            >
        </div>

        <button type="submit" class="btn-auth">Criar conta</button>
    </form>

    <p class="auth-link">
        Já tens conta? <a href="login.php">Entrar</a>
    </p>
    <div class="separador">ou</div>

    <a href="<?= htmlspecialchars(googleUrlAutorizacao()) ?>" class="btn-google">
        <svg width="18" height="18" viewBox="0 0 48 48">
            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
        </svg>
        Continuar com Google
    </a>
</div>
    <script src="js/sidebar.js"></script>

</body>
</html>