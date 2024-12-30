
import axios from 'axios';

export const GHL_BASE_URL = 'https://services.leadconnectorhq.com';

export class GoHighLevelAPI {
    private token: string;
    private locationId: string;

    constructor(token: string, locationId: string) {
        this.token = token;
        this.locationId = locationId;
    }

    private getHeaders() {
        return {
            'Authorization': `Bearer ${this.token}`,
            'Content-Type': 'application/json',
            'Version': '2021-07-28'
        };
    }

    async getContacts() {
        try {
            const response = await axios.get(
                `${GHL_BASE_URL}/contacts?locationId=${this.locationId}`,
                { headers: this.getHeaders() }
            );
            return response.data;
        } catch (error) {
            console.error('Error fetching contacts:', error);
            throw error;
        }
    }

    async searchContacts(query: string) {
        try {
            const response = await axios.get(
                `${GHL_BASE_URL}/contacts/search?locationId=${this.locationId}&query=${query}`,
                { headers: this.getHeaders() }
            );
            return response.data;
        } catch (error) {
            console.error('Error searching contacts:', error);
            throw error;
        }
    }
}
