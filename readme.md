# Fortify AI - Análisis de Amenazas en Redes Corporativas

Fortify AI es una aplicación web que permite analizar la seguridad de dispositivos en una red local mediante escaneos automáticos de puertos y servicios usando Nmap. El sistema está diseñado para ser fácil de usar, visualmente atractivo y seguro.

## Características principales

- **Validación automática de direcciones IP**: El frontend valida en tiempo real que la IP ingresada sea válida antes de permitir el escaneo.
- **Escaneo rápido con Nmap**: El backend ejecuta Nmap sobre la IP proporcionada y retorna los resultados de puertos y servicios abiertos.
- **Interfaz moderna y responsiva**: Utiliza Bootstrap 5 y FontAwesome para una experiencia de usuario profesional.
- **Modal de resultados**: El resultado completo del escaneo se muestra en una ventana modal, permitiendo una visualización clara y sin recargar la página.
- **Flujo 100% dinámico**: El escaneo se realiza mediante AJAX, sin recargar la página, y los resultados solo se muestran tras un escaneo exitoso.

## Instalación y configuración del entorno

### 1. Requisitos previos

- **Linux** (recomendado, probado en Ubuntu/Debian)
- **Servidor web**: Apache, Nginx o XAMPP/LAMPP con soporte PHP 7+
- **Nmap** instalado y accesible desde terminal
- **Python 3.8+** instalado en el sistema

### 2. Instalación de Nmap

```bash
sudo apt update
sudo apt install nmap
```

### 3. Instalación de Python y dependencias IA

```bash
# Ubícate en la carpeta del proyecto
cd /opt/lampp/htdocs/tesis

# Crea un entorno virtual de Python
python3 -m venv venv

# Activa el entorno virtual
source venv/bin/activate

# Actualiza pip
pip install --upgrade pip

# Instala la librería de Gemini/Google Generative AI
pip install google-genai
```

> **Nota:** Si tienes problemas con el paquete, prueba también:
> ```
> pip install google-generativeai
> ```

### 4. Instalación de Nmap

```bash
source venv/bin/activate
pip install python-dotenv
```

### 5. Pruebas de conectividad IA

```bash
# Verifica que los archivos existan y sean ejecutables
ls -l /opt/lampp/htdocs/tesis/venv/bin/python
ls -l /opt/lampp/htdocs/tesis/ia.py

# Prueba la ejecución directa del script IA
/opt/lampp/htdocs/tesis/venv/bin/python /opt/lampp/htdocs/tesis/ia.py "prueba de conectividad IA"
```

### 6. Verifica la instalación de la librería

```bash
/opt/lampp/htdocs/tesis/venv/bin/pip show google-genai
# o
/opt/lampp/htdocs/tesis/venv/bin/pip show google-generativeai
```

### 7. Prueba de mitigación IA tras instalar la librería

```bash
/opt/lampp/htdocs/tesis/venv/bin/python /opt/lampp/htdocs/tesis/ia.py "prueba de mitigacion IA tras instalar libreria"
```

### 8. Permisos y configuración de Apache/LAMPP

- Asegúrate de que el usuario del servidor web (por ejemplo, `www-data` o `daemon`) tenga permisos de ejecución sobre los archivos y el entorno virtual.
- Si usas XAMPP/LAMPP, asegúrate de que el directorio del proyecto tenga permisos adecuados:
  ```bash
  sudo chown -R www-data:www-data /opt/lampp/htdocs/tesis
  sudo chmod -R 755 /opt/lampp/htdocs/tesis
  ```

### 9. Acceso a la aplicación

- Inicia Apache/XAMPP/LAMPP si no está corriendo.
- Accede desde tu navegador a:  
  ```
  http://localhost/tesis/
  ```


## Estructura del proyecto

```
/tesis
├── index.php                # Página principal con formulario y frontend
├── scan.php                 # Endpoint backend para ejecutar Nmap y devolver resultados (AJAX)
├── assets/
│   ├── css/
│   │   └── style.css        # Estilos personalizados
│   ├── js/
│   │   └── main.js          # Lógica de validación, AJAX y UI
│   └── img/                 # Imágenes y recursos gráficos
├── imagenes/                # Capturas y diagramas
│   └── ...
└── readme.md                # Este archivo
```

## Instalación y requisitos

- **Servidor web**: Apache, Nginx o similar con soporte PHP 7+
- **Nmap**: Debe estar instalado y accesible desde el servidor (por ejemplo, en Linux: `sudo apt install nmap`)
- **Permisos**: El usuario del servidor web debe tener permisos para ejecutar Nmap

## Uso

1. Accede a `index.php` desde tu navegador.
2. Ingresa una dirección IP válida de la red local.
3. Haz clic en "Iniciar Escaneo".
4. Cuando el escaneo termine, aparecerá el botón "Ver resultado del escaneo". Haz clic para ver el resultado completo en una modal.

## Seguridad
- El backend valida que la IP sea IPv4 antes de ejecutar Nmap.
- El comando se escapa con `escapeshellcmd` para evitar inyecciones.
- No se almacena ningún resultado ni IP en el servidor.

## Créditos
- Bootstrap 5 (CDN)
- FontAwesome (CDN)
- Nmap (https://nmap.org/)

---

## 📅 Plan del Trabajo de Integración Curricular con Metodología Iterativa

### Iteración 1: Interfaz inicial y captura de IP
1. **Planificación y requisitos**
   - Requerimiento: formulario en la aplicación web para ingresar una dirección IP.
   - Definición del entorno: servidor con PHP y Bootstrap.
2. **Análisis y diseño**
   - Diseño de la interfaz simple (campo IP + botón "Escanear").
   - Definición del flujo inicial (entrada IP → backend recibe la IP).
3. **Implementación**
   - Desarrollo del formulario en PHP con Bootstrap.
   - Validación de la IP (formato correcto).
4. **Pruebas**
   - Ingresar distintas IP válidas e inválidas.
   - Verificar respuesta del sistema (acepta válidas, rechaza inválidas).
5. **Evaluación y revisión**
   - Ajustar validaciones de entrada.
   - Documentar problemas iniciales y mejoras para la siguiente iteración.

### Iteración 2: Integración con Nmap (escaneo básico)
1. **Planificación y requisitos**
   - Ejecutar Nmap desde PHP sobre la IP ingresada.
   - Obtener puertos abiertos (SSH, HTTP, etc.).
2. **Análisis y diseño**
   - Diseñar la estructura de salida del escaneo (texto/JSON).
   - Definir los parámetros iniciales del escaneo (puertos comunes).
3. **Implementación**
   - Ejecutar comando Nmap desde backend PHP (ejemplo: nmap -sV ip).
   - Capturar y mostrar resultados básicos en pantalla.
4. **Pruebas**
   - Escaneo sobre una máquina virtual Metasploitable.
   - Validar detección de servicios expuestos.
5. **Evaluación y revisión**
   - Ajustar tiempos de ejecución y formatos de salida.
   - Decidir si se amplía a escaneo de todos los puertos.

### Iteración 3: Procesamiento y estructuración de resultados
1. **Planificación y requisitos**
   - Convertir salida Nmap en formato estructurado (ejemplo JSON).
   - Guardar resultados para futuras consultas.
2. **Análisis y diseño**
   - Definir modelo de datos (servicio, puerto, versión).
   - Diseñar tabla o estructura en base de datos (opcional).
3. **Implementación**
   - Parsear resultados de Nmap.
   - Almacenar resultados en archivo o base de datos.
4. **Pruebas**
   - Comparar salida de Nmap vs estructura generada.
   - Validar consistencia de datos (puertos coinciden).
5. **Evaluación y revisión**
   - Ajustar el parseo para diferentes escenarios.
   - Preparar datos como entrada del modelo de IA.

### Iteración 4: Modelo de IA (recomendaciones de mitigación)
1. **Planificación y requisitos**
   - Entrenar modelo experto con dataset (CVE + OWASP Top 10).
   - Generar recomendaciones a partir de datos del escaneo.
2. **Análisis y diseño**
   - Selección de técnicas (modelo basado en reglas, embeddings gratuitos, etc.).
   - Mapear vulnerabilidades → recomendaciones (ejemplo: “SSH puerto 22 abierto → reforzar autenticación por clave pública”).
3. **Implementación**
   - Entrenamiento / configuración del modelo.
   - Integración inicial del modelo con backend.
4. **Pruebas**
   - Escaneo de Metasploitable con servicios conocidos.
   - Validar recomendaciones generadas (comparar con OWASP/CVE oficial).
5. **Evaluación y revisión**
   - Ajustar calidad de recomendaciones.
   - Revisar que sean comprensibles para usuarios con poco conocimiento técnico.

### Iteración 5: Integración completa (IA + Web + Prompt)
1. **Planificación y requisitos**
   - Mostrar resultados del escaneo + recomendaciones de IA en la interfaz web.
   - Incluir un campo (prompt) para interacción con el modelo.
2. **Análisis y diseño**
   - Diseño de la vista de resultados (tabla de puertos + recomendaciones).
   - Integración del prompt para preguntas del usuario.
3. **Implementación**
   - Frontend en Bootstrap mostrando resultados.
   - Backend conecta escaneo → IA → respuesta.
4. **Pruebas**
   - Usuario ingresa IP, recibe resultados estructurados + recomendaciones.
   - Uso del prompt para consultas adicionales.
5. **Evaluación y revisión**
   - Validar usabilidad del sistema.
   - Ajustar interfaz para mejor experiencia de usuario.

### Iteración 6: Validación final y despliegue
1. **Planificación y requisitos**
   - Validar el sistema completo en entorno controlado (máquinas vulnerables).
   - Documentar resultados finales.
2. **Análisis y diseño**
   - Plan de pruebas con diferentes escenarios de red.
   - Estrategia de despliegue (servidor local o nube).
3. **Implementación**
   - Configurar despliegue en servidor.
   - Generar dataset de pruebas con múltiples escaneos.
4. **Pruebas**
   - Escaneos en diferentes entornos.
   - Validar consistencia de resultados y recomendaciones.
5. **Evaluación y revisión**
   - Ajustar sistema en base a pruebas reales.
   - Documentar limitaciones y posibles mejoras futuras.
