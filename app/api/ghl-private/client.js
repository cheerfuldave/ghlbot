
import axios from 'axios';

const BASE_URL = 'https://services.leadconnectorhq.com';

class GHLPrivateClient {
    constructor(token) {
        this.client = axios.create({
            baseURL: BASE_URL,
            headers: {
                'Authorization': `Bearer ${token}`,
                'Version': '2021-07-28',
                'Content-Type': 'application/json'
            }
        });
    }

    // Calendars
    async getCalendars() {
        return this.client.get('/calendars');
    }

    async getCalendarEvents(calendarId) {
        return this.client.get(`/calendars/${calendarId}/events`);
    }

    // Conversations
    async getConversations() {
        return this.client.get('/conversations');
    }

    async sendMessage(data) {
        return this.client.post('/conversations/messages', data);
    }

    // Contacts
    async getContacts() {
        return this.client.get('/contacts');
    }

    async createContact(data) {
        return this.client.post('/contacts', data);
    }

    // Tasks
    async getTasks() {
        return this.client.get('/tasks');
    }

    async createTask(data) {
        return this.client.post('/tasks', data);
    }

    // Live Chat
    async sendLiveChatMessage(data) {
        return this.client.post('/conversations/livechat/messages', data);
    }
}

export default GHLPrivateClient;
