
import { useState } from 'react';
import GHLPrivateChatbot from './components/ghl-private/Chatbot';

export default function Home() {
    const [selectedBot, setSelectedBot] = useState('standard');
    const [token, setToken] = useState('');

    return (
        <main className="flex min-h-screen flex-col items-center justify-between p-24">
            <div className="z-10 max-w-5xl w-full items-center justify-between font-mono text-sm">
                <h1 className="text-4xl font-bold mb-8">GHL Bot</h1>
                
                <div className="mb-8">
                    <label className="block text-sm font-medium text-gray-700">Select Bot Type</label>
                    <select
                        value={selectedBot}
                        onChange={(e) => setSelectedBot(e.target.value)}
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="standard">Standard Bot</option>
                        <option value="private">Private Integration Bot</option>
                    </select>
                </div>

                {selectedBot === 'private' && (
                    <div className="mb-8">
                        <label className="block text-sm font-medium text-gray-700">Private Integration Token</label>
                        <input
                            type="text"
                            value={token}
                            onChange={(e) => setToken(e.target.value)}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Enter your GHL Private Integration token"
                        />
                    </div>
                )}

                <div className="h-[600px] w-full border rounded-lg overflow-hidden">
                    {selectedBot === 'private' ? (
                        token ? (
                            <GHLPrivateChatbot token={token} />
                        ) : (
                            <div className="flex items-center justify-center h-full">
                                Please enter your Private Integration token to start
                            </div>
                        )
                    ) : (
                        <StandardChatbot />
                    )}
                </div>
            </div>
        </main>
    );
}
