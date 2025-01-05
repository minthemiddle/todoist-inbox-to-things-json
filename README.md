# Todoist Inbox to Things JSON

Fetches todos from your Todoist's inbox.  
Saves them as Things3 JSON to be imported in Things3.  
Gets title and note.

Available in both Python and PHP versions.

## Common Setup Steps

1. [Activate URL Scheme](https://culturedcode.com/things/support/articles/2803573/) in Things3
2. Open terminal (such as *iTerm*)
3. Clone repo: `git clone https://github.com/minthemiddle/todoist-inbox-to-things-json.git`
4. Copy .env for your Todoist API key: `cp .env.example .env`
5. Replace placeholder in `.env` with [your Todoist API key](https://todoist.com/de/help/articles/find-your-api-token-Jpzx9IIlB)

## Python Version

### Installation
- Create and activate venv:  `python -m venv venv && source venv/bin/activate`  
- Install libraries: `pip install -r requirements.txt`

### Usage
- Run script: `python3 todoist-to-things.py`
- Append optional `--delete` to delete todos from Todoist's inbox after
- Click output link in terminal to import as tasks in Things3

## PHP Version

### Installation
- Install dependencies: `composer install`

### Usage
- Run script: `php todoist-to-things.php`
- Append optional `--delete` to delete todos from Todoist's inbox after
- Click output link in terminal to import as tasks in Things3
