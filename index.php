<?php // Lógica de escaneo movida a scan.php, solo frontend dinámico 
?>
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
                    <i class="fas fa-code-branch me-1"></i>Iteración 5 - v1.0.5
                </span>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title">
                <i class="fas fa-shield-alt me-3"></i>Fortify AI
            </h1>
            <p class="hero-subtitle">
                Sistema Inteligente de Análisis de Amenazas en Redes Corporativas
            </p>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="scan-card">
                        <h3 class="text-white mb-4">
                            <i class="fas me-2"></i>Análisis de Seguridad
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
                        <!-- Modal para escaneo completo (SOLO resultado, NO chat IA) -->
                        <div class="modal fade" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true" data-bs-backdrop="false">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header" style="background: linear-gradient(45deg, var(--secondary-color), #589A8D); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                                        <h5 class="modal-title" id="scanModalLabel">Resultado del escaneo</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body" style="background: #265C4B;" id="scanModalBody">
                                        <div class="text-muted">No hay resultado de escaneo disponible.</div>
                                    </div>
                                    <div class="modal-footer flex-column align-items-stretch" style="background: #146551;">
                                        <button type="button" class="btn mb-2 btn-open-chat" id="btnMitigacionIA" style="display:none;">Obtener recomendaciones de mitigación IA</button>
                                        <button type="button" class="btn btn-close-modal mt-2" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Características del sistema -->
                        <div class="features">
                            <h5 class="text-white mb-3 text-center">
                                <i class="fas fa-cogs me-2"></i>Características del Sistema
                            </h5>
                            <div class="feature-item">
                                <i class="fas fa-check-circle feature-icon"></i>
                                Validación automática de direcciones IP
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-key feature-icon"></i>
                                Análisis de seguridad basado en IA
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-brain feature-icon"></i>
                                Recomendaciones inteligentes de mitigación
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-desktop feature-icon"></i>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- IA Chat Logic ---
            let lastScanSummary = null;
            let iaChatHistory = [];

            // Mostrar botón de mitigación IA tras escaneo
            function enableMitigacionBtn(scanSummary) {
                lastScanSummary = scanSummary;
                document.getElementById('btnMitigacionIA').style.display = 'inline-block';
            }

            // Mostrar chat IA y enviar resumen del escaneo
            document.getElementById('btnMitigacionIA').addEventListener('click', function() {
                document.getElementById('iaChatContainer').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('iaChatContainer').scrollIntoView({
                        behavior: 'smooth'
                    });
                }, 200);
                if (iaChatHistory.length === 0 && lastScanSummary) {
                    sendToIA('Analiza el siguiente resultado de escaneo de seguridad y dame recomendaciones de mitigación claras y comprensibles para un usuario no técnico.\n\n' + lastScanSummary, true);
                }
            });

            // Enviar mensaje a backend IA (procesar.php)
            function sendToIA(prompt, isAuto = false) {
                appendIAChat('...', '<i class="fas fa-spinner fa-spin"></i> Esperando respuesta de la IA...');
                fetch('procesar.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: 'prompt=' + encodeURIComponent(prompt)
                    })
                    .then(res => res.text())
                    .then(html => {
                        if (iaChatHistory.length > 0 && iaChatHistory[iaChatHistory.length - 1].role === '...') {
                            iaChatHistory.pop();
                        }
                        let iaResp = html;
                        if (iaResp.length > 20000) iaResp = iaResp.substring(0, 20000) + '...';
                        iaResp = iaResp.replace(/\n/g, '<br>');
                        appendIAChat('🤖 IA', iaResp, true);
                        // Habilitar SIEMPRE el prompt después de cada respuesta
                        document.getElementById('iaPromptInput').disabled = false;
                        document.getElementById('iaPromptInput').focus();
                    })
                    .catch(() => {
                        if (iaChatHistory.length > 0 && iaChatHistory[iaChatHistory.length - 1].role === '...') {
                            iaChatHistory.pop();
                        }
                        appendIAChat('🤖 IA', '⚠️ Error al comunicarse con la IA.');
                        document.getElementById('iaPromptInput').disabled = false;
                        document.getElementById('iaPromptInput').focus();
                    });
            }
            // Mostrar mensaje en el chat IA
            function appendIAChat(role, text, isIAHtml = false) {
                iaChatHistory.push({
                    role,
                    text
                });
                const chat = document.getElementById('iaChat');
                let html = '';
                iaChatHistory.forEach(msg => {
                    if (msg.role === 'Tú') {
                        html += `<div class='msg-user'>${escapeHtml(msg.text)}</div>`;
                    } else if (msg.role === '🤖 IA') {
                        html += `<div class='msg-ia'>${isIAHtml ? msg.text : escapeHtml(msg.text)}</div>`;
                    } else {
                        html += `<div class='msg-ia'>${escapeHtml(msg.text)}</div>`;
                    }
                });
                chat.innerHTML = html;
                chat.scrollTop = chat.scrollHeight;
            }

            // Manejar envío del prompt IA por AJAX
            document.getElementById('iaPromptForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const input = document.getElementById('iaPromptInput');
                const prompt = input.value.trim();
                if (!prompt) return;
                appendIAChat('Tú', prompt);
                input.value = '';
                input.disabled = true;
                sendToIA(prompt, false);
            });

            // Deshabilitar prompt hasta que la IA responda la primera vez
            document.getElementById('iaPromptInput').disabled = true;

            // Función para escapar HTML
            function escapeHtml(text) {
                return text.replace(/[&<>"']/g, function(c) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    } [c];
                });
            }

            // Exponer enableMitigacionBtn globalmente si lo usas desde main.js
            window.enableMitigacionBtn = enableMitigacionBtn;

            document.getElementById('scanForm').addEventListener('submit', function() {
                // Resetear historial y ocultar chat IA
                iaChatHistory = [];
                document.getElementById('iaChat').innerHTML = '';
                document.getElementById('iaChatContainer').style.display = 'none';
                document.getElementById('iaPromptInput').value = '';
                document.getElementById('iaPromptInput').disabled = true;
            });

        });
    </script>
    <div id="iaChatContainer" style="display:none; width:100%; max-width:1250px; margin:40px auto 0 auto;" class="row justify-content-center">
        <div class="col-lg-12">
            <div class="scan-card">
                <!-- Chat IA fuera de la modal, al final del body -->
                <div class="card mb-2">
                    <div class="card-header text-white text-center py-2 px-3" style="background: linear-gradient(45deg, var(--secondary-color), #589A8D); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                        <b>💬 Recomendaciones de Mitigación (IA)</b>
                    </div>
                    <div class="card-body bg-light chat-container" id="iaChat" style="height:auto; overflow-y:auto; padding:10px;"></div>
                    <div class="card-footer p-2">
                        <form id="iaPromptForm" class="d-flex">
                            <input type="text" name="prompt" id="iaPromptInput" class="form-control me-2" placeholder="Consulta a la IA sobre mitigación..." autocomplete="off" required>
                            <button class="btn btn-success">Enviar</button>
                        </form>
                    </div>
                </div>


            </div>

        </div>
    </div>




</body>

</html>