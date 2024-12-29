
import click
from gohighlevel_bot import GoHighLevelBot
import os
import json

@click.group()
def cli():
    """GoHighLevel Bot CLI"""
    pass

@cli.command()
@click.option('--api-token', required=True, help='GoHighLevel API token')
@click.option('--location-id', required=True, help='Location ID')
@click.option('--tag', required=True, help='Tag to filter contacts')
def get_contacts(api_token, location_id, tag):
    """Get contacts by tag"""
    bot = GoHighLevelBot(api_token, location_id)
    contacts = bot.get_contacts_by_tag(tag)
    click.echo(contacts.to_json(orient='records'))

@cli.command()
@click.option('--api-token', required=True, help='GoHighLevel API token')
@click.option('--location-id', required=True, help='Location ID')
def get_calendars(api_token, location_id):
    """Get all calendars"""
    bot = GoHighLevelBot(api_token, location_id)
    calendars = bot.get_calendar_list()
    click.echo(json.dumps(calendars, indent=2))

@cli.command()
@click.option('--api-token', required=True, help='GoHighLevel API token')
@click.option('--location-id', required=True, help='Location ID')
@click.option('--calendar-id', required=True, help='Calendar ID')
def get_events(api_token, location_id, calendar_id):
    """Get calendar events"""
    bot = GoHighLevelBot(api_token, location_id)
    events = bot.get_calendar_events(calendar_id)
    click.echo(json.dumps(events, indent=2))

@cli.command()
@click.option('--api-token', required=True, help='GoHighLevel API token')
@click.option('--location-id', required=True, help='Location ID')
def get_conversations(api_token, location_id):
    """Get all conversations"""
    bot = GoHighLevelBot(api_token, location_id)
    conversations = bot.get_conversations()
    click.echo(json.dumps(conversations, indent=2))

if __name__ == '__main__':
    cli()
