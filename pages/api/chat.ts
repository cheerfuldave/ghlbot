
import { Configuration, OpenAIApi } from 'openai';
import { NextApiRequest, NextApiResponse } from 'next';

const configuration = new Configuration({
    apiKey: process.env.OPENAI_API_KEY,
});

const openai = new OpenAIApi(configuration);

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
    if (req.method !== 'POST') {
        return res.status(405).json({ message: 'Method not allowed' });
    }

    try {
        const completion = await openai.createChatCompletion({
            model: "gpt-4",
            messages: req.body.messages,
            temperature: 0.7,
        });

        return res.status(200).json(completion.data);
    } catch (error) {
        return res.status(500).json({ message: 'Error processing request' });
    }
}
