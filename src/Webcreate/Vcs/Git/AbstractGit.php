<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Git;

use Webcreate\Vcs\Common\Reference;
use Webcreate\Util\Cli;
use Webcreate\Vcs\Git\Parser\CliParser;
use Webcreate\Vcs\Common\Adapter\CliAdapter;
use Webcreate\Vcs\Common\Adapter\AdapterInterface;
use Webcreate\Vcs\Common\AbstractClient;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Abstract base class for Git class.
 *
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 */
abstract class AbstractGit extends AbstractClient
{
    /**
     * Repository url
     *
     * @var string
     */
    protected $url;

    /**
     * Current working directory
     *
     * @var string
     */
    protected $cwd;

    /**
     * @var bool
     */
    protected $hasCheckout = false;

    /**
     * @var bool
     */
    protected $hasClone = false;

    /**
     * @var bool
     */
    protected $isTemporary = false;

    /**
     * Constructor.
     *
     * @param string           $url     Url of the repository
     * @param AdapterInterface $adapter adapter
     * @param string|null      $cwd     current working directory
     */
    public function __construct($url, AdapterInterface $adapter = null, $cwd = null)
    {
        if (null === $adapter) {
            $cli = new Cli();
            $cli->setTimeout(600);
            $adapter = new CliAdapter('/usr/bin/git', $cli, new CliParser());
        }

        parent::__construct($url, $adapter);

        $this->setCwd($cwd);
        $this->setHead(new Reference('master'));
    }

    /**
     * Sets the current working directory
     *
     * @param  string                         $cwd
     * @return \Webcreate\Vcs\Git\AbstractGit
     */
    public function setCwd($cwd)
    {
        $this->hasCheckout = false;
        $this->hasClone = false;
        $this->isTemporary = false;

        if (is_null($cwd)) {
            $this->cwd = sys_get_temp_dir() . '/' . uniqid('git');
            $this->isTemporary = true;
        } else {
            if (is_dir($cwd)) {
                if (is_dir($cwd . '/.git')) {
                    $this->hasClone = true;
                }
            }
            $this->cwd = $cwd;
        }

        return $this;
    }

    public function setHead($reference)
    {
        parent::setHead($reference);

        // branch might have changed, so if we had a checkout it could be out of sync
        // setting this to false will get it back in sync
        $this->hasCheckout = false;
    }

    /**
     * Execute GIT command
     *
     * @param  string $command
     * @param  array $arguments
     * @param string|null $cwd
     * @return string
     */
    protected function execute($command, array $arguments = array(), $cwd = null)
    {
        if (null === $cwd) {
            $cwd = $this->cwd;
        }

        return $this->adapter->execute($command, $arguments, $cwd);
    }

    /**
     * Removes temprorary files
     */
    public function __destruct()
    {
        if ($this->isTemporary) {
            $filesystem = new Filesystem();
            $filesystem->remove($this->cwd);
        }
    }
}
