
import { NextApiRequest, NextApiResponse } from 'next';

const GHL_PRIVATE_TOKEN = process.env.GOHIGHLEVEL_PRIVATE_INTEGRATIONS_TOKEN;
const GHL_API_URL = 'https://services.gohighlevel.com/v1/';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
    if (req.method !== 'POST') {
        return res.status(405).json({ message: 'Method not allowed' });
    }

    try {
        const response = await fetch(GHL_API_URL + req.body.endpoint, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${GHL_PRIVATE_TOKEN}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(req.body.data)
        });

        const data = await response.json();
        return res.status(200).json(data);
    } catch (error) {
        return res.status(500).json({ message: 'Error processing request' });
    }
}
