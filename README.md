# Todoist Inbox to Things JSON

Fetches todos from your Todoist's inbox.  
Saves them as Things3 JSON to be imported in Things3.  
Gets title and note.

## Usage

- Clone repo
- Create and activate venv, e.g. `python -m venv venv`, `source venv/bin/activate`
- Install libraries, `pip install -r requirements.txt`
- Copy .env for your Todoist API key: `cp .env.example .env`
- Replace placeholder in `.env` with [your Todoist API key](https://todoist.com/de/help/articles/find-your-api-token-Jpzx9IIlB)
- Run script `python3 todoist-to-things.py`
- Append optional `--delete` to delete todos from Todoist's inbox after
- Click output in terminal to import as tasks in Things3
