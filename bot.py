

# Add Private Integration Bot class
class PrivateIntegrationBot(BaseBot):
    def __init__(self, token: str, location_id: str):
        super().__init__()
        self.ghl = GHLPrivateIntegration(token, location_id)

    async def handle_message(self, message: str) -> str:
        # Add private integration specific message handling
        try:
            # Basic command handling
            if message.startswith("/calendars"):
                return str(self.ghl.get_calendars())
            elif message.startswith("/contacts"):
                return str(self.ghl.get_contacts())
            elif message.startswith("/tasks"):
                return str(self.ghl.get_tasks())
            elif message.startswith("/tags"):
                return str(self.ghl.get_tags())
            else:
                return "Available commands: /calendars, /contacts, /tasks, /tags"
        except Exception as e:
            return f"Error processing request: {str(e)}"
