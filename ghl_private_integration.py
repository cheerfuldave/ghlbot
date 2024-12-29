
import requests
from typing import Dict, Any, Optional

class GHLPrivateIntegration:
    def __init__(self, token: str, location_id: str):
        self.base_url = "https://services.leadconnectorhq.com"
        self.headers = {
            "Authorization": f"Bearer {token}",
            "Version": "2021-07-28",
            "Content-Type": "application/json"
        }
        self.location_id = location_id

    def _make_request(self, method: str, endpoint: str, data: Optional[Dict] = None) -> Dict:
        url = f"{self.base_url}{endpoint}"
        response = requests.request(method, url, headers=self.headers, json=data)
        response.raise_for_status()
        return response.json()

    # Calendar endpoints
    def get_calendars(self) -> Dict:
        return self._make_request("GET", "/calendars")

    def get_calendar_events(self, calendar_id: str) -> Dict:
        return self._make_request("GET", f"/calendars/{calendar_id}/events")

    # Conversation endpoints
    def get_conversations(self) -> Dict:
        return self._make_request("GET", "/conversations")

    def send_message(self, data: Dict) -> Dict:
        return self._make_request("POST", "/conversations/messages", data)

    # Contact endpoints
    def get_contacts(self) -> Dict:
        return self._make_request("GET", "/contacts")

    def create_contact(self, data: Dict) -> Dict:
        return self._make_request("POST", "/contacts", data)

    # Task endpoints
    def get_tasks(self) -> Dict:
        return self._make_request("GET", "/tasks")

    def create_task(self, data: Dict) -> Dict:
        return self._make_request("POST", "/tasks", data)

    # Tag endpoints
    def get_tags(self) -> Dict:
        return self._make_request("GET", "/tags")

    def create_tag(self, data: Dict) -> Dict:
        return self._make_request("POST", "/tags", data)

    # Live chat endpoints
    def send_live_chat_message(self, data: Dict) -> Dict:
        return self._make_request("POST", "/conversations/livechat/messages", data)
