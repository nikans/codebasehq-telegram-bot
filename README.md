# CodebaseHQ Telegram bot
Hackathonish version of the Telegram notifications bot for [CodebaseHQ](https://www.codebasehq.com)

I'm in no way a PHP programmer, but this bot works quite well in [Telecan](http://telecan.ru) company. It also has no reason to be opensource, but why the hell not.

![Screenshot](https://bot.nikans.com/telecan/trash/screen1.png "CodebaseHQ Telegram bot")

# Features

This bot sends you notifications regarding your assigned projects. 

Currently available events (pretty much [everything you can get from CodebaseHQ](https://support.codebasehq.com/articles/getting-started/how-do-i-setup-notifications)):

- Ticket creation and update
- Repo push
- Exception raised
- Deploy.hq deployment

## Commands

### Search tickets:
`/tickets` project_name [search_options]
- `project_name`: fuzzy search for match in project name or permalink
- `search_options`: [quick search options](https://support.codebasehq.com/articles/tickets/quick-search) from CodebaseHQ (optional, though, recommended)

### Your assigned unresolved tickets:
`/my_tickets` [project_name search_options]
- `project_name` and `search_options` are optional

You may use shortcuts: `\t` and `\mt` respectively.

CodebaseHQ search parameters: https://support.codebasehq.com/articles/tickets/quick-search

### Help
`/help`

## Some nice stuff

- Bot highlights messages requiring your attention with ‼️
- It gives you direct links to anything useful regarding the event
- Messages contain #hashtags for easy navigation and grouping
- You can forward a ticket you've just created to the assignee to get his attention (currently, you're notified of your own actions, too. You can switch it off... somewhere in the code)

# Setup

1) Copy config files from `config_sample/` to `config/` and set them up accordingly. 

- You can find your CodebaseHQ API credentials in your profile: `https://{PROJECT_NAME}.codebasehq.com/settings/profile`, then setup `config/codebasehq.php`

- You'll need to create a Telegram Bot using [@BotFather](https://telegram.me/BotFather) and setup `config/telegram.php`

- Also, your domain should have a valid SSL certificate for Telegram bot API to work. You can get a free one here: https://letsencrypt.org

2) Setup Telegram bot hook using `telegram_hook_set.php`. You can unset it with `telegram_hook_unset.php`

3) Setup [event hooks on CodebaseHQ](https://support.codebasehq.com/articles/notification-services/http-post-notifications): `https://{PROJECT_NAME}.codebasehq.com/settings/event_hooks`

- All actions, HTTP Post to `https://{BOT_URL}/hook_codebasehq.php`

4) Call `codebasehq_update_projects.php` periodically to update users, projects and assignments.

5) Register in your bot (`/start` it and it'll guide you and your users through the process).

6) Manually assign `codebasehq_user_id` in `telegram_users` table or register using your CodebaseHQ API key via bot dialog.

# Requirements

- PHP7
- SSL for your bot domain
- MySQL or MariaDB

# Installation

Fast & dirty
- configure everything in `config/`
- open `https://{BOT_URL}/install.php` or just run `install.php`

# Vscale notifications
Optionally, you can setup vscale account status notifications in `config/vscale.php` and call `hook_vscale.php` with CRON (see `config_sample/cron.txt`)

# To-do

- [x] Register CodebaseHQ account automatically with API-key
- [x] Installer
- [ ] Add bot to groups
- [ ] Configure which notifications the user would like to receive
