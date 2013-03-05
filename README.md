# PHP interface for version control systems

Webcreate\Vcs is an interface for PHP for working with various
version control systems, like SVN, GIT, etc.

This library was created as part of [Conveyor](http://conveyordeploy.com).

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

Getting started
---------------

Webcreate\Vcs is build around a single interface, the `VcsInterface`.
This interface contains methods to work with a version control system.

This libary currently contains two implementations of the interface:
`Svn` and `Git`.

Lets say you want to get the latest commits from git. Here is an example:

``` php
// Example R1
use Webcreate\Vcs\Git;

$git = new Git('https://someserver/somerepo.git');

// Retrieve the 20 latest commits for master
$result = $git->log('.', null, 20);
foreach($result as $commit) {
    $date        = $commit->getDate();      // returns \DateTime instance
    $author      = $commit->getAuthor();    // returns "John Doe"
    $revision    = $commit->getRevision();  // returns "1a410efbd13591db07496601ebc7a059dd55cfe9"
    $message     = $commit->getMessage();   // returns "commit message"
}
```

Full documentation is available in [docs/](https://github.com/webcreate/vcs/tree/master/docs).