import json
from http.server import BaseHTTPRequestHandler
from .ghl_private_api import GHLPrivateAPI

def handler(request):
    if request.method == 'GET':
        api = GHLPrivateAPI()
        
        # Parse query parameters
        params = request.query_params
        location_id = params.get('locationId')
        query = params.get('query')
        
        if query:
            data = api.search_contacts(query, location_id)
        else:
            data = api.get_contacts(location_id)
            
        return {
            'statusCode': 200,
            'body': json.dumps(data)
        }
    
    return {
        'statusCode': 405,
        'body': 'Method not allowed'
    }
