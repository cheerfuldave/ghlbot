
import { describe, it, expect, beforeEach, jest } from 'jest'
import GHLPrivateIntegrationService from '../services/ghl_private_integration_service'

describe('GHLPrivateIntegrationService', () => {
    let service

    beforeEach(() => {
        service = new GHLPrivateIntegrationService()
    })

    it('should initialize with correct configuration', () => {
        expect(service.BASE_URL).toBe('https://services.leadconnectorhq.com')
        expect(service.headers.Authorization).toContain('Bearer')
        expect(service.LOCATION_ID).toBeDefined()
    })

    it('should handle getContacts request', async () => {
        global.fetch = jest.fn(() =>
            Promise.resolve({
                json: () => Promise.resolve({ contacts: [] })
            })
        )

        const result = await service.getContacts()
        expect(result).toHaveProperty('contacts')
        expect(fetch).toHaveBeenCalledWith(
            `${service.BASE_URL}/contacts`,
            expect.objectContaining({
                headers: service.headers
            })
        )
    })

    // Add more tests for other methods
})
