import requests
import json
import click
import urllib.parse
from dotenv import load_dotenv
import os

# Load environment variables from .env file
load_dotenv()

# Get the Todoist API token from environment variables
TODOIST_API_TOKEN = os.getenv('TODOIST_API_TOKEN')

if not TODOIST_API_TOKEN:
    raise ValueError("Todoist API token not found. Please set it in the .env file.")

# Function to fetch projects from Todoist
def fetch_todoist_projects():
    url = 'https://api.todoist.com/rest/v2/projects'
    headers = {
        'Authorization': f'Bearer {TODOIST_API_TOKEN}'
    }
    response = requests.get(url, headers=headers)
    response.raise_for_status()
    return response.json()

# Function to fetch tasks from Todoist
def fetch_todoist_tasks(project_id):
    url = 'https://api.todoist.com/rest/v2/tasks'
    headers = {
        'Authorization': f'Bearer {TODOIST_API_TOKEN}'
    }
    params = {
        'project_id': project_id
    }
    response = requests.get(url, headers=headers, params=params)
    response.raise_for_status()
    return response.json()

# Function to delete a task from Todoist
def delete_todoist_task(task_id):
    url = f'https://api.todoist.com/rest/v2/tasks/{task_id}'
    headers = {
        'Authorization': f'Bearer {TODOIST_API_TOKEN}'
    }
    response = requests.delete(url, headers=headers)
    response.raise_for_status()

# Function to convert Todoist tasks to Things3 JSON format
def convert_to_things_json(tasks):
    things_tasks = []
    for task in tasks:
        things_task = {
            "type": "to-do",
            "attributes": {
                "title": task['content'],
                "notes": task.get('description', '')
            }
        }
        things_tasks.append(things_task)
    return things_tasks

# Function to generate Things3 import URL
def generate_things_url(things_tasks):
    json_data = json.dumps(things_tasks)
    encoded_data = urllib.parse.quote(json_data)
    return f'things:///json?data={encoded_data}'

@click.command()
@click.option('--delete', is_flag=True, help='Delete tasks from Todoist after processing')
def main(delete):
    # Fetch projects from Todoist
    projects = fetch_todoist_projects()

    # Find the inbox project ID
    inbox_project_id = None
    for project in projects:
        if project.get('is_inbox_project'):
            inbox_project_id = project['id']
            break

    if not inbox_project_id:
        raise ValueError("Inbox project not found in Todoist.")

    # Fetch tasks from the inbox project
    tasks = fetch_todoist_tasks(inbox_project_id)

    # Convert tasks to Things3 JSON format
    things_tasks = convert_to_things_json(tasks)

    # Generate Things3 import URL
    things_url = generate_things_url(things_tasks)

    # Print the Things3 import URL
    print(f'Click the link to import tasks into Things3: {things_url}')

    # Delete tasks from Todoist if the delete option is specified
    if delete:
        for task in tasks:
            delete_todoist_task(task['id'])
        print('Tasks deleted from Todoist.')

if __name__ == '__main__':
    main()
