<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Svn;

use Webcreate\Vcs\Common\VcsFileInfo;
use Webcreate\Vcs\Common\Reference;
use Webcreate\Vcs\Svn\Parser\CliParser;
use Webcreate\Util\Cli;
use Webcreate\Vcs\Common\Adapter\CliAdapter;
use Webcreate\Vcs\Exception\NotFoundException;
use Webcreate\Vcs\Common\AbstractClient;
use Webcreate\Vcs\Common\Adapter\AdapterInterface;

/**
 * Abstract base class for Svn class.
 *
 * Provides basic low-level functions for working with Subversion. This
 * way the high-level api class stays clean.
 *
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 */
abstract class AbstractSvn extends AbstractClient
{
    /**
     * Username
     *
     * @var string
     */
    protected $username;

    /**
     * Password
     *
     * @var string
     */
    protected $password;

    /**
     * Output callback
     *
     * @var \Closure
     */
    protected $output;

    /**
     * Current working directory
     *
     * @var string
     */
    protected $cwd;

    /**
     * Basepaths mapping
     *
     * @var array
     */
    protected $basePaths = array(
            'trunk'    => 'trunk',
            'tags'     => 'tags',
            'branches' => 'branches',
    );

    /**
     * Constructor.
     *
     * @param string           $url     Url of the repository
     * @param AdapterInterface $adapter adapter
     */
    public function __construct($url, AdapterInterface $adapter = null)
    {
        if (null === $adapter) {
            $adapter = new CliAdapter('/usr/bin/svn', new Cli(), new CliParser());
        }

        parent::__construct($url, $adapter);

        $this->setHead(new Reference('trunk'));
    }

    /**
     * Set username and password
     *
     * @param  string                         $username
     * @param  string                         $password
     * @return \Webcreate\Vcs\Svn\AbstractSvn
     */
    public function setCredentials($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    /**
     * Returns username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set output callback
     *
     * @param  \Closure                       $output
     * @return \Webcreate\Vcs\Svn\AbstractSvn
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Global arguments for executing SVN commands
     *
     * @return array
     */
    protected function getGlobalArguments()
    {
        $args = array(
                '--non-interactive' => true,
        );

        if ($this->username) $args['--username'] = $this->username;
        if ($this->password) $args['--password'] = $this->password;
        return $args;
    }

    /**
     * Execute SVN command
     *
     * @param  string $command
     * @param  array $arguments
     * @param  string $cwd
     * @throws \Webcreate\Vcs\Exception\NotFoundException
     * @throws \Exception
     * @return string
     */
    public function execute($command, array $arguments = array(), $cwd = null)
    {
        $arguments += $this->getGlobalArguments();

        try {
            $result = $this->adapter->execute($command, $arguments, $cwd);
        } catch (\Exception $e) {
            // @todo move to a generic error handler? Something similar to the ParserInterface
            if (preg_match('/svn: URL \'[^\']+\' non-existent in that revision/', $e->getMessage())) {
                throw new NotFoundException($e->getMessage());
            }
            if (preg_match('/svn: File not found/', $e->getMessage())) {
                throw new NotFoundException($e->getMessage());
            }

            throw $e;
        }

        return $result;
    }

    /**
     * Returns url for a specific path
     *
     * @param  string|VcsFileInfo $path
     * @return string
     */
    public function getSvnUrl($path)
    {
        $head = $this->head;

        if ($path instanceof Reference) {
            $head = $path;
            $path = '/';
        }

        if (null === $path) {
            $path = '/';
        }

        if (is_string($path)) {
            $path = new VcsFileInfo($path, array($head->getName(), $head->getType()));
        }

        if ($path->inBranch()) {
            $branchName = $path->getReferenceName();

            if ('trunk' === $branchName) {
                $basePath = $this->basePaths['trunk'];
            } else {
                $basePath = $this->basePaths['branches'] . '/' . $branchName;
            }
        } elseif ($path->inTag()) {
            $tagName = $path->getReferenceName();

            $basePath = $this->basePaths['tags'] . '/' . $tagName;
        }

        $retval = $this->url;
        if (isset($basePath)) {
            $retval.= '/' . $basePath;
        }

        $retval.= '/' . ltrim($path->getPathname(), '/');
        $retval = rtrim($retval, '/');

        return $retval;
    }

    /**
     * Sort function for sorting entries
     *
     * @param  array  $item1
     * @param  array  $item2
     * @return number
     */
    protected function cmpSvnEntriesByKind(array $item1, array $item2)
    {
        $item1['name'] = strtolower($item1['name']);
        $item2['name'] = strtolower($item2['name']);

        return ($item1['kind'] == 'dir'
                ? ($item2['kind'] == 'dir' ? strnatcmp($item1['name'], $item2['name']) : -1)
                : ($item2['kind'] == 'dir' ? 1 : strnatcmp($item1['name'], $item2['name'])
                ));
    }
}
