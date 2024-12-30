
from flask import Flask, request, jsonify
from gohighlevel_bot import GoHighLevelBot

app = Flask(__name__)

# Initialize the bot with environment variables for API token and location ID
import os
api_token = os.getenv('GHL_API_TOKEN')
location_id = os.getenv('GHL_LOCATION_ID')
bot = GoHighLevelBot(api_token, location_id)

@app.route('/')
def homepage():
    return "Welcome to the GoHighLevel Bot! Choose an option to proceed."

@app.route('/api/express', methods=['GET', 'POST', 'PUT', 'DELETE'])
def express_api():
    endpoint = request.args.get('endpoint')
    method = request.method
    data = request.json

    try:
        response = bot.handle_express_request(endpoint, method=method, data=data)
        return jsonify(response)
    except Exception as e:
        return jsonify({"error": str(e)}), 400

if __name__ == '__main__':
    app.run(debug=True)
