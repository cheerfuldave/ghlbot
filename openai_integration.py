import openai
import os
from typing import Dict, Any

class OpenAIAssistant:
    def __init__(self):
        self.api_key = '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'
        openai.api_key = self.api_key
        
    async def process_message(self, message: str, context: Dict[str, Any] = None) -> str:
        try:
            completion = await openai.ChatCompletion.acreate(
                model="gpt-4",
                messages=[
                    {"role": "system", "content": "You are a helpful assistant managing GoHighLevel contacts."},
                    {"role": "user", "content": message}
                ]
            )
            return completion.choices[0].message.content
        except Exception as e:
            return f"Error processing message: {str(e)}"
