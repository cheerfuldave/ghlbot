
import os
import requests
import json

def test_ghl_integration():
    # Test configuration
    token = os.getenv('Gohighlevel Private Integrations Token')
    location_id = "me0EnocxsZH10tkDWF5K"
    base_url = "https://services.leadconnectorhq.com"
    
    headers = {
        "Authorization": f"Bearer {token}",
        "Version": "2021-07-28",
        "Content-Type": "application/json"
    }
    
    # Test endpoints
    endpoints = [
        f"/locations/{location_id}",
        "/calendars",
        "/contacts"
    ]
    
    results = {}
    for endpoint in endpoints:
        try:
            response = requests.get(f"{base_url}{endpoint}", headers=headers)
            results[endpoint] = {
                "status": response.status_code,
                "success": response.status_code == 200
            }
        except Exception as e:
            results[endpoint] = {
                "status": "error",
                "message": str(e)
            }
    
    return results

# Run the test
test_results = test_ghl_integration()
print("GHL Integration Test Results:")
print(json.dumps(test_results, indent=2))
