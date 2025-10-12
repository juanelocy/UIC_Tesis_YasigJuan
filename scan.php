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
        $output_prefix = __DIR__ . '/scans/scan_result_' . $ip . '_' . date('Ymd_His');
        $comando = escapeshellcmd("nmap --script vulners -sV -oA $output_prefix $ip");
        shell_exec($comando);

        $xml_file = $output_prefix . '.xml';
        $output = file_exists($xml_file) ? file_get_contents($xml_file) : '';

        $puertos = [];
        $cves_detectados = [];
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
                        'extrainfo' => isset($port->service['extrainfo']) ? (string)$port->service['extrainfo'] : '',
                        'cves' => []
                    ];
                    if (isset($port->script)) {
                        foreach ($port->script as $script) {
                            if ((string)$script['id'] === 'vulners') {
                                preg_match_all('/(CVE-\d{4}-\d+)/', (string)$script['output'], $matches);
                                $puerto['cves'] = $matches[1] ?? [];
                                $cves_detectados = array_merge($cves_detectados, $puerto['cves']);
                            }
                        }
                    }
                    $puertos[] = $puerto;
                }
            }
        }
        $cves_detectados = array_unique($cves_detectados);

        $scanData = [
            'ip' => $ip,
            'timestamp' => date('Y-m-d_H-i-s'),
            'ports' => $puertos,
            'cves' => $cves_detectados,
            'raw' => $output
        ];
        $filename = $output_prefix . '.json';
        file_put_contents($filename, json_encode($scanData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Guarda el resumen para la IA (puedes ajustar el formato)
        $_SESSION['scan_summary'] = json_encode($scanData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        echo json_encode([
            'success' => true,
            'scan' => $output,
            'ports' => $puertos,
            'cves' => $cves_detectados
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
