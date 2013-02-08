# Basic usage

Here is a simple example to get the contents of trunk of
a Subversion repository:

``` php
// Example B1
use Webcreate\Vcs\Svn;

$svn = new Svn('svn://someserver/somerepo');
$svn->setCredentials('user', 'userpass');

// Listing contents for trunk
$result = $svn->ls('/');
foreach($result as $fileinfo) {
    $filename    = $fileinfo->getPathname();    // returns "path/to/file"
    $isDirectory = $fileinfo->isDir();          // returns false
    $commitInfo  = $fileinfo->getCommit();      // returns a Commit instance

    $revision    = $commitInfo->getRevision();  // returns 344
    $message     = $commitInfo->getMessage();   // returns "commit messge"
}
```

A similar approach for a Git repository:

``` php
// Example B2
use Webcreate\Vcs\Git;

$git = new Git('https://someserver/somerepo');

// Listing contents for master
$result = $git->ls('/');
foreach($result as $fileinfo) {
    $filename    = $fileinfo->getPathname();   // returns "path/to/file"
    $isDirectory = $fileinfo->isDir();         // returns false
    $commitInfo  = $fileinfo->getCommit();     // returns a Commit instance

    $revision    = $commitInfo->getRevision(); // returns "1a410efbd13591db07496601ebc7a059dd55cfe9"
    $message     = $commitInfo->getMessage();  // returns "commit message"
}
```

&larr; [Intro](00-intro.md) | [The interface](02-the-interface.md) &rarr;