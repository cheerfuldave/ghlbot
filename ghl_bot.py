from ghl_private_api import GHLPrivateAPI
from openai_integration import OpenAIAssistant
import json

class GHLBot:
    def __init__(self):
        self.ghl_api = GHLPrivateAPI()
        self.ai = OpenAIAssistant()
    
    async def handle_message(self, message: str, location_id: str = None):
        try:
            # Process message with OpenAI
            ai_response = await self.ai.process_message(message)
            
            # If the message contains contact-related queries, fetch from GHL
            if any(keyword in message.lower() for keyword in ['contact', 'customer', 'client']):
                contacts = self.ghl_api.get_contacts(location_id)
                return {
                    'ai_response': ai_response,
                    'contacts': contacts
                }
            
            return {'ai_response': ai_response}
            
        except Exception as e:
            return {'error': str(e)}
