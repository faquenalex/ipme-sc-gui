# STEAM CACHE 

## Table of Contents

* [Table of contents](#table-of-contents)
* [Introduction](#introduction)
* [Installation](#installation)
* [How to](#how-to)
* [Tests](#tests)
    - [Heroku](#heroku)
    - [Travis](#travis)
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

### Travis

### Heroku

Heroku is a platform as a service (PaaS) that enables developers to build, run, and operate applications entirely in the cloud. It allows you to test your app before sending it in prod. You can have access to logs and a lot of other services.

Here a quick tutorial to be able to use Heroku :

 1 - Sign up on https://www.heroku.com/ .
 2 - 

#### Procfile

## Thanks

- Based on configs from [Steam Cache](https://github.com/steamcache/).
- Everyone that helped us on the project.


## Author
