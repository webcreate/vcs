<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs;

/**
 * Common interface for version control clients.
 *
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 */
interface VcsInterface
{
    /**
     * Set the branch or tag you are currently working on
     *
     * @param array|object $reference
     */
    public function setHead($reference);

    /**
     * Returns the current branch or tag you are working on
     *
     * @return object $reference
     */
    public function getHead();

    /**
     * Return branches
     *
     * @return array list of branch names
     */
    public function branches();

    /**
     * Return tags
     *
     * @return array list of tag names
     */
    public function tags();

    /**
     * Checkout from repository
     *
     * @param string $dest destination path
     */
    public function checkout($dest);

    /**
     * Add to version control
     *
     * @param string $path path to add
     */
    public function add($path);

    /**
     * Commit modified and added files
     *
     * @param string $message commit message
     */
    public function commit($message);

    /**
     * Returns the status of the working copy
     *
     * @param string|null $path optional: a specific path to return the status for
     */
    public function status($path = null);

    /**
     * Imports a directory into the vcs
     *
     * @param string $src     path that needs to be imported to the vcs
     * @param string $path    destination path in the vcs
     * @param string $message commit message
     */
    public function import($src, $path, $message);

    /**
     * Export from VCS
     *
     * @param string $path path in the vcs
     * @param string $dest destination folder
     */
    public function export($path, $dest);

    /**
     * Lists files and directories
     *
     * @param  string $path path in the vcs
     * @return array
     */
    public function ls($path);

    /**
     * Returns a list of commits for a specific path
     *
     * @see \Webcreate\Vcs\Common\Commit
     *
     * @param  string                         $path path in the vcs
     * @return \Webcreate\Vcs\Common\Commit[]
     */
    public function log($path);

    /**
     * Returns a list of commits between two revisions
     *
     * @param string $revision1 the oldest revision
     * @param string $revision2 the newest revision
     * @return
     */
    public function changelog($revision1, $revision2);

    /**
     * Retrieve contents for a file
     *
     * @param  string $path path in the vcs
     * @return string
     */
    public function cat($path);

    /**
     * Return a diff
     *
     * @param string $oldPath
     * @param string $newPath
     * @param string $oldRevision
     * @param string $newRevision
     * @param bool   $summary     when true only the filenames are listed,
     *                            otherwise the diff of the content is included
     * @return \Webcreate\Vcs\Common\VcsFileInfo[]
     */
    public function diff($oldPath, $newPath, $oldRevision = 'HEAD', $newRevision = 'HEAD', $summary = true);

    /**
     * Compares two revisions
     *
     * @param  string $revision1
     * @param  string $revision2
     * @return int    returns 1 when revision1 is greater then revision1,
     *                     -1 when revision1 is smaller then revision2 and
     *                     0 when they are equal
     */
    public function revisionCompare($revision1, $revision2);
}
