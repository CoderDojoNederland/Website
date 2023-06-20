require "capistrano/setup"
require "capistrano/deploy"
require "capistrano/scm/git"
require 'capistrano/setup'
require 'capistrano/deploy'
require 'capistrano/symfony'
require 'capistrano/yarn'
install_plugin Capistrano::SCM::Git

Dir.glob('config/capistrano_tasks/*.rake').each { |r| import r }
