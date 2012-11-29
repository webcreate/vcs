# PHP interface for version control systems

Webcreate/Vcs is an interface for PHP for working with various
version control system, like SVN, GIT, etc.

[![Build Status](https://secure.travis-ci.org/webcreate/vcs.png?branch=master)](https://travis-ci.org/webcreate/vcs)

Installation / Usage
--------------------

1. Download the [`composer.phar`](https://getcomposer.org/composer.phar)
executable or use the installer.

    ``` sh
    $ curl -s https://getcomposer.org/installer | php
    ```

2. Create a composer.json defining your dependencies.

    ``` json
    {
        "require": {
            "webcreate/vcs": "dev-master"
        }
    }
    ```

3. Run Composer: `php composer.phar install`

Documenation
------------

Full documentation is available in [docs/](docs/).