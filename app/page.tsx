
'use client'

import { useState } from 'react'
import GHLBot from '../components/GHLBot'
import GHLPrivateBot from '../components/GHLPrivateBot'

export default function Home() {
    const [botType, setBotType] = useState('standard')

    return (
        <main className="min-h-screen p-4">
            <div className="mb-4">
                <select 
                    value={botType} 
                    onChange={(e) => setBotType(e.target.value)}
                    className="p-2 border rounded"
                >
                    <option value="standard">Standard GHL Bot</option>
                    <option value="private">Private Integration Bot</option>
                </select>
            </div>
            
            {botType === 'standard' ? <GHLBot /> : <GHLPrivateBot />}
        </main>
    )
}
