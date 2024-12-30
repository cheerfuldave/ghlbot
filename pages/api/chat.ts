
import { NextApiRequest, NextApiResponse } from 'next';
import { assistantConfig } from '../../app/assistant-config';
import { VercelChatbot } from '../../lib/gohighlevel_bot';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
    if (req.method !== 'POST') {
        return res.status(405).json({ message: 'Method not allowed' });
    }

    try {
        const { message } = req.body;
        
        const chatbot = new VercelChatbot(
            process.env.GHL_API_TOKEN,
            process.env.GHL_LOCATION_ID,
            process.env.OPENAI_API_KEY
        );

        const response = await chatbot.process_message(message);
        
        res.status(200).json(response);
    } catch (error) {
        console.error('Error processing request:', error);
        res.status(500).json({ message: 'Internal server error' });
    }
}
