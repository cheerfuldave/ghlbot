
'use client';

import React, { useState, useEffect } from 'react';
import { GoHighLevelAPI } from '../ghl-api';
import styles from '../page.module.css';

export default function GHLChat() {
    const [messages, setMessages] = useState([]);
    const [input, setInput] = useState('');
    const [ghlApi, setGhlApi] = useState(null);

    useEffect(() => {
        // Initialize GHL API with environment variables
        const api = new GoHighLevelAPI(
            process.env.NEXT_PUBLIC_GHL_TOKEN,
            process.env.NEXT_PUBLIC_GHL_LOCATION_ID
        );
        setGhlApi(api);
    }, []);

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!input.trim()) return;

        // Add user message
        const userMessage = { role: 'user', content: input };
        setMessages(prev => [...prev, userMessage]);

        try {
            // Search contacts based on user input
            const searchResults = await ghlApi.searchContacts(input);
            
            // Format and add assistant response
            const assistantResponse = {
                role: 'assistant',
                content: `Found ${searchResults.contacts?.length || 0} matching contacts.`,
                data: searchResults
            };
            setMessages(prev => [...prev, assistantResponse]);
        } catch (error) {
            console.error('Error:', error);
            setMessages(prev => [...prev, {
                role: 'assistant',
                content: 'Sorry, there was an error processing your request.'
            }]);
        }

        setInput('');
    };

    return (
        <div className={styles.chatContainer}>
            <div className={styles.messagesContainer}>
                {messages.map((msg, idx) => (
                    <div key={idx} className={styles[msg.role]}>
                        {msg.content}
                        {msg.data && (
                            <pre className={styles.codeBlock}>
                                {JSON.stringify(msg.data, null, 2)}
                            </pre>
                        )}
                    </div>
                ))}
            </div>
            <form onSubmit={handleSubmit} className={styles.inputForm}>
                <input
                    type="text"
                    value={input}
                    onChange={(e) => setInput(e.target.value)}
                    placeholder="Search contacts..."
                    className={styles.input}
                />
                <button type="submit" className={styles.button}>
                    Send
                </button>
            </form>
        </div>
    );
}
