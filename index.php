<?php // Lógica de escaneo movida a scan.php, solo frontend dinámico 
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fortify AI - Análisis de Amenazas</title>
    <link rel="icon" href="assets/img/escudo-blanco.svg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Botón flotante PDF */
        #pdfFloatBtn {
            position: fixed;
            right: 32px;
            bottom: 32px;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #e53935;
            color: #fff;
            border: none;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.18);
            font-size: 2rem;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.35s cubic-bezier(.4, 0, .2, 1);
        }

        #pdfFloatBtn.visible {
            opacity: 1;
            pointer-events: auto;
        }

        #pdfFloatBtn:hover {
            background: #b71c1c;
        }
    </style>
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
                // Cierra la modal si está abierta
                const scanModal = bootstrap.Modal.getInstance(document.getElementById('scanModal'));
                if (scanModal) scanModal.hide();

                if (iaChatHistory.length === 0 && lastScanSummary) {
                    sendToIA('Analiza el siguiente resultado de escaneo de seguridad y dame recomendaciones de mitigación claras y comprensibles para un usuario no técnico.\n\n' + lastScanSummary, true);
                }
            });

            // Enviar mensaje a backend IA (procesar.php)
            function sendToIA(prompt, isAuto = false) {
                appendIAChat('...', 'Esperando respuesta de la IA...');
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
                // Si es la primera respuesta de la IA, guardar y mostrar el botón PDF
                if (role === '🤖 IA' && iaChatHistory.filter(m => m.role === '🤖 IA').length === 1) {
                    // Quitar etiquetas HTML y convertir saltos de línea correctamente
                    let plainText = text;
                    if (isIAHtml) {
                        // Quitar etiquetas Markdown simples y HTML
                        plainText = plainText.replace(/<br\s*\/?>(\n)?/gi, "\n");
                        plainText = plainText.replace(/<[^>]+>/g, '');
                        // Quitar ** y * de Markdown
                        plainText = plainText.replace(/\*\*([^*]+)\*\*/g, '$1');
                        plainText = plainText.replace(/\*([^*]+)\*/g, '$1');
                    }
                    firstIAResponse = plainText;
                    if (lastScanData) {
                        showPdfButton(lastScanData);
                    }
                }
                const chat = document.getElementById('iaChat');
                let html = '';
                iaChatHistory.forEach((msg, idx) => {
                    if (msg.role === 'Tú') {
                        html += `<div class='msg-user'>${escapeHtml(msg.text)}</div>`;
                    } else if (msg.role === '🤖 IA') {
                        // Si es el último mensaje IA, animar
                        if (idx === iaChatHistory.length - 1) {
                            html += `<div class='msg-ia' id="ia-typewriter"></div>`;
                        } else {
                            html += `<div class='msg-ia'>${isIAHtml ? msg.text : escapeHtml(msg.text)}</div>`;
                        }
                    } else {
                        html += `<div class='msg-ia'>${escapeHtml(msg.text)}</div>`;
                    }
                });
                chat.innerHTML = html;
                chat.scrollTop = chat.scrollHeight;

                // Animación typewriter solo para el último mensaje IA
                const lastMsg = iaChatHistory[iaChatHistory.length - 1];
                if (lastMsg && lastMsg.role === '🤖 IA') {
                    const el = document.getElementById('ia-typewriter');
                    if (el) {
                        let i = 0;
                        const content = isIAHtml ? lastMsg.text.replace(/<br>/g, '\n') : escapeHtml(lastMsg.text);

                        function typeWriter() {
                            el.innerHTML = isIAHtml ?
                                content.slice(0, i).replace(/\n/g, '<br>') :
                                content.slice(0, i);
                            i++;
                            chat.scrollTop = chat.scrollHeight; // <-- Mantiene el scroll abajo
                            if (i <= content.length) {
                                setTimeout(typeWriter, 8);
                            } else {
                                el.innerHTML = isIAHtml ?
                                    content.replace(/\n/g, '<br>') :
                                    content;
                                chat.scrollTop = chat.scrollHeight;
                            }
                        }
                        typeWriter();
                    }
                }
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

            // ...existing code...

            // --- PDF FLOATING BUTTON LOGIC ---
            let lastScanData = null; // Guarda el último resultado de escaneo en JSON
            // Guarda la primera respuesta de la IA
            let firstIAResponse = null;

            // Llama a esta función cuando el escaneo termine exitosamente
            function showPdfButton(scanData) {
                lastScanData = scanData;
                const btn = document.getElementById('pdfFloatBtn');
                btn.classList.add('visible');
            }

            // Oculta el botón al iniciar un nuevo escaneo
            function hidePdfButton() {
                lastScanData = null;
                firstIAResponse = null;
                document.getElementById('pdfFloatBtn').classList.remove('visible');
            }

            // Maneja la descarga del PDF
            document.getElementById('pdfFloatBtn').addEventListener('click', function() {
                if (!lastScanData) return;
                const formData = new FormData();
                formData.append('scanData', JSON.stringify(lastScanData));
                if (firstIAResponse) {
                    formData.append('iaResponse', firstIAResponse);
                }
                fetch('generar_pdf.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(resp => resp.blob())
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'resultado_escaneo.pdf';
                        document.body.appendChild(a);
                        a.click();
                        setTimeout(() => {
                            window.URL.revokeObjectURL(url);
                            a.remove();
                        }, 100);
                    });
                            // Si es la primera respuesta de la IA, guardar y mostrar el botón PDF
                            if (role === '🤖 IA' && iaChatHistory.filter(m => m.role === '🤖 IA').length === 0) {
                                firstIAResponse = isIAHtml ? text.replace(/<br>/g, '\n') : text;
                                // Solo mostrar el botón si hay datos de escaneo
                                if (lastScanData) {
                                    showPdfButton(lastScanData);
                                }
                            }
            });

            // Ejemplo: Llama showPdfButton(scanData) cuando el escaneo termina
            // y hidePdfButton() cuando se inicia uno nuevo

            // Si tu main.js o lógica de escaneo ya tiene un callback al finalizar, llama showPdfButton(scanData) ahí.
            // Por ejemplo, después de mostrar el resultado del escaneo:
            window.showPdfButton = showPdfButton;
            window.hidePdfButton = hidePdfButton;

            // Al iniciar un nuevo escaneo:
            document.getElementById('scanForm').addEventListener('submit', function() {
                hidePdfButton();
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

    <!-- Modal para escaneo completo (SOLO resultado, NO chat IA) -->
    <div class="modal fade justify-content-center" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true" data-bs-backdrop="true">
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
                    <button type="button" class="btn mb-2 btn-open-chat" id="btnMitigacionIA" style="display:none;">
                        Obtener recomendaciones de mitigación IA
                    </button>
                    <button type="button" class="btn btn-close-modal mt-2" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Agrega esto justo antes del cierre de </body> en index.php -->

    <footer class="footer mt-5 py-4" style="background: linear-gradient(90deg, #265C4B 60%, #146551 100%); color: #fff; text-align: center; border-top-left-radius: 20px; border-top-right-radius: 20px;">
        <div class="container">
            <small>
                Trabajo de Unidad de Integración Curricular por <b>Juan Carlos Yasig</b> <br>
                Ingeniería en Tecnologías de la Información <br>Universidad de las Fuerzas Armadas ESPE Sede Santo Domingo<br>
                <i class="fas fa-phone-alt"></i> 0967212283
            </small>
        </div>
    </footer>

    <!-- Botón flotante PDF -->
    <button id="pdfFloatBtn" title="Descargar PDF">
        <i class="fas fa-file-pdf"></i>
    </button>
</body>

</html>