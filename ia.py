import sys
from google import genai

# Tu API Key aquí
client = genai.Client(api_key="AIzaSyAsC3EJVG-3qujAbjrqwXM2zoD6xnYFo78")

prompt = sys.argv[1]

response = client.models.generate_content(
    model="gemini-2.5-flash",
    contents=prompt
)

# Forzar salida sin saltos extraños
print(response.text.strip())