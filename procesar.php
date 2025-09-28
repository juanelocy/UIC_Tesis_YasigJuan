<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prompt = trim($_POST['prompt']);

    // Guardar mensaje del usuario
    $_SESSION['chat'][] = ['role' => 'Tú', 'text' => $prompt];

    // Ejecutar Python y capturar salida limpia
    $command = __DIR__ . "/venv/bin/python ia.py " . escapeshellarg($prompt);
    $output = shell_exec($command . " 2>&1"); // Captura errores también

    if ($output === null || trim($output) === "") {
        $output = "⚠️ No recibí respuesta del modelo.";
    }

    // Guardar respuesta de la IA
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
