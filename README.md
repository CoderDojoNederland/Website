# CoderDojo Nederland Website

[![Build Status](https://travis-ci.org/CoderDojoNederland/website.svg?branch=master)](https://travis-ci.org/CoderDojoNederland/website)
[![Coverage Status](https://coveralls.io/repos/CoderDojoNederland/website/badge.svg?branch=master&service=github)](https://coveralls.io/github/CoderDojoNederland/website?branch=master)

http://www.coderdojo.nl

Welcome in the CoderDojo Nederland Website repository. The website is completely open source which means anybody can contribute to it. We welcome everybody to do so and send us a pull request to bring it into master and have it deployed. In order to do so, here are the basics you need to know.

# Table of Contents

- [What to do?](#what-to-do)
- [How to contribute](#how-to-contribute)
- [Local Development](#local-development)
  - [Easy](#easy): Run a virtual box with Vagrant (1 command)
  - [Advanced](#advanced): Set up your own (L/W/X/M)AMP environment
- [Contact](#contact)

# What to do?

We keep track of stuff to do in the [GitHub Issue](https://github.com/CoderDojoNederland/website/issues) list. Here you can mainly see 2 different categories; Bugs & Ideas. Both can either be reported or accepted. The accepted ones are ready to be picked up, the reported ones are ready to be discussed and then accepted / rejected.

# How to Contribute

Some ground rules

1. Keep all commits, comments, descriptions, etc. in English
2. Fork the Repo and send a pull request
3. Always add a description in your pull request
4. Use the labels (`Work In Progress`, `Ready For Review`, `Ready For Merge`, `Needs Work`)
5. Make sure you write according unit tests to your code
6. We stick with [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) as much as possible

Once your PR has been reviewed and approved it will be merged and deployed to production.

# Local Development

There are 2 options to work on this project locally:

- **[Easy](#easy):** Run a virtual box with Vagrant (1 command)
- **[Advanced](#advanced):** Set up your own (L/W/X/M)AMP environment

## Easy

The easy way is to use the Vagrant setup we have provided. This is a predefined virtual ubuntu machine which will run in de background and has everything you need. You just need to follow these 4 easy steps:

1. Install [Virtual Box](https://www.virtualbox.org/wiki/Downloads)
2. Install [Vagrant](https://www.vagrantup.com/downloads.html)*
3. Add `127.0.0.1 coderdojo.nl.dev` to your hosts file ([see how](http://support.hostgator.com/articles/general-help/technical/how-do-i-change-my-hosts-file))
4. From the root of the project directory run `vagrant up` (the first time can take a while, grab a coffee)
5. Open your browser and go to http://coderdojo.nl.dev:8080/app_dev.php

That's it! You should now be looking at de dev version of the website.

From here on you can just work on the the project, all files are automatically synced between your local filesystem and the virtual box. If you need to run commands on the vagrant box you can easily log in with the `vagrant ssh` command.

*) Please be aware that the vagrant box uses 1GB ram when running. You can easily stop it when you are done with `vagrant suspend`.

## Advanced

### Tools

In order to develop on the website locally you need to have a couple of things up and running:

- PHP 5.6 (minimum)
- MySQL
- Apache

Next you will need to install a few dependencies for this project:

1. Install [Composer](http://getcomposer.org)
2. Install [Node.JS](http://nodejs.org) / [NPM](http://npmjs.com)
3. Install Less: `npm install less`

As you might have noticed, we run on Symfony (2.7 at the moment).

Please make sure to refer to the [Symfony Documentation](http://symfony.com) for basic knowledge of the system.

First you fork the repo and clone it to your computer. Once you have it enter de root directory on your terminal. The first step is to install all dependencies by running composer.

```
composer install
```

At the end of the process this will ask you to also define some parameters. Fill in the database host, port, name, user and password according to you local database environment. The email settings can be left default (unless you need to test emails) and for the secret just type a random string.

Now make sure the following directories are writable by apache:

```
- app/cache
- app/logs
```

Now you can fill the database with dummy data to work with:

```
php app/console doctrine:fixtures:load
```

And you install assets:

```
php app/console cache:clear
php app/console assetic:dump
php app/console assets:install --symlink
```

Now finally make sure apache uses the `web` directory as root and open the site in your browser:

```
http://localhost/app_dev.php
```

*Note how you open `/app_dev.php`, this adds extra debug features*

# Contact

For questions feel free to contact **[@ChristianVermeulen](http://github.com/christianvermeulen)** or **[@tmw](http://github.com/tmw)**.
