# config valid only for current version of Capistrano
lock "3.11.0"

set :application, "coderdojo-website"
set :repo_url, "git@github.com:CoderDojoNederland/Website.git"

set :linked_files, ["app/config/parameters.yml"]
set :linked_dirs, ["var/logs", "web/articles", "web/club-100-avatars", "web/media/cache"]

set :symfony_console_path, "bin/console"
set :symfony_console_flags, "--no-debug"

set :slackistrano, {
  channel: '#website-nl',
  webhook: ENV['SLACK_WEBHOOK']
}

after 'deploy:publishing', 'coderdojo:migrate'
after 'deploy:publishing', 'coderdojo:bower_symlink'
