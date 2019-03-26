# STEAM CACHE 

## Table of Contents

* [Table of contents](#table-of-contents)
* [Introduction](#introduction)
* [Installation](#installation)
* [How to](#how-to)
* [Tests](#tests)
    - [Travis](#travis)
        - [Get Started](#get-started)
    - [Heroku](#heroku)
        - [Procfile](#procfile)
        - [Config Vars](#config-vars)
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
* [Heroku](https://devcenter.heroku.com/categories/reference) - Platform as a Service (PaaS).
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

#### Get Started

Let's get started with Travis CI :

1- Go to [Travis](https://travis-ci.com/) website and Sign up with GitHub. \
2- Accept the Authorization of Travis CI on your GitHub account. \
3- Activa to allow Travis, and select the repositories you want to use with Travis CI. \
4- Finally, add a *.travis.yml* file and set it up like you want.

Here an example for our application :

```yml
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

 1- Sign up on https://www.heroku.com/ \
 2- Create a new app. \
 3- Go to Deploy and add a deployment method like GitHub, that's what we did. \
 4- Connect to GitHub to enable code diffs and deploys. \
 5- After that, go to Deploy and set it up if you want your deployment to be automatic or not by branch. \
 6- Finally, go to settings and add buildpacks that correspond with the language you use.

A brief description of the differents tabs :

- Overview : Information global of your app.
- Resources : Show the Dynos' (container) type your app is using and the add-ons it uses.
- Deploy : Setting on how your app is deployed.
- Metrics : Data about your app.
- Activity : Show all the app's deployments and builds with logs.
- Access : If you want collaborator to have access of your app.
- Settings : Setting of your app.

Now that you have done all of this. You have go configure a procfile 

#### Procfile

Heroku apps include a Procfile that specifies the commands that are executed by the app on startup.
You can use Procfile to declare a variety of process types like your app's web server. So you can test your application on the web.
Procfile is always a simple text file that is named *procfile* without an extension. It must be in your app's root directory.

To see more about how to configure your procfile, follow the [official](https://devcenter.heroku.com/articles/procfile) doc.

Here an example from our app :

```
web: $(composer config bin-dir)/heroku-php-apache2 public
```

We declare a web server process for our app that will point on the repertory public and its files.
When it's done, we are able to see our app when we click on *Open App* at the right top of our dashboard.

In the *settings* tab, we can add environment variables.

#### Config Vars

A single app always runs a lot of environments.
Using the Heroku Dashboard, you can edit config vars from your app's Settings tab :
![alt text](https://image.prntscr.com/image/CN9Fa7taRMiVwx9G4DrZRQ.png)

It is easier and let you see all the environments you are using.

You will need at least four vars :

- MYSQL_DATABASE - name of the DB.
- MYSQL_USER -  username.
- MYSQL_PASSWORD - password.
- MYSQL_URL - url to connect to the DB.


## Thanks

- Based on configs from [Steam Cache](https://github.com/steamcache/).
- Everyone that helped us on the project.


## Author
