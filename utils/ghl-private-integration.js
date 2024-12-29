
export class GHLPrivateIntegration {
  constructor(token, locationId) {
    this.baseUrl = 'https://services.leadconnectorhq.com';
    this.headers = {
      'Authorization': `Bearer ${token}`,
      'Version': '2021-07-28',
      'Content-Type': 'application/json'
    };
    this.locationId = locationId;
  }

  async makeRequest(method, endpoint, data = null) {
    const response = await fetch(`${this.baseUrl}${endpoint}`, {
      method,
      headers: this.headers,
      body: data ? JSON.stringify(data) : null
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
  }

  async getCalendars() {
    return this.makeRequest('GET', '/calendars');
  }

  async getContacts() {
    return this.makeRequest('GET', '/contacts');
  }

  async getTasks() {
    return this.makeRequest('GET', '/tasks');
  }

  async sendMessage(data) {
    return this.makeRequest('POST', '/conversations/messages', data);
  }
}
