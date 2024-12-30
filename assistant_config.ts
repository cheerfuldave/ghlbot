
export const assistantConfig = {
    id: 'asst_QYnUXjrC7JrDGyGmVFHMsUD6',
    name: 'GoHighLevel Assistant',
    apiKey: process.env.OPENAI_API_KEY,
    model: 'gpt-4-1106-preview',
    tools: [
        {
            type: 'function',
            function: {
                name: 'fetch_contacts',
                description: 'Fetch contacts from GoHighLevel API',
                parameters: {
                    type: 'object',
                    properties: {},
                    required: []
                }
            }
        },
        {
            type: 'function',
            function: {
                name: 'filter_contacts_by_tag',
                description: 'Filter contacts by specific tags',
                parameters: {
                    type: 'object',
                    properties: {
                        tag: {
                            type: 'string',
                            description: 'Tag to filter contacts by'
                        },
                        exclude_tags: {
                            type: 'array',
                            items: {
                                type: 'string'
                            },
                            description: 'Tags to exclude from results'
                        }
                    },
                    required: ['tag']
                }
            }
        },
        {
            type: 'function',
            function: {
                name: 'count_contacts_by_tag',
                description: 'Count contacts with a specific tag',
                parameters: {
                    type: 'object',
                    properties: {
                        tag: {
                            type: 'string',
                            description: 'Tag to count contacts by'
                        }
                    },
                    required: ['tag']
                }
            }
        }
    ]
};
