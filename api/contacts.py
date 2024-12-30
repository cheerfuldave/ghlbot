
from http.server import BaseHTTPRequestHandler
from urllib.parse import parse_qs
import json
import os
import sys

# Add lib directory to Python path
sys.path.append(os.path.join(os.path.dirname(__file__), '..', 'lib'))

from gohighlevel_bot import GoHighLevelBot

def handle_request(event):
    try:
        # Parse query parameters
        query = parse_qs(event.get('queryStringParameters', {}))
        tag = query.get('tag', [None])[0]
        exclude_tags = query.get('exclude_tags', [])
        count_only = query.get('count_only', ['false'])[0].lower() == 'true'
        
        bot = GoHighLevelBot()
        
        if tag:
            if count_only:
                count = bot.get_contact_count_by_tag(tag)
                return {
                    'statusCode': 200,
                    'body': json.dumps({'count': count})
                }
            else:
                contacts = bot.get_contacts_by_tag(tag, exclude_tags)
                return {
                    'statusCode': 200,
                    'body': json.dumps({'contacts': contacts})
                }
        else:
            return {
                'statusCode': 400,
                'body': json.dumps({'error': 'Tag parameter is required'})
            }
            
    except Exception as e:
        return {
            'statusCode': 500,
            'body': json.dumps({'error': str(e)})
        }

class Handler(BaseHTTPRequestHandler):
    def do_GET(self):
        # Convert the request to the format expected by handle_request
        event = {
            'queryStringParameters': parse_qs(self.path.split('?')[1] if '?' in self.path else '')
        }
        
        response = handle_request(event)
        
        self.send_response(response['statusCode'])
        self.send_header('Content-type', 'application/json')
        self.end_headers()
        self.wfile.write(response['body'].encode())
        
def handler(event, context):
    return handle_request(event)
