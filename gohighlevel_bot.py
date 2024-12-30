
import requests
import pandas as pd
from typing import Optional, List, Dict, Any

class GoHighLevelBot:
    def __init__(self, api_token: str, location_id: str):
        self.base_url = "https://services.leadconnectorhq.com"
        self.headers = {
            "Authorization": f"Bearer {api_token}",
            "Content-Type": "application/json",
            "Version": "2021-07-28"
        }
        self.location_id = location_id
        self._contacts_cache = None
        
    def fetch_all_contacts(self) -> pd.DataFrame:
        """Fetch all contacts from GoHighLevel API"""
        all_contacts = []
        page = 1
        
        while True:
            response = requests.get(
                f"{self.base_url}/contacts?locationId={self.location_id}&limit=100&page={page}", 
                headers=self.headers
            )
            
            if response.status_code != 200:
                break
                
            batch = response.json().get('contacts', [])
            if not batch:
                break
                
            all_contacts.extend(batch)
            page += 1
            
        self._contacts_cache = pd.DataFrame(all_contacts)
        return self._contacts_cache
    
    def get_contacts_by_tag(self, tag: str, exclude_tags: Optional[List[str]] = None) -> pd.DataFrame:
        """Get contacts with specific tag and optionally exclude other tags"""
        if self._contacts_cache is None:
            self.fetch_all_contacts()
            
        mask = self._contacts_cache['tags'].apply(lambda tags: tag in tags)
        if exclude_tags:
            for exclude_tag in exclude_tags:
                mask &= ~self._contacts_cache['tags'].apply(lambda tags: exclude_tag in tags)
                
        return self._contacts_cache[mask]
    
    def get_contact_count_by_tag(self, tag: str) -> int:
        """Get count of contacts with specific tag"""
        if self._contacts_cache is None:
            self.fetch_all_contacts()
        return self._contacts_cache['tags'].apply(lambda tags: tag in tags).sum()

    def create_express_endpoint(self, endpoint_path: str, method: str = 'GET') -> str:
        """Create a new Express API endpoint URL"""
        return f"{self.base_url}/express-api{endpoint_path}"
        
    def handle_express_request(self, endpoint_path: str, method: str = 'GET', data: Optional[Dict] = None) -> Dict[str, Any]:
        """Handle Express API requests"""
        url = self.create_express_endpoint(endpoint_path)
        headers = self.headers.copy()
        
        if method.upper() == 'GET':
            response = requests.get(url, headers=headers)
        elif method.upper() == 'POST':
            response = requests.post(url, headers=headers, json=data)
        elif method.upper() == 'PUT':
            response = requests.put(url, headers=headers, json=data)
        elif method.upper() == 'DELETE':
            response = requests.delete(url, headers=headers)
            
        response.raise_for_status()
        return response.json()
    
    # Calendar Endpoints
    def get_calendar_list(self):
        return self.handle_express_request('/calendars', method='GET')

    def get_calendar_events(self, calendar_id):
        return self.handle_express_request(f'/calendars/{calendar_id}/events', method='GET')

    def add_calendar_event(self, calendar_id, event_data):
        return self.handle_express_request(f'/calendars/{calendar_id}/events', method='POST', data=event_data)

    def update_calendar_event(self, calendar_id, event_id, event_data):
        return self.handle_express_request(f'/calendars/{calendar_id}/events/{event_id}', method='PUT', data=event_data)

    def remove_calendar_event(self, calendar_id, event_id):
        return self.handle_express_request(f'/calendars/{calendar_id}/events/{event_id}', method='DELETE')

    # Conversations Endpoints
    def get_all_conversations(self):
        return self.handle_express_request('/conversations', method='GET')

    def send_message(self, message_data):
        return self.handle_express_request('/conversations/messages', method='POST', data=message_data)

    def get_conversation_details(self, conversation_id):
        return self.handle_express_request(f'/conversations/{conversation_id}', method='GET')

    def update_conversation_status(self, conversation_id, status_data):
        return self.handle_express_request(f'/conversations/{conversation_id}/status', method='PUT', data=status_data)

    def remove_conversation(self, conversation_id):
        return self.handle_express_request(f'/conversations/{conversation_id}', method='DELETE')

    # Contacts Endpoints
    def get_contact_list(self):
        return self.handle_express_request('/contacts', method='GET')

    def create_contact(self, contact_data):
        return self.handle_express_request('/contacts', method='POST', data=contact_data)

    def update_contact(self, contact_id, contact_data):
        return self.handle_express_request(f'/contacts/{contact_id}', method='PUT', data=contact_data)

    def remove_contact(self, contact_id):
        return self.handle_express_request(f'/contacts/{contact_id}', method='DELETE')

    # Add more endpoints as needed based on paste.txt
    