<?php
// ============================================================
//  API_UPLOAD.PHP — OCR local via Tesseract (página a página)
//  Resolve: PDFs scanned, 100% offline (sem dependência de API externa)
// ============================================================

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

require_once 'configuracao.php';
require_once 'conexao.php';
require_once 'auth.php';

iniciarSessao();

if (!eAdmin()) {
    definirCabecalhosJson();
    echo json_encode(['sucesso' => false, 'dados' => null, 'erro' => 'Não autorizado.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$pdo          = obterConexao();
$content_type = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';

// ------------------------------------------------------------
// Acções JSON
// ------------------------------------------------------------
if (str_contains($content_type, 'application/json')) {
    $corpo = json_decode(file_get_contents('php://input'), true);
    $acao  = $corpo['acao'] ?? '';
    $id    = trim($corpo['id'] ?? '');

    if ($id === '') respostaJson(false, null, 'ID inválido.');

    if ($acao === 'eliminar') {
        $stmt = $pdo->prepare("SELECT caminho_ficheiro FROM documentos WHERE id_documento=:id AND id_configuracao_bot=:bot");
        $stmt->execute([':id' => $id, ':bot' => BOT_ID]);
        $doc = $stmt->fetch();
        if ($doc && file_exists($doc['caminho_ficheiro'])) unlink($doc['caminho_ficheiro']);
        $stmt = $pdo->prepare("DELETE FROM documentos WHERE id_documento=:id AND id_configuracao_bot=:bot");
        $stmt->execute([':id' => $id, ':bot' => BOT_ID]);
        respostaJson($stmt->rowCount() > 0, null, $stmt->rowCount() === 0 ? 'Não encontrado.' : '');
    }

    if ($acao === 'reprocessar') {
        $stmt = $pdo->prepare("SELECT * FROM documentos WHERE id_documento=:id AND id_configuracao_bot=:bot");
        $stmt->execute([':id' => $id, ':bot' => BOT_ID]);
        $doc = $stmt->fetch();
        if (!$doc) respostaJson(false, null, 'Documento não encontrado.');
        $pdo->prepare("UPDATE documentos SET estado='a_processar',mensagem_erro=NULL WHERE id_documento=:id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM fragmentos_documento WHERE id_documento=:id")->execute([':id' => $id]);
        processarDocumento($pdo, $id, $doc['caminho_ficheiro'], $doc['tipo_mime']);
        respostaJson(true, null, '');
    }

    if ($acao === 'verificar_estado') {
        $stmt = $pdo->prepare("
            SELECT d.estado, d.mensagem_erro,
                   COUNT(f.id_fragmento) AS total_fragmentos
            FROM documentos d
            LEFT JOIN fragmentos_documento f ON f.id_documento = d.id_documento
            WHERE d.id_documento=:id AND d.id_configuracao_bot=:bot
            GROUP BY d.estado, d.mensagem_erro
        ");
        $stmt->execute([':id' => $id, ':bot' => BOT_ID]);
        $r = $stmt->fetch();
        respostaJson((bool)$r, $r ?: null, $r ? '' : 'Não encontrado.');
    }

    respostaJson(false, null, 'Acção desconhecida.');
}

// ------------------------------------------------------------
// Upload de ficheiro
// ------------------------------------------------------------
$upload_erro = $_FILES['ficheiro']['error'] ?? UPLOAD_ERR_NO_FILE;
if (!isset($_FILES['ficheiro']) || $upload_erro !== UPLOAD_ERR_OK) {
    $erros = [
        UPLOAD_ERR_INI_SIZE   => 'Excede upload_max_filesize no php.ini.',
        UPLOAD_ERR_FORM_SIZE  => 'Excede o limite do formulário.',
        UPLOAD_ERR_PARTIAL    => 'Upload incompleto.',
        UPLOAD_ERR_NO_FILE    => 'Nenhum ficheiro enviado.',
        UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária em falta.',
        UPLOAD_ERR_CANT_WRITE => 'Sem permissão de escrita.',
    ];
    respostaJson(false, null, $erros[$upload_erro] ?? "Código {$upload_erro}.");
}

$ficheiro      = $_FILES['ficheiro'];
$nome_original = basename($ficheiro['name']);
$tamanho       = $ficheiro['size'];
$extensao      = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));
$categoria     = trim($_POST['categoria'] ?? '') ?: null;
$descricao     = trim($_POST['descricao']  ?? '') ?: null;
$tipo_mime     = detectarMime($ficheiro['tmp_name'], $nome_original);

if (!in_array($tipo_mime, ['application/pdf', 'text/plain']) && !in_array($extensao, ['pdf', 'txt'])) {
    respostaJson(false, null, "Tipo não permitido ({$tipo_mime}).");
}
if ($tamanho > TAMANHO_MAXIMO_BYTES) {
    respostaJson(false, null, 'Ficheiro grande demais. Máximo ' . TAMANHO_MAXIMO_MB . ' MB.');
}
if (!is_dir(PASTA_UPLOADS) && !mkdir(PASTA_UPLOADS, 0755, true)) {
    respostaJson(false, null, 'Não foi possível criar pasta uploads/.');
}

$nome_guardado = uniqid('doc_', true) . '.' . $extensao;
$caminho_final = PASTA_UPLOADS . $nome_guardado;

if (!move_uploaded_file($ficheiro['tmp_name'], $caminho_final)) {
    respostaJson(false, null, 'Erro ao mover ficheiro. Verifica permissões de uploads/.');
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO documentos
            (id_configuracao_bot,nome_original,nome_guardado,caminho_ficheiro,
             tipo_mime,tamanho_bytes,categoria,descricao,estado)
        VALUES (:bot,:orig,:guard,:cam,:mime,:tam,:cat,:desc,'a_processar')
        RETURNING id_documento
    ");
    $stmt->execute([
        ':bot'  => BOT_ID, ':orig' => $nome_original, ':guard' => $nome_guardado,
        ':cam'  => $caminho_final, ':mime' => $tipo_mime, ':tam'  => $tamanho,
        ':cat'  => $categoria,    ':desc' => $descricao,
    ]);
    $id_documento = $stmt->fetchColumn();
} catch (PDOException $e) {
    if (file_exists($caminho_final)) unlink($caminho_final);
    respostaJson(false, null, 'Erro na BD: ' . $e->getMessage());
}

// Responde ao browser imediatamente e processa em background
$resposta = json_encode([
    'sucesso' => true,
    'dados'   => ['id' => $id_documento, 'nome' => $nome_original, 'estado' => 'a_processar'],
    'erro'    => '',
], JSON_UNESCAPED_UNICODE);

while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json; charset=utf-8');
header('Connection: close');
header('Content-Length: ' . strlen($resposta));
echo $resposta;
flush();
if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();

set_time_limit(600); // PDFs scanned com OCR podem demorar vários minutos
ignore_user_abort(true);
session_write_close();

processarDocumento($pdo, $id_documento, $caminho_final, $tipo_mime);
exit;


// ============================================================
// FUNÇÕES
// ============================================================

function processarDocumento(PDO $pdo, string $id, string $caminho, string $mime): void {
    try {
        $texto = extrairTexto($caminho, $mime);
        if (trim($texto) === '') {
            $pdo->prepare("UPDATE documentos SET estado='erro',mensagem_erro='Sem texto extraível. O PDF é baseado em imagens e o OCR não conseguiu transcrever o conteúdo.' WHERE id_documento=:id")
                ->execute([':id' => $id]);
            return;
        }
        $total = criarFragmentos($pdo, $id, $texto);
        $pdo->prepare("UPDATE documentos SET estado='pronto',processado_em=NOW(),mensagem_erro=NULL WHERE id_documento=:id")
            ->execute([':id' => $id]);
        error_log("[UPLOAD] OK — {$id}: {$total} fragmentos.");
    } catch (Throwable $e) {
        $pdo->prepare("UPDATE documentos SET estado='erro',mensagem_erro=:m WHERE id_documento=:id")
            ->execute([':id' => $id, ':m' => substr($e->getMessage(), 0, 500)]);
        error_log("[UPLOAD] ERRO — {$id}: " . $e->getMessage());
    }
}

// ------------------------------------------------------------
// EXTRACÇÃO DE TEXTO
// Cascata: TXT → smalot → OCR Tesseract (página a página) → pdftotext → fallback regex
// ------------------------------------------------------------

function extrairTexto(string $caminho, string $mime): string {

    // 1. TXT — leitura directa
    if (str_contains($mime, 'text') || str_ends_with($caminho, '.txt')) {
        $t = file_get_contents($caminho);
        return $t !== false ? limparTexto($t) : '';
    }

    if (!file_exists($caminho) || !is_readable($caminho)) {
        error_log("[EXTRAI] Inacessível: {$caminho}");
        return '';
    }

    // 2. smalot/pdfparser — PDFs com texto seleccionável (rápido, sem OCR)
    if (class_exists('\Smalot\PdfParser\Parser')) {
        try {
            $config = new \Smalot\PdfParser\Config();
            $config->setRetainImageContent(false);
            $parser = new \Smalot\PdfParser\Parser([], $config);
            $pdf    = $parser->parseFile($caminho);
            $texto  = '';
            foreach ($pdf->getPages() as $pag) {
                $texto .= $pag->getText() . "\n\n";
            }
            if (strlen(trim($texto)) > 50) {
                error_log("[EXTRAI] smalot OK: " . strlen($texto) . " chars");
                return limparTexto($texto);
            }
            // Texto insuficiente = PDF scanned → passa para OCR
            error_log("[EXTRAI] smalot: texto insuficiente (" . strlen(trim($texto)) . " chars) — a usar OCR.");
        } catch (\Throwable $e) {
            error_log("[EXTRAI] smalot excepção: " . $e->getMessage());
        }
    }

    // 3. OCR via Tesseract (local) — PDFs scanned
    $texto_ocr = extrairTextoOcrTesseract($caminho);
    if ($texto_ocr !== '') {
        error_log("[EXTRAI] OCR Tesseract OK: " . strlen($texto_ocr) . " chars");
        return $texto_ocr;
    }

    // 4. pdftotext — apenas Linux/Mac
    if (DIRECTORY_SEPARATOR === '/') {
        $dis = ini_get('disable_functions') ?: '';
        if (function_exists('shell_exec') && !str_contains($dis, 'shell_exec')) {
            foreach (['/usr/bin/pdftotext', '/usr/local/bin/pdftotext'] as $bin) {
                if (!file_exists($bin)) continue;
                $r = @shell_exec($bin . ' -enc UTF-8 -nopgbrk ' . escapeshellarg($caminho) . ' - 2>&1');
                if ($r && strlen(trim($r)) > 20 && !str_contains($r, 'Error')) {
                    return limparTexto($r);
                }
                break;
            }
        }
    }

    // 5. Fallback regex (extracção bruta do binário PDF)
    error_log("[EXTRAI] Fallback regex para: {$caminho}");
    return extrairTextoPdfFallback($caminho);
}

// ------------------------------------------------------------
// OCR VIA TESSERACT (local) — página a página
//
// Rasteriza o PDF em imagens PNG (uma por página) com pdftoppm e
// transcreve cada imagem com tesseract. 100% local, sem depender
// de nenhuma API externa. Requer poppler-utils e tesseract-ocr
// instalados no container.
// ------------------------------------------------------------

function extrairTextoOcrTesseract(string $caminho): string {
    if (!ferramentaOcrDisponivel('pdftoppm') || !ferramentaOcrDisponivel('tesseract')) {
        error_log("[OCR] pdftoppm/tesseract indisponíveis — a saltar OCR.");
        return '';
    }

    $pasta = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ocr_' . uniqid();
    mkdir($pasta, 0755, true);
    $prefixo = $pasta . DIRECTORY_SEPARATOR . 'pag';

    // Rasteriza cada página do PDF em PNG (150 DPI é suficiente para OCR)
    shell_exec('pdftoppm -r 150 -png ' . escapeshellarg($caminho) . ' ' . escapeshellarg($prefixo) . ' 2>&1');

    $paginas = glob($prefixo . '*.png') ?: [];
    sort($paginas, SORT_NATURAL);

    $texto_completo = '';
    foreach ($paginas as $img) {
        $saida = $img . '_txt';
        shell_exec('tesseract ' . escapeshellarg($img) . ' ' . escapeshellarg($saida) . ' -l por 2>&1');
        $conteudo = @file_get_contents($saida . '.txt') ?: '';
        if (trim($conteudo) !== '') {
            $texto_completo .= "\n\n" . $conteudo;
        }
        @unlink($img);
        @unlink($saida . '.txt');
    }

    @rmdir($pasta);
    return limparTexto($texto_completo);
}

function ferramentaOcrDisponivel(string $bin): bool {
    $dis = ini_get('disable_functions') ?: '';
    if (!function_exists('shell_exec') || str_contains($dis, 'shell_exec')) return false;
    $r = @shell_exec('which ' . escapeshellarg($bin) . ' 2>/dev/null');
    return !empty(trim((string)$r));
}

// ------------------------------------------------------------
// UTILITÁRIOS
// ------------------------------------------------------------

function detectarMime(string $tmp, string $nome): string {
    if (function_exists('finfo_open')) {
        $fi = finfo_open(FILEINFO_MIME_TYPE);
        $m  = finfo_file($fi, $tmp);
        finfo_close($fi);
        if ($m && $m !== 'application/octet-stream') return $m;
    }
    $h = fopen($tmp, 'rb');
    if ($h) { $b = fread($h, 4); fclose($h); if ($b === '%PDF') return 'application/pdf'; }
    return match(strtolower(pathinfo($nome, PATHINFO_EXTENSION))) {
        'pdf'  => 'application/pdf',
        'txt'  => 'text/plain',
        default => 'application/octet-stream',
    };
}

function extrairTextoPdfFallback(string $caminho): string {
    $c = file_get_contents($caminho);
    if (!$c) return '';
    $texto = '';
    preg_match_all('/BT\s*(.*?)\s*ET/s', $c, $bl);
    foreach ($bl[1] as $b) {
        preg_match_all('/\(([^)]*)\)/', $b, $sp);
        foreach ($sp[1] as $s) {
            $texto .= str_replace(['\\n','\\r','\\t'], ["\n","\r","\t"], $s) . ' ';
        }
    }
    return limparTexto($texto);
}

function limparTexto(string $t): string {
    $t = str_replace(chr(0), '', $t);
    $t = mb_convert_encoding($t, 'UTF-8', 'UTF-8');
    $t = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $t);
    $t = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $t);
    $t = preg_replace('/[ \t]+/', ' ', $t);
    $t = preg_replace('/\n{3,}/', "\n\n", $t);
    return trim($t);
}

function criarFragmentos(PDO $pdo, string $id_doc, string $texto): int {
    $texto = mb_convert_encoding($texto, 'UTF-8', 'UTF-8');
    $texto = str_replace(chr(0), '', $texto);
    $tam   = CHUNK_TAMANHO;
    $sob   = min(CHUNK_SOBREPOSICAO, (int)($tam / 2));
    $len   = mb_strlen($texto);
    $pos   = 0;
    $frags = [];

    while ($pos < $len) {
        $f = mb_substr($texto, $pos, $tam);
        if (trim($f) !== '') $frags[] = $f;
        $pos += ($tam - $sob);
    }

    $stmt = $pdo->prepare("
        INSERT INTO fragmentos_documento (id_documento, indice_fragmento, conteudo, total_tokens)
        VALUES (:doc, :i, :c, :t)
        ON CONFLICT (id_documento, indice_fragmento)
        DO UPDATE SET conteudo = EXCLUDED.conteudo
    ");

    $ok = 0;
    foreach ($frags as $i => $f) {
        $f = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $f);
        if ($stmt->execute([':doc' => $id_doc, ':i' => $i, ':c' => $f, ':t' => (int)(mb_strlen($f) / 4)])) {
            $ok++;
        }
    }
    return $ok;
}