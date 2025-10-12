'''import sys
import os
import csv
from dotenv import load_dotenv
from google import genai

# Cargar variables de entorno desde .env
load_dotenv()

api_key = os.getenv("GOOGLE_GENAI_API_KEY")
if not api_key:
    print("ERROR: No se encontró la variable GOOGLE_GENAI_API_KEY en el entorno.")
    sys.exit(1)

client = genai.Client(api_key=api_key)

# Recibe el prompt contextualizado desde PHP
prompt = sys.argv[1]

# --- Cargar dataset de CVEs ---
cve_data = []
cve_path = os.path.join(os.path.dirname(__file__), 'dataset', 'cve.csv')
if os.path.exists(cve_path):
    with open(cve_path, newline='', encoding='utf-8') as csvfile:
        reader = csv.DictReader(csvfile)
        for row in reader:
            cve_data.append(row)

# --- Extraer servicios/puertos del resumen del escaneo ---
def extraer_servicios(scan_summary):
    """
    Extrae posibles servicios o puertos del resumen del escaneo.
    Ajusta esta función según el formato real de tu resumen.
    """
    servicios = []
    for line in scan_summary.splitlines():
        # Ejemplo: "53/tcp open domain"
        if "/" in line and ("tcp" in line or "udp" in line):
            servicios.append(line.strip())
    return servicios

# --- Buscar CVEs relevantes en el dataset ---
def buscar_cves(servicios):
    """
    Busca CVEs relevantes en el dataset según los servicios detectados.
    """
    resultados = []
    for servicio in servicios:
        for cve in cve_data:
            # Busca coincidencias simples por nombre de producto/servicio
            if servicio.lower() in cve.get('product', '').lower():
                resultados.append(f"CVE: {cve.get('cve_id')} - {cve.get('description')}")
    return resultados

# --- Separar resumen del escaneo y pregunta del usuario ---
if "Resumen del escaneo:" in prompt and "Pregunta del usuario:" in prompt:
    resumen = prompt.split("Resumen del escaneo:")[1].split("Pregunta del usuario:")[0].strip()
    pregunta = prompt.split("Pregunta del usuario:")[1].strip()
else:
    resumen = ""
    pregunta = prompt

servicios = extraer_servicios(resumen)
cve_relevantes = buscar_cves(servicios)

# --- Construir contexto enriquecido para la IA ---
contexto = (
    "IMPORTANTE: Eres un asistente experto en ciberseguridad y mitigación de vulnerabilidades. "
    "Solo puedes responder preguntas relacionadas con mitigación, seguridad informática, escaneo de puertos, servicios y CVEs. "
    "Si el usuario pregunta algo fuera de ese ámbito, responde: 'Solo puedo responder consultas sobre ciberseguridad y mitigación de vulnerabilidades.'\n\n"
    f"Resumen del escaneo:\n{resumen}\n\n"
)
if cve_relevantes:
    contexto += "Vulnerabilidades relevantes encontradas en el dataset:\n"
    contexto += "\n".join(cve_relevantes[:5]) + "\n\n"
contexto += f"Pregunta del usuario:\n{pregunta}"

# --- Llama al modelo de Gemini con el contexto enriquecido ---
response = client.models.generate_content(
    model="gemini-2.5-flash",
    contents=contexto
)

print(response.text.strip())'''
import sys
import os
import csv
import json
from dotenv import load_dotenv
from google import genai

load_dotenv()
api_key = os.getenv("GOOGLE_GENAI_API_KEY")
if not api_key:
    print("ERROR: No se encontró la variable GOOGLE_GENAI_API_KEY en el entorno.")
    sys.exit(1)
client = genai.Client(api_key=api_key)

prompt = sys.argv[1]

def load_csv_dict(path):
    data = []
    if os.path.exists(path):
        with open(path, newline='', encoding='utf-8') as csvfile:
            reader = csv.DictReader(csvfile)
            for row in reader:
                data.append(row)
    return data

cve_data = load_csv_dict(os.path.join(os.path.dirname(__file__), 'dataset', 'cve.csv'))

# --- Extraer resumen, historial y pregunta ---
import re
resumen = ""
historial = ""
pregunta = ""
if "Resumen del escaneo (JSON):" in prompt and "Historial de la conversación:" in prompt and "Pregunta del usuario:" in prompt:
    resumen = prompt.split("Resumen del escaneo (JSON):")[1].split("Historial de la conversación:")[0].strip()
    historial = prompt.split("Historial de la conversación:")[1].split("Pregunta del usuario:")[0].strip()
    pregunta = prompt.split("Pregunta del usuario:")[1].strip()
else:
    pregunta = prompt

# --- Parsear JSON del escaneo ---
cves_encontrados = []
try:
    scan_json = json.loads(resumen)
    cves_encontrados = scan_json.get('cves', [])
except Exception:
    pass

def detalles_cves(cve_ids):
    detalles = []
    for cve_id in cve_ids:
        for cve in cve_data:
            if cve.get('cve_id', '') == cve_id:
                detalles.append(f"{cve_id}: {cve.get('summary', '')} (CVSS: {cve.get('cvss', '')})")
                break
    return detalles

detalles = detalles_cves(cves_encontrados)

# --- Contexto frío y técnico con historial ---
contexto = (
    "Eres un asistente técnico de ciberseguridad. Responde de forma concisa, precisa y fría, solo lo necesario. "
    "Solo responde sobre mitigación, seguridad informática, escaneo de puertos, servicios y CVEs. "
    "Si la consulta es irrelevante, responde: 'Solo consultas de ciberseguridad y mitigación.'\n\n"
    f"Resumen del escaneo (JSON):\n{resumen}\n\n"
    f"Historial de la conversación:\n{historial}\n\n"
)
if detalles:
    contexto += "CVEs detectados y detalles técnicos:\n"
    contexto += "\n".join(detalles[:10]) + "\n\n"
contexto += f"Pregunta del usuario:\n{pregunta}"

response = client.models.generate_content(
    model="gemini-2.5-flash",
    contents=contexto
)
print(response.text.strip())