# config valid only for current version of Capistrano
lock "3.8.0"

set :application, "coderdojo-website"
set :repo_url, "git@github.com:CoderDojoNederland/Website.git"

set :linked_files, ["app/config/parameters.yml"]
set :linked_dirs, ["var/logs"]

set :symfony_console_path, "bin/console"
set :symfony_console_flags, "--no-debug"

set :slackistrano, {
  channel: '#website-nl',
  webhook: ENV['SLACK_WEBHOOK']
}

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }
