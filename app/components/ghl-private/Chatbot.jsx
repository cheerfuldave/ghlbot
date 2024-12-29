
'use client';

import { useState } from 'react';
import GHLPrivateClient from '../../api/ghl-private/client';

export default function GHLPrivateChatbot({ token }) {
    const [message, setMessage] = useState('');
    const [responses, setResponses] = useState([]);
    const client = new GHLPrivateClient(token);

    const handleSendMessage = async (e) => {
        e.preventDefault();
        
        try {
            const response = await client.sendLiveChatMessage({
                message: message,
                type: 'text'
            });

            setResponses(prev => [...prev, {
                type: 'user',
                content: message
            }, {
                type: 'bot',
                content: response.data
            }]);
            
            setMessage('');
        } catch (error) {
            console.error('Error sending message:', error);
        }
    };

    return (
        <div className="flex flex-col h-full">
            <div className="flex-1 overflow-y-auto p-4 space-y-4">
                {responses.map((response, index) => (
                    <div key={index} className={`flex ${response.type === 'user' ? 'justify-end' : 'justify-start'}`}>
                        <div className={`max-w-[70%] rounded-lg p-3 ${
                            response.type === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200'
                        }`}>
                            {response.content}
                        </div>
                    </div>
                ))}
            </div>
            <form onSubmit={handleSendMessage} className="border-t p-4">
                <div className="flex space-x-4">
                    <input
                        type="text"
                        value={message}
                        onChange={(e) => setMessage(e.target.value)}
                        className="flex-1 rounded-lg border p-2"
                        placeholder="Type your message..."
                    />
                    <button
                        type="submit"
                        className="bg-blue-500 text-white px-4 py-2 rounded-lg"
                    >
                        Send
                    </button>
                </div>
            </form>
        </div>
    );
}
