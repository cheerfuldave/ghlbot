
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
