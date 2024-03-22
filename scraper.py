import feedparser
import json
import yaml
import re
import requests
import newspaper
import argparse
from datetime import datetime, timedelta,timezone
from dateutil.parser import parse


# Parse command line arguments
parser = argparse.ArgumentParser(description='Process some integers.')
parser.add_argument('--time', type=str, help='Timestamp or time difference in minutes')
args = parser.parse_args()

# Convert the time argument to a datetime object
if args.time:
    try:
        # Try to convert the argument to an integer (time difference in minutes)
        time_diff = int(args.time)
        time_limit = datetime.now(timezone.utc) - timedelta(minutes=time_diff)
    except ValueError:
        # If that fails, try to convert the argument to a datetime object (timestamp)
        time_limit = datetime.strptime(args.time, '%Y-%m-%d %H:%M:%S')
else:
    time_limit = None


# List of RSS feed URLs
with open('rssfeeds.yml') as f:
    feed_settings = yaml.safe_load(f)

data = {"articles": []}

for feed_setting in feed_settings["feeds"]:
    rss_feed = feed_setting["url"]
    cleanup_rules = feed_setting.get("cleanup_rules", {})
    manual_lookup = feed_setting["manual_lookup"]

    # Parse the RSS feed
    try:
        feed = feedparser.parse(rss_feed)
    except Exception as e:
        print("Failed to parse the RSS feed:", rss_feed)
        print("Error:", e)
        continue

    for post in feed.entries:
        post_time = parse(post.published)        
        if time_limit and post_time < time_limit:
            continue
        print(post.description if 'description' in post else "unkown")
        article = {
            "newspaper_name": feed.feed.title,
            "newspaper_link": feed.feed.link,
            "article_title": post.title,
            "article_link": post.link,
            "article_publishing_date": post.published if 'published' in post else "unknown",
            "article_description": post.description if 'description' in post else "unknown",
        }
        
        # Extract author names and apply cleanup rules
        author_names = []
        if 'dc_creator' in post:
            author_names = [post.dc_creator]
        elif 'author' in post:
            author_names = [post.author]

        # If manual lookup is needed
        if manual_lookup:
            if False:
                pattern = r'(vV)on\s+(.*?)\.?$'
                match = re.search(pattern, article["article_description"])
                if match:
                    names = match.group(1).split(' und ')
                    names = [name.strip() for name in names]
                    author_names = [', '.join(names)]
            else:
                article_url = article["article_link"]
                try:
                    parsed_article = newspaper.article(article_url, language='de')
                    author_names = parsed_article.authors
                except Exception as e:
                    print("Failed to parse the article:", article_url)
                    print("Error:", e)
                    continue

        # apply replace rule
        replace_rule = cleanup_rules.get("replace")
        if replace_rule:
            # find first occurence of the pattern and replace everthing in front of it and the pattern itself
            author_names = [re.sub(replace_rule[0], replace_rule[1], author, count=1) for author in author_names]
            # if string now is empty or only whitespace, set to unknown
            author_names = [author if author and author.strip() else "unknown" for author in author_names]


        #if commas are used, split by comma and separate them into different authors
        author_names = [author.split(",") for author in author_names]

        # remove all whitespaces from the beginning and end of the string
        author_names = [[author.strip() for author in authors] for authors in author_names]

        # if now is empty or only whitespace, set to unknown
        author_names = [[author if author and author.strip() else "unknown" for author in authors] for authors in author_names]

        print(author_names)
        article["author_names"] = author_names
        data["articles"].append(article)

# Define the API endpoint URL
api_url = "http://localhost:8000/api/v1/articles"

# Send the JSON data as a POST request to the API
response = requests.post(api_url, json=data)

# Check the response status
if response.status_code == 200:
    print("Data sent successfully to the API.")
else:
    print("Failed to send data to the API.")
    print("Response:", response.text)
    #print("Body:", json_data)