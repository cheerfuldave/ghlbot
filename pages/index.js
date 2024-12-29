
import { useState } from 'react';
import Head from 'next/head';
import styles from '../styles/Home.module.css';

export default function Home() {
  const [selectedBot, setSelectedBot] = useState('standard');
  
  const botOptions = [
    {
      id: 'standard',
      name: 'Standard Bot',
      description: 'Standard GHL API Bot'
    },
    {
      id: 'private-integration',
      name: 'Private Integration Bot',
      description: 'Enhanced Bot using GHL Private Integration API'
    }
  ];

  return (
    <div className={styles.container}>
      <Head>
        <title>GHL Bot</title>
        <meta name="description" content="GHL Bot with Private Integration" />
      </Head>

      <main className={styles.main}>
        <h1 className={styles.title}>
          Welcome to GHL Bot
        </h1>

        <div className={styles.botSelector}>
          {botOptions.map((bot) => (
            <div
              key={bot.id}
              className={`${styles.botOption} ${selectedBot === bot.id ? styles.selected : ''}`}
              onClick={() => setSelectedBot(bot.id)}
            >
              <h3>{bot.name}</h3>
              <p>{bot.description}</p>
            </div>
          ))}
        </div>

        <div className={styles.chatContainer}>
          {/* Chat interface will be implemented here */}
        </div>
      </main>
    </div>
  );
}
