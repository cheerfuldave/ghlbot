
# GHL Bot with Private Integration Support

This project now includes support for GoHighLevel's Private Integration API, allowing for more direct and powerful interactions with the GHL platform.

## Features

- Standard GHL API Integration
- Private Integration Support
- Real-time chat interface
- Contact management
- Task management
- Tag management
- Conversation handling

## Environment Setup

Create a `.env` file with the following variables:

```env
GHL_API_TOKEN=your_api_token
GHL_PRIVATE_TOKEN=your_private_token
GHL_LOCATION_ID=your_location_id
```

## Available Commands

The Private Integration bot supports the following commands:

- `list contacts`: Display the first 5 contacts
- `list conversations`: Show the latest 5 conversations
- `send message to [conversation_id]: [message]`: Send a message to a specific conversation
- `get tasks`: Display current tasks
- `get tags`: List available tags

## Development

1. Install dependencies:
   ```bash
   npm install
   ```

2. Run the development server:
   ```bash
   npm run dev
   ```

3. Run tests:
   ```bash
   npm test
   ```

## Private Integration Features

The bot now supports direct integration with GHL's private API endpoints:

- Calendar management
- Contact management
- Conversation handling
- Task management
- Tag management
- Live chat messaging

## Security

Ensure your private integration token and location ID are kept secure and never committed to version control.
