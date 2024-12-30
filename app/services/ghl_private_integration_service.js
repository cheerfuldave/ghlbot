
import requests

class GHLPrivateIntegrationService {
    constructor() {
        this.BASE_URL = "https://services.leadconnectorhq.com"
        this.headers = {
            "Authorization": "Bearer pit-27e6ad48-1594-4bcf-8a2f-ddba9f69ff3a",
            "Version": "2021-07-28",
            "Content-Type": "application/json"
        }
        this.LOCATION_ID = "9nKzvZJBYUc1IPrDFa44"
    }

    async getCalendars() {
        const response = await fetch(`${this.BASE_URL}/calendars`, { headers: this.headers })
        return await response.json()
    }

    async getConversations() {
        const response = await fetch(`${this.BASE_URL}/conversations`, { headers: this.headers })
        return await response.json()
    }

    async getContacts() {
        const response = await fetch(`${this.BASE_URL}/contacts`, { headers: this.headers })
        return await response.json()
    }

    async sendMessage(conversationId, message) {
        const response = await fetch(`${this.BASE_URL}/conversations/messages`, {
            method: 'POST',
            headers: this.headers,
            body: JSON.stringify({
                conversationId,
                message,
                locationId: this.LOCATION_ID
            })
        })
        return await response.json()
    }

    async createContact(contactData) {
        const response = await fetch(`${this.BASE_URL}/contacts`, {
            method: 'POST',
            headers: this.headers,
            body: JSON.stringify({
                ...contactData,
                locationId: this.LOCATION_ID
            })
        })
        return await response.json()
    }

    async getCustomFields() {
        const response = await fetch(`${this.BASE_URL}/custom-fields`, { headers: this.headers })
        return await response.json()
    }

    async getTasks() {
        const response = await fetch(`${this.BASE_URL}/tasks`, { headers: this.headers })
        return await response.json()
    }

    async getTags() {
        const response = await fetch(`${this.BASE_URL}/tags`, { headers: this.headers })
        return await response.json()
    }

    async sendLiveChatMessage(message) {
        const response = await fetch(`${this.BASE_URL}/conversations/livechat/messages`, {
            method: 'POST',
            headers: this.headers,
            body: JSON.stringify({
                message,
                locationId: this.LOCATION_ID
            })
        })
        return await response.json()
    }
}

export default GHLPrivateIntegrationService
