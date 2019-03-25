# STEAM CACHE 

## Table of Contents

* [Table of contents](#table-of-contents)
* [Introduction](#introduction)
* [Installation](#installation)
* [How to](#how-to)
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

## Thanks

- Based on configs from [Steam Cache](https://github.com/steamcache/).
- Everyone that helped us on the project.


## Author
