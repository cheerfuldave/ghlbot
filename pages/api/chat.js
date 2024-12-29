
import { GHLPrivateIntegration } from '../../utils/ghl-private-integration';

export default async function handler(req, res) {
  if (req.method !== 'POST') {
    return res.status(405).json({ message: 'Method not allowed' });
  }

  const { message, botType } = req.body;

  try {
    const ghl = new GHLPrivateIntegration(
      process.env.GHL_PRIVATE_TOKEN,
      process.env.GHL_LOCATION_ID
    );

    let response;
    if (botType === 'private-integration') {
      // Handle private integration specific commands
      if (message.startsWith('/')) {
        const command = message.split(' ')[0];
        switch (command) {
          case '/calendars':
            response = await ghl.getCalendars();
            break;
          case '/contacts':
            response = await ghl.getContacts();
            break;
          case '/tasks':
            response = await ghl.getTasks();
            break;
          default:
            response = 'Available commands: /calendars, /contacts, /tasks';
        }
      } else {
        // Handle regular chat messages
        response = await ghl.sendMessage({
          message: message,
          type: 'text'
        });
      }
    }

    return res.status(200).json({ response });
  } catch (error) {
    console.error('Error:', error);
    return res.status(500).json({ message: 'Internal server error', error: error.message });
  }
}
