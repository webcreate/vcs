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
     * Return branches
     */
    public function branches();

    /**
     * Return tags
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
     * Retuns the status of the working copy
     */
    public function status($path);

    /**
     * Imports a directory into the vcs
     *
     * @param string $src     path that needs to be imported to the vcs
     * @param string $path    destionation path in the vcs
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
     * List files and directories
     *
     * @param string $path path in the vcs
     * @return array
     */
    public function ls($path);

    /**
     * Retrieve commit log
     *
     * @param string $path path in the vcs
     * @return array
     */
    public function log($path);

    /**
     * Retrieve contents for a file
     *
     * @param string $path path in the vcs
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
     * @param bool   $summary
     */
    public function diff($oldPath, $newPath, $oldRevision = 'HEAD', $newRevision = 'HEAD', $summary = true);
}