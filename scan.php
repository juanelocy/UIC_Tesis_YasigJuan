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
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        // Ejecutar Nmap con salida XML
        $comando = escapeshellcmd("nmap -F -oX - " . $ip);
        $output = shell_exec($comando);
        // Parsear XML a estructura PHP
        $puertos = [];
        $servicios = [];
        if ($output) {
            $xml = @simplexml_load_string($output);
            if ($xml && isset($xml->host->ports->port)) {
                foreach ($xml->host->ports->port as $port) {
                    $puerto = [
                        'portid' => (string)$port['portid'],
                        'protocol' => (string)$port['protocol'],
                        'state' => (string)$port->state['state'],
                        'service' => isset($port->service['name']) ? (string)$port->service['name'] : '',
                        'product' => isset($port->service['product']) ? (string)$port->service['product'] : '',
                        'version' => isset($port->service['version']) ? (string)$port->service['version'] : '',
                        'extrainfo' => isset($port->service['extrainfo']) ? (string)$port->service['extrainfo'] : ''
                    ];
                    $puertos[] = $puerto;
                }
            }
        }
        // Guardar resultado en archivo JSON
        $scanData = [
            'ip' => $ip,
            'timestamp' => date('Y-m-d_H-i-s'),
            'ports' => $puertos,
            'raw' => $output
        ];
        $filename = __DIR__ . '/scans/scan_' . $ip . '_' . date('Ymd_His') . '.json';
        file_put_contents($filename, json_encode($scanData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        // Responder con estructura y texto plano
        echo json_encode([
            'success' => true,
            'scan' => $output,
            'ports' => $puertos
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
