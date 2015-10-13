# CoderDojo Nederland Website

http://www.coderdojo.nl

Welcome in the CoderDojo Nederland Website repository. The website is completely open source which means anybody can contribute to it. We welcome everybody to do so and send us a pull request to bring it into master and have it deployed. In order to do so, here are the basics you need to know.

# Local Development

## Tools

In order to develop on the website locally you need a couple of things:

- php 5.6 (minimum)
- MySQL
- Apache

The tools you need are:

- [Composer](http://getcomposer.org)
- [Node.JS](http://nodejs.org) / [NPM](http://npmjs.com)
  - `npm install less`
- Terminal / Command Line

As you might have noticed, we run on Symfony (2.7 at the moment).

Please make sure to refer to the [Symfony Documenteation](http://symfony.com) for basic knowledge of the system.

## Installation

First you fork the repo and clone it to your computer. Once you have it enter de root directory on your terminal. The first step is to install all dependencies by running composer.

```
composer install
```

At the end of the process this will ask you to also define some parameters. Fill in thedatabase host, port, name, user and password according to you local database environment. The email settings can be left default (unless you need to test emails) and for the secret just type a random string.

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

Now finally make sure your localhost uses the `web` directory as root and open the site in your browser:

```
http://localhost/app_dev.php
```

*Note how you open `/app_dev.php`, this adds extra debug features*

## How to Contribute

Some ground rules

1. Keep all commits, comments, descriptions, etc. in English
2. Fork the Repo and send a pull request
3. Always add a description in your pull request
4. Use the labels (`Work In Progress`, `Ready For Review`, `Ready For Merge`, `Needs Work`)
5. Make sure you right according unit tests to your code
6. We stick with [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) as much as possible

Once your PR has been reviewed and approved it will be merged and deployed to production.

# Contact

For questions feel free to contact **[@ChristianVermeulen](http://github.com/christianvermeulen)** or **[@tmw](http://github.com/tmw)**.
