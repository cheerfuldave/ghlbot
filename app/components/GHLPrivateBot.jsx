
'use client'

import { useState, useEffect } from 'react'
import GHLPrivateIntegrationService from '../services/ghl_private_integration_service'

export default function GHLPrivateBot() {
    const [input, setInput] = useState('')
    const [messages, setMessages] = useState([])
    const [contacts, setContacts] = useState([])
    const [conversations, setConversations] = useState([])
    const [loading, setLoading] = useState(false)
    const ghlService = new GHLPrivateIntegrationService()

    useEffect(() => {
        // Load initial data
        loadData()
    }, [])

    const loadData = async () => {
        setLoading(true)
        try {
            const [contactsData, conversationsData] = await Promise.all([
                ghlService.getContacts(),
                ghlService.getConversations()
            ])
            setContacts(contactsData.contacts || [])
            setConversations(conversationsData.conversations || [])
        } catch (error) {
            console.error('Error loading data:', error)
            setMessages(prev => [...prev, {
                role: 'assistant',
                content: 'Error loading data. Please try again later.'
            }])
        }
        setLoading(false)
    }

    const handleSubmit = async (e) => {
        e.preventDefault()
        if (!input.trim()) return

        const userMessage = { role: 'user', content: input }
        setMessages(prev => [...prev, userMessage])
        setInput('')
        setLoading(true)

        try {
            const response = await processMessage(input)
            setMessages(prev => [...prev, {
                role: 'assistant',
                content: response
            }])
        } catch (error) {
            console.error('Error processing message:', error)
            setMessages(prev => [...prev, {
                role: 'assistant',
                content: 'Error processing your request. Please try again.'
            }])
        }
        
        setLoading(false)
    }

    const processMessage = async (message) => {
        // Basic command processing
        const command = message.toLowerCase()
        
        if (command.includes('list contacts')) {
            const contactsData = await ghlService.getContacts()
            return `Found ${contactsData.contacts.length} contacts. Here are the first 5:\n` +
                contactsData.contacts.slice(0, 5).map(c => 
                    `${c.firstName} ${c.lastName} (${c.email})`
                ).join('\n')
        }
        
        if (command.includes('list conversations')) {
            const conversationsData = await ghlService.getConversations()
            return `Found ${conversationsData.conversations.length} conversations. Here are the latest 5:\n` +
                conversationsData.conversations.slice(0, 5).map(c => 
                    `ID: ${c.id} - Last message: ${c.lastMessage || 'No messages'}`
                ).join('\n')
        }
        
        if (command.startsWith('send message')) {
            // Format: "send message to [conversation_id]: [message]"
            const match = command.match(/send message to ([^:]+):\s*(.+)/)
            if (match) {
                const [_, conversationId, messageContent] = match
                await ghlService.sendMessage(conversationId.trim(), messageContent.trim())
                return `Message sent to conversation ${conversationId}`
            }
        }
        
        if (command.includes('get tasks')) {
            const tasksData = await ghlService.getTasks()
            return `Here are your tasks:\n` +
                tasksData.tasks.slice(0, 5).map(t => 
                    `${t.title} - Due: ${t.dueDate}`
                ).join('\n')
        }
        
        if (command.includes('get tags')) {
            const tagsData = await ghlService.getTags()
            return `Available tags:\n` +
                tagsData.tags.slice(0, 10).map(t => t.name).join(', ')
        }

        // Default response for unrecognized commands
        return `Available commands:\n
        - list contacts
        - list conversations
        - send message to [conversation_id]: [message]
        - get tasks
        - get tags`
    }

    return (
        <div className="flex flex-col h-screen bg-gray-100">
            <div className="p-4 bg-white shadow-md">
                <h1 className="text-2xl font-bold text-gray-800">GHL Private Integration Bot</h1>
                {loading && <div className="text-sm text-gray-500">Loading...</div>}
            </div>
            
            <div className="flex-1 overflow-y-auto p-4 space-y-4">
                {messages.map((message, index) => (
                    <div key={index} className={`flex ${
                        message.role === 'user' ? 'justify-end' : 'justify-start'
                    }`}>
                        <div className={`max-w-3/4 p-3 rounded-lg ${
                            message.role === 'user' 
                                ? 'bg-blue-500 text-white' 
                                : 'bg-white text-gray-800 shadow-md'
                        }`}>
                            <pre className="whitespace-pre-wrap font-sans">
                                {message.content}
                            </pre>
                        </div>
                    </div>
                ))}
            </div>
            
            <form onSubmit={handleSubmit} className="p-4 bg-white border-t">
                <div className="flex space-x-2">
                    <input
                        type="text"
                        value={input}
                        onChange={(e) => setInput(e.target.value)}
                        className="flex-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Type a command (e.g., 'list contacts', 'get tasks')"
                        disabled={loading}
                    />
                    <button 
                        type="submit"
                        className={`px-4 py-2 bg-blue-500 text-white rounded-lg ${
                            loading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-600'
                        }`}
                        disabled={loading}
                    >
                        Send
                    </button>
                </div>
            </form>
        </div>
    )
}
