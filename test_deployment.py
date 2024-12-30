
import requests
import json

def test_endpoints():
    base_url = "https://ghlbot.vercel.app"
    
    # Test OpenAI endpoint
    chat_response = requests.post(f"{base_url}/api/chat", json={
        "messages": [{"role": "user", "content": "Hello"}]
    })
    print("OpenAI Chat Response:", chat_response.status_code)
    
    # Test GHL endpoint
    ghl_response = requests.post(f"{base_url}/api/private-integrations", json={
        "endpoint": "contacts/list",
        "data": {"limit": 1}
    })
    print("GHL Response:", ghl_response.status_code)

if __name__ == "__main__":
    test_endpoints()
