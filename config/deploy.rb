# config valid only for current version of Capistrano
lock "3.17.3"

set :application, "coderdojo-website"
set :repo_url, "git@github.com:CoderDojoNederland/Website.git"

set :linked_files, ["app/config/parameters.yml"]
set :linked_dirs, ["var/logs", "web/articles", "web/club-100-avatars", "web/media/cache"]

set :symfony_console_path, "bin/console"
set :symfony_console_flags, "--no-debug"

after 'deploy:publishing', 'coderdojo:migrate'
after 'deploy:publishing', 'coderdojo:bower_symlink'
