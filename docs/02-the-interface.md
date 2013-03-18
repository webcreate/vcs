# The interface

Webcreate\Vcs is build around a single interface, the `VcsInterface`.
This interface contains methods to work with a version control system.

Defined methods:

* cat
* ls
* branches
* tags
* log
* changelog
* diff
* import
* export
* status
* checkout
* add
* commit
* revisionCompare
* getHead
* setHead

The methods are a mix between the commands for subversion and git. They should
look quite familiar to you when you've worked with both systems before.

&larr; [Intro](01-basic-usage.md) | [Subversion](03-subversion.md) &rarr;
