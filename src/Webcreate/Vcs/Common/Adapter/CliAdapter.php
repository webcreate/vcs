<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Common\Adapter;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Webcreate\Vcs\Common\AbstractClient;
use Webcreate\Vcs\Common\Parser\ParserInterface;
use Webcreate\Util\Cli;
use Webcreate\Vcs\Common\Adapter\AdapterInterface;

/**
 * Commandline interface adapter
 *
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 */
class CliAdapter implements AdapterInterface
{
    /**
     * Path to binary
     *
     * @var string
     */
    protected $bin;

    /**
     * Commandline interface utility
     *
     * @var Webcreate\Util\Cli
     */
    protected $cli;

    /**
     * VCS client
     *
     * @var Webcreate\Vcs\Common\AbstractClient
     */
    protected $client;

    /**
     * Output parser
     *
     * @var Webcreate\Vcs\Common\Parser\ParserInterface
     */
    protected $parser;

    /**
     * Global arguments for binary
     *
     * @var array
     */
    protected $globalArguments = array();

    /**
     * Constructor.
     *
     * @param string          $bin    path to the clients' binary
     * @param Cli             $cli
     * @param ParserInterface $parser output parser
     */
    public function __construct($bin, Cli $cli, ParserInterface $parser = null)
    {
        $this->setExecutable($bin);
        $this->cli = $cli;
        $this->setParser($parser);
    }

    /**
     * Returns VCS client
     *
     * @return Webcreate\Vcs\Common\AbstractClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * (non-PHPdoc)
     * @see Webcreate\Vcs\Common\Adapter.AdapterInterface::setClient()
     */
    public function setClient(AbstractClient $client)
    {
        $this->client = $client;

        $this->parser->setClient($this->client);

        return $this;
    }

    /**
     * Returns parser
     *
     * @return ParserInterface
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Set parser
     *
     * @param  ParserInterface                          $parser
     * @return \Webcreate\Vcs\Common\Adapter\CliAdapter
     */
    public function setParser(ParserInterface $parser)
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * Set global arguments for binary
     *
     * @param  array                                    $arguments
     * @return \Webcreate\Vcs\Common\Adapter\CliAdapter
     */
    public function setGlobalArguments(array $arguments)
    {
        $this->globalArguments = $arguments;

        return $this;
    }

    /**
     * Get the executable path
     *
     * @return string
     */
    public function getExecutable()
    {
        return $this->bin;
    }

    /**
     * Set the executable path
     *
     * @param  string                                   $bin
     * @return \Webcreate\Vcs\Common\Adapter\CliAdapter
     */
    public function setExecutable($bin)
    {
        $this->bin = $bin;

        return $this;
    }

    /**
     * Return global arguments for binary
     *
     * @return array
     */
    public function getGlobalArguments()
    {
        return $this->globalArguments;
    }

    /**
     * (non-PHPdoc)
     * @see Webcreate\Vcs\Common\Adapter.AdapterInterface::execute()
     *
     * @return mixed
     */
    public function execute($command, array $arguments = array(), $cwd = null)
    {
        $bincommand  = $this->bin . ' ' . $command;
        $arguments  += $this->getGlobalArguments();
        $commandline = $this->cli->prepare($bincommand, $arguments);

        if ($this->cli->execute($commandline, null, $cwd) <> 0) {
            throw new ProcessFailedException($this->cli->getProcess());
        } elseif ($message = $this->cli->getErrorOutput()) {
            throw new \RuntimeException($message);
        }

        return $this->parse($command, $arguments, $this->cli->getOutput());
    }

    /**
     * Delegate output to output parser when a parser is set,
     * otherwise returns the raw output
     *
     * @param  string $command
     * @param  array  $arguments
     * @param  string $output
     * @return mixed
     */
    protected function parse($command, array $arguments = array(), $output)
    {
        if (null !== $this->parser) {
            return $this->parser->parse($command, $arguments, $output);
        }

        return $output;
    }
}
