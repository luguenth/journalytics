# Journalytica

Journalytica is a web application that serves as a repository of journalists and their published articles. It allows you to crawl articles from different news journals via RSS feeds and save the authors and related information. With Journalytica, you can perform various analyses and gain insights into the relationships between journalists, newspapers, and articles.

## Features

- **Article Crawling**: Retrieve articles from various news journals through their RSS feeds.
- **Journalist Repository**: Store journalist profiles including their names, email addresses, and biographies.
- **Article Repository**: Save articles with details such as titles, publication dates, content, and source RSS feeds.
- **Newspaper Repository**: Maintain a collection of newspapers with their respective RSS feeds.
- **Analytical Capabilities**: Explore the relationships between journalists, newspapers, and articles through queries and analysis. (TODO)
- **Frontend Web Interface**: Access and interact with the data via a user-friendly web application.
- **API Support**: Provide an API for data retrieval and integration with external systems. (TODO)

## Technology Stack

Journalytica is built using the following technologies:

- Backend: Symfony (PHP)
- Frontend: Twig (PHP)
- Database: PostgreSQL
- Scraping: Python (for the web scraper)

## Installation

Follow these steps to set up Journalytica locally:

1. Clone the repository: `git clone [repository_url]`
2. Navigate to the project directory: `cd journalytica`
3. Install dependencies: `composer install` && `npm install`

## Usage

1. Start the Webpack dev server: `npm run watch`
2. Start PostgreSQL: `docker-compose up -d`
3. Start the Symfony server: `symfony server:start`

## TODO

- [ ] Add API support
- [ ] Add analytical capabilities
- [ ] Combine newspaper RSS feeds
- [ ] Add support for more newspapers
- [ ] Add support for languages
- [ ] Add support for more article types
- [ ] Add support for more article metadata

