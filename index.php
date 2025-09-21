<?php // Lógica de escaneo movida a scan.php, solo frontend dinámico ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fortify AI - Análisis de Amenazas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt me-2"></i>Fortify AI
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text">
                    <i class="fas fa-code-branch me-1"></i>Iteración 2 - v1.0.2
                </span>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title">
                <i class="fas fa-robot me-3"></i>Fortify AI
            </h1>
            <p class="hero-subtitle">
                Sistema Inteligente de Análisis de Amenazas en Redes Corporativas
            </p>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="scan-card">
                        <h3 class="text-white mb-4">
                            <i class="fas fa-search me-2"></i>Análisis de Seguridad
                        </h3>

                        <form id="scanForm" method="POST">
                            <div class="mb-4">
                                <label for="ipAddress" class="form-label text-white-50">
                                    Dirección IP a Analizar
                                </label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="ipAddress"
                                        name="ipAddress"
                                        placeholder="Ej: 192.168.1.1"
                                        required
                                        pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$">
                                    <button class="btn btn-scan" type="submit" id="scanButton">
                                        <span class="scan-text">
                                            <i class="fas fa-search me-2"></i>Iniciar Escaneo
                                        </span>
                                        <span class="loading-spinner">
                                            <i class="fas fa-spinner fa-spin me-2"></i>Escaneando...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Área de mensajes -->
                        <div id="messageArea"></div>

                        <!-- Ejemplos de IP -->
                        <div class="ip-examples">
                            <p class="text-white-50 mb-3">
                                <i class="fas fa-lightbulb me-2"></i>Ejemplos de direcciones IP válidas:
                            </p>
                            <div class="ip-example" onclick="fillIP('192.168.1.1')">192.168.1.1</div>
                            <div class="ip-example" onclick="fillIP('10.0.0.1')">10.0.0.1</div>
                            <div class="ip-example" onclick="fillIP('172.16.0.1')">172.16.0.1</div>
                            <div class="ip-example" onclick="fillIP('8.8.8.8')">8.8.8.8</div>
                            <div class="ip-example" onclick="fillIP('1.1.1.1')">1.1.1.1</div>
                        </div>
                        <div class="features text-center">
                            <button id="showScanResultBtn" type="button" class="btn btn-scan scan-text" data-bs-toggle="modal" data-bs-target="#scanModal" style="display:none;">Ver resultado del escaneo</button>
                        </div>
                        <!-- Modal para escaneo completo SIEMPRE en el DOM, contenido dinámico -->
                        <div class="modal fade" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true" data-bs-backdrop="false">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header" style="background: linear-gradient(45deg, #3498db, #2ecc71);">
                                        <h5 class="modal-title" id="scanModalLabel">Resultado del escaneo Nmap</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body" id="scanModalBody">
                                        <div class="text-muted">No hay resultado de escaneo disponible.</div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Características del sistema -->
                        <div class="features">
                            <h5 class="text-white mb-3">
                                <i class="fas fa-cogs me-2"></i>Características del Sistema
                            </h5>
                            <div class="feature-item">
                                <i class="fas fa-check-circle feature-icon"></i>
                                Validación automática de direcciones IP
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-shield-alt feature-icon"></i>
                                Análisis de seguridad basado en IA
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-brain feature-icon"></i>
                                Recomendaciones inteligentes de mitigación
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-mobile-alt feature-icon"></i>
                                Interfaz responsiva y moderna
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>