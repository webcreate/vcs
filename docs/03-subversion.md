# Subversion

This library contains an implementation of the `VcsInterface` for
working with Subversion repositories.

The implemenation works around the commandline client of subversion.

## Initializing a new instance

``` php
// Example S1
use Webcreate\Vcs\Svn;

$svn = new Svn('svn://someserver/somerepo');
$svn->setCredentials('user', 'userpass');
```

If your subversion exacutable isn't located in `/usr/bin` you can specify
this via the adapter:

``` php
// Example S2
use Webcreate\Vcs\Svn;

$svn = new Svn('svn://someserver/somerepo');
$svn->setCredentials('user', 'userpass');
$svn->getAdapter()->setExecutable('/usr/local/bin/svn');
```

&larr; [The interface](02-the-interface.md) | [Git](04-git.md) &rarr;