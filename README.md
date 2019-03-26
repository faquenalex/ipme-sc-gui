# STEAM CACHE 

## Table of Contents

* [Table of contents](#table-of-contents)
* [Introduction](#introduction)
* [Installation](#installation)
* [How to](#how-to)
* [Tests](#tests)
    - [Travis](#travis)
    - [Heroku](#heroku)
* [Thanks](#thanks)
* [Author](#author)

## Introduction

You're planning to invite friends at your house to play video games but your internet sucks, Steam Cache is your solution !
It provides a caching proxy server for game download content.

What does it mean ?
It means that when you will download your game for the time with your internet, it will cache its data on a local server cache. After this, your friends, that want to have this game too, will download it locally. This will drastically reduce internet bandwith consumption, and will allow you to gain a lot of time !

## Technologies

* [Docker](https://hub.docker.com/r/steamcache/monolithic) - The containers software we used.
* [Symfony](https://symfony.com/) - PHP Framework.
* [SteamCMD](https://developer.valvesoftware.com/wiki/SteamCMD) - Command-line version of the Steam client.
* [Heroku](https://devcenter.heroku.com/categories/reference) - Platform as a Service (Paas).
* [Travis](https://docs.travis-ci.com/) - CI service.


## Installation

### Docker

Update the *apt* package index.

```bash
$ sudo apt-get update
```
Install the latest version of *Docker CE* and *containerd*.

```bash
$ sudo apt-get install docker-ce docker-ce-cli containerd.io
```

Clone the repository :

```bash
$ sudo git clone https://github.com/faquenalex/ipme-sc-gui.git
```

Then you have to build the dockerfile download to install all the dependencies needed :

```bash
$ sudo docker build name_file
```

## How to

Set the default settings to be used when downloading your games.

```bash
$ nano .env
```
 You can check the file .env.test to help you set it up.

## Tests

Before going to the specifications, we will explain to you what is CI.

CI means Continuous Integration. It is a development practice where developers integrate code into a repository frequently per day. The goal is to build healthier software by developping and testing smaller part of your integration. Each one can then be verified by an automatic build and automated test. It is where Travis CI comes in.


### Travis

When you run a build, Travis CI clones your GitHub repository into a new virtual environment, and then build and test your code. If there are errors the build is considered broker, if not it is considered passed and Travis can deploi your code to a web server, or application host. You can check more about it on [here](https://docs.travis-ci.com/user/for-beginners/).

Travis CI is configured by adding a file .travis.yml to the root directory of the repository. This file specifies the programming  language used in the application, the desired building and testing environment, and various other parameters. You can find more on [Travis Official Doc](https://docs.travis-ci.com/user/customizing-the-build)).


Let's get started with Travis CI :

1- Go to [Travis](https://travis-ci.com/) website and Sign up with GitHub. \
2- Accept the Authorization of Travis CI on your GitHub account. \
3- Activa to allow Travis, and select the repositories you want to use with Travis CI. \
4- Finally, add a *.travis.yml* file and set it up like you want.

Here an example for our application :

```bash
language: php

php:
  - 7.2

services:
  - docker
  - docker-compose
  - mariadb

install:
  - composer install
  - php bin/console doctrine:database:create --env=travis
  - php bin/console doctrine:schema:create --env=travis
  - php bin/console doctrine:fixtures:load -n --env=travis

script:
  - php bin/phpunit -c phpunit.travis.xml.dist
  - docker ps -a
```
What does the previous example do ?
- It specifies a PHP project that should be built in PHP 7.2.
- It uses 3 different services : docker, docker-compose and mariadb.
- It installs dependencies with composer, and create a database, a schema with fixtures by using environnement variables.
- Then it uses a config file to do some unit testings, and do some command.



However Travis CI isn’t just for running tests, there are many others things you can do with your code like running your apps on Heroku.

### Heroku

Heroku is a platform as a service (PaaS) that enables developers to build, run, and operate applications entirely in the cloud. It allows you to test your app before sending it in prod. You can have access to logs and a lot of other services.

Here a quick tutorial to be able to use Heroku :

 1 - Sign up on https://www.heroku.com/ \
 2 - 

#### Procfile

## Thanks

- Based on configs from [Steam Cache](https://github.com/steamcache/).
- Everyone that helped us on the project.


## Author
