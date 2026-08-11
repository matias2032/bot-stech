<?php
// ============================================================
// CONFIGURACAO.PHP — Suporte Exclusivo a Ollama (SmolLM2)
// ============================================================

// ------------------------------------------------------------
// AMBIENTE
// ------------------------------------------------------------
if (!defined('AMBIENTE')) {
    define('AMBIENTE', getenv('AMBIENTE') ?: 'desenvolvimento');
}

// ------------------------------------------------------------
// OLLAMA (Local LLM via Coolify)
// ------------------------------------------------------------
define('OLLAMA_API_URL', getenv('OLLAMA_API_URL') ?: 'http://ollama:11434/api/chat');
define('OLLAMA_MODELO',  getenv('OLLAMA_MODELO')  ?: 'smollm2:360m');

// ------------------------------------------------------------
// BOT
// ------------------------------------------------------------
define('BOT_ID', getenv('BOT_ID'));

// ------------------------------------------------------------
// UPLOADS
// ------------------------------------------------------------
define('PASTA_UPLOADS', __DIR__ . '/uploads/');
define('TAMANHO_MAXIMO_MB', 10);
define('TAMANHO_MAXIMO_BYTES', TAMANHO_MAXIMO_MB * 1024 * 1024);
define('TIPOS_PERMITIDOS', ['application/pdf', 'text/plain']);

// ------------------------------------------------------------
// RAG (Busca de Conhecimento)
// ------------------------------------------------------------
define('CHUNK_TAMANHO', 350);
define('CHUNK_SOBREPOSICAO', 40);
define('MAX_RESULTADOS_BUSCA', 2);
define('MAX_HISTORICO_MENSAGENS', 4);

// ------------------------------------------------------------
// SEGURANÇA
// ------------------------------------------------------------
define('ADMIN_SENHA', getenv('ADMIN_PASSWORD') ?: 'admin123');

// ------------------------------------------------------------
// FUNÇÕES AUXILIARES
// ------------------------------------------------------------
function definirCabecalhosJson(): void {
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
}

function respostaJson(bool $sucesso, mixed $dados = null, string $erro = ''): void {
    definirCabecalhosJson();
    echo json_encode([
        'sucesso' => $sucesso,
        'dados'   => $dados,
        'erro'    => $erro,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Criar pasta de uploads se não existir
if (!is_dir(PASTA_UPLOADS)) {
    mkdir(PASTA_UPLOADS, 0755, true);
}