<?php
/*
 * Este script recibe una petición POST con una dirección IP (ipAddress),
 * valida que sea una IP IPv4 válida y ejecuta un escaneo rápido de puertos
 * usando la herramienta nmap. El resultado del escaneo se retorna en formato JSON.
*/
// Establecer el tipo de contenido a JSON
header('Content-Type: application/json');
// Manejo de la petición
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ipAddress'])) {
    // Validar y limpiar la IP
    $ip = trim($_POST['ipAddress']);
    // Validar que sea una IP IPv4
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        // Ejecutar el escaneo (ejemplo con nmap)
        $comando = escapeshellcmd("nmap -F " . $ip);
        // Ejecutar el comando y capturar la salida
        $output = shell_exec($comando);
        echo json_encode([
            'success' => true,
            'scan' => $output
        ]);
        exit;
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'IP inválida.'
        ]);
        exit;
    }
}
echo json_encode([
    'success' => false,
    'error' => 'Petición inválida.'
]);
