
import { useState } from 'react';
import styles from './Chat.module.css';

export default function Chat() {
    const [message, setMessage] = useState('');
    const [response, setResponse] = useState('');
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        try {
            const res = await fetch('/api/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message }),
            });

            const data = await res.json();
            setResponse(JSON.stringify(data, null, 2));
        } catch (error) {
            console.error('Error:', error);
            setResponse('Error processing request');
        }

        setLoading(false);
    };

    return (
        <div className={styles.container}>
            <h1>Chat with GoHighLevel Assistant</h1>
            <form onSubmit={handleSubmit}>
                <textarea
                    value={message}
                    onChange={(e) => setMessage(e.target.value)}
                    placeholder="Enter your message..."
                    rows={4}
                />
                <button type="submit" disabled={loading}>
                    {loading ? 'Processing...' : 'Send'}
                </button>
            </form>
            {response && (
                <div className={styles.response}>
                    <pre>{response}</pre>
                </div>
            )}
        </div>
    );
}
