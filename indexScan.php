<?php
$resultado = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ip'])) {
    $ip = trim($_POST['ip']);
    // Validar que la IP es válida (IPv4 simple)
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        // Ejecutar el comando nmap (solo puertos rápidos)
        // IMPORTANTE: En producción, valida mucho mejor la entrada y configura permisos de ejecución.
        $comando = escapeshellcmd("nmap -F " . $ip);
        $output = shell_exec($comando);
        $resultado = "<pre>" . htmlspecialchars($output) . "</pre>";
    } else {
        $resultado = "<span style='color:red;'>IP inválida.</span>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Escaneo Nmap desde PHP</title>
    <style>
        body { font-family: Arial, sans-serif; background: #fafafa; padding: 2em;}
        form { margin-bottom: 1em;}
        input[type="text"] { padding: .3em; width: 200px;}
        input[type="submit"] { padding: .3em 1em;}
        pre { background: #222; color: #bada55; padding: 1em;}
    </style>
</head>
<body>
    <h2>Escaneo rápido con Nmap</h2>
    <form method="POST">
        <label for="ip">IP a escanear:</label>
        <input type="text" name="ip" id="ip" placeholder="192.168.1.1" required>
        <input type="submit" value="Escanear">
    </form>
    <?php if ($resultado) echo "<h3>Resultado del escaneo:</h3>" . $resultado; ?>
</body>
</html>

192.168.1.59