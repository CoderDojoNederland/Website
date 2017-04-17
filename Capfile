require "capistrano/setup"
require "capistrano/deploy"
require "capistrano/scm/git"
require 'capistrano/setup'
require 'capistrano/deploy'
require 'capistrano/symfony'
require 'slackistrano/capistrano'
require 'capistrano/bower'
install_plugin Capistrano::SCM::Git

Dir.glob('config/capistrano_tasks/*.rake').each { |r| import r }
