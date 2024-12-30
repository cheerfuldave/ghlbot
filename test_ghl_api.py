
import os
from ghl_private_api import GHLPrivateAPI

def test_ghl_api():
    # Initialize API with token
    api = GHLPrivateAPI(os.environ.get('GHL_PRIVATE_TOKEN'))
    
    # Test contact operations
    print("Testing contact operations...")
    
    # Create a test contact
    test_contact = {
        "email": "test@example.com",
        "firstName": "Test",
        "lastName": "User",
        "phone": "+1234567890"
    }
    
    created_contact = api.create_contact(test_contact)
    print("Created contact:", created_contact)
    
    if 'id' in created_contact:
        contact_id = created_contact['id']
        
        # Update the contact
        update_data = {
            "firstName": "Updated Test"
        }
        updated_contact = api.update_contact(contact_id, update_data)
        print("Updated contact:", updated_contact)
        
        # Delete the test contact
        delete_result = api.delete_contact(contact_id)
        print("Delete contact result:", delete_result)
    
    # Test task operations
    print("\nTesting task operations...")
    
    # Create a test task
    test_task = {
        "title": "Test Task",
        "description": "This is a test task",
        "dueDate": "2024-12-31"
    }
    
    created_task = api.create_task(test_task)
    print("Created task:", created_task)
    
    if 'id' in created_task:
        task_id = created_task['id']
        
        # Update the task
        update_task_data = {
            "title": "Updated Test Task"
        }
        updated_task = api.update_task(task_id, update_task_data)
        print("Updated task:", updated_task)
        
        # Delete the test task
        delete_task_result = api.delete_task(task_id)
        print("Delete task result:", delete_task_result)

if __name__ == "__main__":
    test_ghl_api()
