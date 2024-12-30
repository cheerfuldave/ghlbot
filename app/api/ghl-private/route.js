
import { NextResponse } from 'next/server'
import GHLPrivateIntegrationService from '@/services/ghl_private_integration_service'

export async function POST(req) {
    try {
        const { message, action } = await req.json()
        const ghlService = new GHLPrivateIntegrationService()
        
        let response
        switch (action) {
            case 'getCalendars':
                response = await ghlService.getCalendars()
                break
            case 'getConversations':
                response = await ghlService.getConversations()
                break
            case 'getContacts':
                response = await ghlService.getContacts()
                break
            case 'sendMessage':
                const { conversationId, messageContent } = message
                response = await ghlService.sendMessage(conversationId, messageContent)
                break
            default:
                response = { error: 'Invalid action' }
        }
        
        return NextResponse.json(response)
    } catch (error) {
        return NextResponse.json({ error: error.message }, { status: 500 })
    }
}
