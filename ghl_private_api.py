
import os
import requests

class GHLPrivateAPI:
    def __init__(self, private_token=None):
        self.base_url = "https://services.leadconnectorhq.com"  # Private integrations endpoint
        self.private_token = private_token or os.environ.get('GHL_PRIVATE_TOKEN')
        if not self.private_token:
            raise ValueError("No private token provided")
        self.headers = {
            "Authorization": f"Bearer {self.private_token}",
            "Version": "2021-07-28",
            "Content-Type": "application/json"
        }

    def get_contacts(self, locationId=None):
        endpoint = f"{self.base_url}/contacts/search"
        params = {"locationId": locationId} if locationId else {}
        response = requests.post(endpoint, headers=self.headers, json=params)
        return response.json()

    def create_contact(self, contact_data, locationId):
        endpoint = f"{self.base_url}/contacts/"
        contact_data["locationId"] = locationId
        response = requests.post(endpoint, headers=self.headers, json=contact_data)
        return response.json()

    def update_contact(self, contact_id, contact_data):
        endpoint = f"{self.base_url}/contacts/{contact_id}"
        response = requests.put(endpoint, headers=self.headers, json=contact_data)
        return response.json()

    def delete_contact(self, contact_id):
        endpoint = f"{self.base_url}/contacts/{contact_id}"
        response = requests.delete(endpoint, headers=self.headers)
        return response.status_code

    def get_locations(self):
        endpoint = f"{self.base_url}/locations/"
        response = requests.get(endpoint, headers=self.headers)
        return response.json()

    def get_tasks(self, locationId):
        endpoint = f"{self.base_url}/tasks/search"
        params = {"locationId": locationId}
        response = requests.post(endpoint, headers=self.headers, json=params)
        return response.json()

    def create_task(self, task_data, locationId):
        endpoint = f"{self.base_url}/tasks/"
        task_data["locationId"] = locationId
        response = requests.post(endpoint, headers=self.headers, json=task_data)
        return response.json()
