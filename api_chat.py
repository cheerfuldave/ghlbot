
from http.server import BaseHTTPRequestHandler
from .lib.gohighlevel_bot import VercelChatbot
import json
import os

class handler(BaseHTTPRequestHandler):
    def do_POST(self):
        content_length = int(self.headers['Content-Length'])
        post_data = self.rfile.read(content_length)
        data = json.loads(post_data.decode('utf-8'))
        
        # Initialize chatbot with environment variables
        chatbot = VercelChatbot(
            ghl_token=os.environ.get('GHL_API_TOKEN'),
            ghl_location_id=os.environ.get('GHL_LOCATION_ID'),
            openai_key=os.environ.get('OPENAI_API_KEY')
        )
        
        # Process the message
        response = chatbot.process_message(data.get('message', ''))
        
        self.send_response(200)
        self.send_header('Content-type', 'application/json')
        self.end_headers()
        self.wfile.write(json.dumps(response).encode())
