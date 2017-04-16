# config valid only for current version of Capistrano
lock "3.8.0"

set :application, "coderdojo-website"
set :repo_url, "git@github.com:CoderDojoNederland/Website.git"

set :linked_files, ["app/config/parameters.yml"]
set :linked_dirs, ["var/logs", "web/articles"]

set :symfony_console_path, "bin/console"
set :symfony_console_flags, "--no-debug"

set :slackistrano, {
  channel: '#website-nl',
  team: 'coderdojonederland',
  token: ENV['SLACK_TOKEN']
}

after 'deploy:publishing', 'coderdojo:migrate'