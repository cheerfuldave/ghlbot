import requests
from ghl_private_api import GHLPrivateAPI

def test_api():
    api = GHLPrivateAPI()
    
    # Test getting locations
    print('Testing get locations...')
    locations = api.get_locations()
    print(f'Found {len(locations)} locations')
    
    if locations:
        location_id = locations[0]['id']
        
        # Test getting contacts
        print('\nTesting get contacts...')
        contacts = api.get_contacts(location_id)
        print(f'Found {len(contacts)} contacts')
        
        # Test getting tags
        print('\nTesting get tags...')
        tags = api.get_tags(location_id)
        print(f'Found {len(tags)} tags')

if __name__ == '__main__':
    test_api()
