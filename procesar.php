<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prompt = trim($_POST['prompt']);

    // Guardar mensaje del usuario en el historial de chat
    $_SESSION['chat'][] = ['role' => 'Tú', 'text' => $prompt];

    // Recuperar historial de chat (sin el último mensaje IA, que aún no existe)
    $chat_history = isset($_SESSION['chat']) ? $_SESSION['chat'] : [];

    // Recuperar resumen del escaneo
    $scan_summary = isset($_SESSION['scan_summary']) ? $_SESSION['scan_summary'] : '';

    // Construir historial en texto
    $historial_txt = "";
    foreach ($chat_history as $msg) {
        $historial_txt .= "{$msg['role']}: {$msg['text']}\n";
    }

    // Construir el prompt contextualizado
    /*$prompt_context =
        "IMPORTANTE: Eres un asistente experto en ciberseguridad y mitigación de vulnerabilidades. ".
        "Solo puedes responder preguntas relacionadas con mitigación, seguridad informática, escaneo de puertos, servicios y CVEs. ".
        "Si el usuario pregunta algo fuera de ese ámbito, responde: 'Solo puedo responder consultas sobre ciberseguridad y mitigación de vulnerabilidades.'\n\n".
        "Resumen del escaneo:\n$scan_summary\n\n".
        "Historial de la conversación:\n$historial_txt\n".
        "Pregunta del usuario:\n$prompt";
    */
    
    // Prompt frío y técnico
    $prompt_context =
        "Eres un asistente técnico de ciberseguridad. Responde de forma concisa, precisa y fría, solo lo necesario. ".
        "Solo responde sobre mitigación, seguridad informática, escaneo de puertos, servicios y CVEs. ".
        "Si la consulta es irrelevante, responde: 'Solo consultas de ciberseguridad y mitigación.'\n\n".
        "Resumen del escaneo (JSON):\n$scan_summary\n\n".
        "Historial de la conversación:\n$historial_txt\n".
        "Pregunta del usuario:\n$prompt";
    // Ejecutar Python y capturar salida limpia
    $command = __DIR__ . "/venv/bin/python ia.py " . escapeshellarg($prompt_context);
    $output = shell_exec($command . " 2>&1"); // Captura errores también

    if ($output === null || trim($output) === "") {
        $output = "⚠️ No recibí respuesta del modelo.";
    }

    // Guardar respuesta de la IA en el historial de chat
    $_SESSION['chat'][] = ['role' => '🤖 IA', 'text' => trim($output)];

    // Si la petición es AJAX/fetch, devolver solo la respuesta IA
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
        header('Content-Type: text/plain; charset=utf-8');
        echo trim($output);
        exit;
    }
    // Si es formulario tradicional, redirigir
    header("Location: index.php");
    exit;
}