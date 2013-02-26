# Git

This library contains an implementation of the `VcsInterface` for
working with Git repositories.

The implemenation works around the commandline client of git.

Additional methods besides the ones from the [VcsInterface](02-the-interface.md):

* push (experimental)
* pull (experimental)

## Initializing a new instance

``` php
// Example G1
use Webcreate\Vcs\Git;

$git = new Git('git@github.com:acme/example.git');
```

If your git exacutable isn't located in `/usr/bin` you can specify
this via the adapter:

``` php
// Example G2
use Webcreate\Vcs\Git;

$git = new Git('git@github.com:acme/example.git');
$git->getAdapter()->setExecutable('/usr/local/bin/git');
```

&larr; [Subversion](03-subversion.md)