
from gohighlevel_bot import GoHighLevelBot
import argparse

def main():
    parser = argparse.ArgumentParser(description='GoHighLevel Contacts CLI')
    parser.add_argument('--token', required=True, help='GoHighLevel API token')
    parser.add_argument('--location', required=True, help='Location ID')
    parser.add_argument('--tag', help='Tag to filter contacts')
    parser.add_argument('--exclude-tags', nargs='*', help='Tags to exclude')
    parser.add_argument('--count-only', action='store_true', help='Only show count')
    
    args = parser.parse_args()
    
    bot = GoHighLevelBot(args.token, args.location)
    
    if args.tag:
        if args.count_only:
            count = bot.get_contact_count_by_tag(args.tag)
            print(f"Number of contacts with tag '{args.tag}': {count}")
        else:
            contacts = bot.get_contacts_by_tag(args.tag, args.exclude_tags)
            print(contacts[['contactName', 'email', 'tags']])
    else:
        contacts = bot.fetch_all_contacts()
        print(f"Total contacts: {len(contacts)}")

if __name__ == '__main__':
    main()

    parser.add_argument('--express-endpoint', help='Express API endpoint path')
    parser.add_argument('--express-method', default='GET', help='HTTP method for Express API request')
    parser.add_argument('--express-data', help='Data for Express API request (JSON format)')

    if args.express_endpoint:
        try:
            data = None
            if args.express_data:
                import json
                data = json.loads(args.express_data)

            response = bot.handle_express_request(
                args.express_endpoint,
                method=args.express_method,
                data=data
            )
            print("Express API Response:", response)
        except Exception as e:
            print("Error handling Express API request:", e)
    