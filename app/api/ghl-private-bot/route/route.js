
import { NextResponse } from 'next/server';
import GHLPrivateClient from '../../../api/ghl-private/client';

export async function POST(request) {
    try {
        const { token, action, data } = await request.json();
        const client = new GHLPrivateClient(token);

        let response;
        switch (action) {
            case 'sendMessage':
                response = await client.sendLiveChatMessage(data);
                break;
            case 'getConversations':
                response = await client.getConversations();
                break;
            // Add more cases as needed
            default:
                return NextResponse.json({ error: 'Invalid action' }, { status: 400 });
        }

        return NextResponse.json(response.data);
    } catch (error) {
        return NextResponse.json({ error: error.message }, { status: 500 });
    }
}
