import sys
import os
from dotenv import load_dotenv
from google import genai

# Cargar variables de entorno desde .env
load_dotenv()

api_key = os.getenv("GOOGLE_GENAI_API_KEY")
if not api_key:
    print("ERROR: No se encontró la variable GOOGLE_GENAI_API_KEY en el entorno.")
    sys.exit(1)

client = genai.Client(api_key=api_key)

prompt = sys.argv[1]

response = client.models.generate_content(
    model="gemini-2.5-flash",
    contents=prompt
)

print(response.text.strip())