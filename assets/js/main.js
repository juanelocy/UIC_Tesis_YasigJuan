// Validación de IP en tiempo real
document.getElementById('ipAddress').addEventListener('input', function (e) {
    const ip = e.target.value;
    const messageArea = document.getElementById('messageArea');

    if (ip.length > 0) {
        if (validateIP(ip)) {
            showMessage('success', '✓ Dirección IP válida');
        } else if (ip.length >= 7) { // Solo mostrar error si hay suficientes caracteres
            showMessage('error', '✗ Formato de IP inválido. Use el formato xxx.xxx.xxx.xxx');
        }
    } else {
        clearMessage();
    }
});

// Función para validar IP
function validateIP(ip) {
    const ipPattern = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
    return ipPattern.test(ip);
}

// Función para llenar IP desde ejemplos
function fillIP(ip) {
    document.getElementById('ipAddress').value = ip;
    showMessage('success', '✓ Dirección IP válida');
}

// Función para manejar el envío del formulario (AJAX)
document.getElementById('scanForm').addEventListener('submit', handleScanAjax);
function handleScanAjax(event) {
    event.preventDefault();

    const ipInput = document.getElementById('ipAddress');
    const ip = ipInput.value.trim();
    const scanButton = document.getElementById('scanButton');
    const showScanResultBtn = document.getElementById('showScanResultBtn');
    const scanModalBody = document.getElementById('scanModalBody');

    if (!validateIP(ip)) {
        showMessage('error', '✗ Por favor, ingrese una dirección IP válida');
        return false;
    }

    showLoadingState(true);
    showMessage('info', '<i class="fas fa-info-circle me-2"></i>Iniciando análisis de la IP: ' + ip);

    // Ocultar botón y limpiar modal antes de nuevo escaneo
    showScanResultBtn.style.display = 'none';
    scanModalBody.innerHTML = '<div class="text-muted">No hay resultado de escaneo disponible.</div>';

    // Enviar petición AJAX a scan.php
    const formData = new FormData();
    formData.append('ipAddress', ip);
    fetch('scan.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        showLoadingState(false);
        if (data.success) {
            showMessage('success', '✓ Escaneo completado.');
            let html = '';
            // Mostrar tabla de puertos si existen
            if (Array.isArray(data.ports) && data.ports.length > 0) {
                html += `<div class="mb-3"><strong>Puertos y servicios detectados:</strong></div>`;
                html += `<div class="table-responsive"><table class="table table-sm table-dark table-bordered align-middle"><thead><tr><th>Puerto</th><th>Protocolo</th><th>Estado</th><th>Servicio</th><th>Producto</th><th>Versión</th><th>Extra</th></tr></thead><tbody>`;
                data.ports.forEach(p => {
                    html += `<tr><td>${p.portid}</td><td>${p.protocol}</td><td>${p.state}</td><td>${escapeHtml(p.service)}</td><td>${escapeHtml(p.product)}</td><td>${escapeHtml(p.version)}</td><td>${escapeHtml(p.extrainfo)}</td></tr>`;
                });
                html += `</tbody></table></div>`;
            } else {
                html += `<div class="text-warning">No se detectaron puertos abiertos.</div>`;
            }
            // Mostrar salida cruda de Nmap
            html += `<div class="mt-4"><strong>Salida completa de Nmap:</strong></div>`;
            html += `<pre style="font-size:0.95em; background:#222; color:#eee; padding:1em; border-radius:6px; max-height:60vh; overflow:auto;">${escapeHtml(data.scan)}</pre>`;
            scanModalBody.innerHTML = html;
            showScanResultBtn.style.display = 'inline-block';
        } else {
            showMessage('error', data.error || 'Error en el escaneo.');
        }
    })
    .catch(() => {
        showLoadingState(false);
        showMessage('error', 'Error de red o del servidor.');
    });
    return false;
}

// Función para limpiar todo al recargar la página
window.addEventListener('DOMContentLoaded', function () {
    const showScanResultBtn = document.getElementById('showScanResultBtn');
    const scanModalBody = document.getElementById('scanModalBody');
    if (showScanResultBtn) showScanResultBtn.style.display = 'none';
    if (scanModalBody) scanModalBody.innerHTML = '<div class="text-muted">No hay resultado de escaneo disponible.</div>';
});

// Función para escapar HTML en el resultado
function escapeHtml(text) {
    return text.replace(/[&<>"']/g, function (c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
    });
}

// Función para mostrar estado de carga
function showLoadingState(loading) {
    const scanButton = document.getElementById('scanButton');
    const scanText = scanButton.querySelector('.scan-text');
    const loadingSpinner = scanButton.querySelector('.loading-spinner');

    if (loading) {
        scanText.style.display = 'none';
        loadingSpinner.style.display = 'inline';
        scanButton.disabled = true;
    } else {
        scanText.style.display = 'inline';
        loadingSpinner.style.display = 'none';
        scanButton.disabled = false;
    }
}

// Función para mostrar mensajes
function showMessage(type, message) {
    const messageArea = document.getElementById('messageArea');
    const alertClass = type === 'error' ? 'alert-danger' :
        type === 'success' ? 'alert-success' : 'alert-info';

    messageArea.innerHTML = `
                <div class="alert ${alertClass} fade-in">
                    ${message}
                </div>
            `;
}

// Función para limpiar mensajes
function clearMessage() {
    document.getElementById('messageArea').innerHTML = '';
}

// Casos de prueba automáticos (solo para desarrollo)
function runTests() {
    const testCases = {
        valid: ['192.168.1.1', '10.0.0.1', '172.16.0.1', '8.8.8.8', '1.1.1.1'],
        invalid: ['256.1.1.1', '192.168.1', '192.168.1.1.1', 'not-an-ip', '999.999.999.999']
    };

    console.log('=== EJECUTANDO CASOS DE PRUEBA ===');

    console.log('Casos válidos:');
    testCases.valid.forEach(ip => {
        const result = validateIP(ip);
        console.log(`${ip}: ${result ? '✓ PASS' : '✗ FAIL'}`);
    });

    console.log('Casos inválidos:');
    testCases.invalid.forEach(ip => {
        const result = validateIP(ip);
        console.log(`${ip}: ${result ? '✗ FAIL' : '✓ PASS'}`);
    });
}

// Ejecutar pruebas automáticas al cargar la página (solo en desarrollo)
document.addEventListener('DOMContentLoaded', function () {
    console.log('CyberSecure AI - Iteración 1 iniciada');
    console.log('Para ejecutar casos de prueba: runTests()');
});

// CSS Animations y estilos para modal
const style = document.createElement('style');
style.textContent = `
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .alert-info {
        background: linear-gradient(45deg, rgba(52, 152, 219, 0.1), rgba(41, 128, 185, 0.1));
        color: #3498db;
        border-left: 4px solid #3498db;
    }
    .modal-content pre {
        background: #222;
        color: #eee;
        font-size: 0.95em;
        border-radius: 6px;
        max-height: 60vh;
        overflow: auto;
    }
`;
document.head.appendChild(style);
