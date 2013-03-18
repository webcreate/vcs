<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Git\Parser;

use Webcreate\Vcs\Common\VcsFileInfo;
use Webcreate\Vcs\Common\Commit;
use Webcreate\Vcs\Git;
use Webcreate\Vcs\Common\AbstractClient;
use Webcreate\Vcs\Common\Parser\ParserInterface;

/**
 * Commandline output parser
 *
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 */
class CliParser implements ParserInterface
{
    /**
     * @var AbstractClient
     */
    protected $client;

    /**
     * Returns client
     *
     * @return AbstractClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * (non-PHPdoc)
     * @see Webcreate\Vcs\Common\Parser.ParserInterface::setClient()
     */
    public function setClient(AbstractClient $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Webcreate\Vcs\Common\Parser.ParserInterface::parse()
     */
    public function parse($command, array $arguments = array(), $output)
    {
        switch ($command) {
            case "log":
                return $this->parseLogOutput($output, $arguments);
                break;
            case "diff":
                return $this->parseDiffOutput($output, $arguments);
                break;
            case "status":
                return $this->parseStatusOutput($output, $arguments);
                break;
        }

        return $output;
    }

    /**
     * Parses the log command output to Commit objects
     *
     * @param  string                                $output
     * @param  array                                 $arguments
     * @return string|\Webcreate\Vcs\Common\Commit[]
     */
    public function parseLogOutput($output, array $arguments = array())
    {
        if (!isset($arguments['--pretty=']) || Git::PRETTY_FORMAT !== $arguments['--pretty=']) {
            // non pretty results are not supported
            return $output;
        }

        $xml = '<log>' . $output . '</log>';

        $sxml = simplexml_load_string($xml);

        $retval = array();
        foreach ($sxml->logentry as $entry) {
            $revision = (string) $entry->commit;
            $date     = (string) $entry->date;
            $author   = (string) $entry->author;
            $message  = (string) $entry->msg;

            $retval[] = new Commit($revision, new \DateTime($date), $author, trim($message));
        }

        return $retval;
    }

    /**
     * Parse the status command output to FileInfo objects
     *
     * @param  string                                  $output
     * @param  array                                   $arguments
     * @throws \Exception
     * @return string|\Webcreate\Vcs\Common\FileInfo[]
     */
    public function parseStatusOutput($output, array $arguments = array())
    {
        if (!isset($arguments['--porcelain']) || false === $arguments['--porcelain']) {
            // non porcelain results are not supported
            return $output;
        }

        if ('' === trim($output)) {
            return array();
        }

        $lines = explode("\n", rtrim($output));

        $retval = array();
        foreach ($lines as $line) {
            if (preg_match('/([A-Z\?\s])([A-Z\?\s])\s(.*)( -> (.*))?/', $line, $matches)) {
                list($fullmatch, $x, $y, $file) = $matches;

                $file = new VcsFileInfo($file, $this->getClient()->getHead());
                $file->setStatus($x);

                $retval[] = $file;
            } else {
                throw new \Exception('Unable to parse line "'. $line . '"');
            }
        }

        return $retval;
    }

    /**
     * Parse the diff command output to FileInfo objects
     *
     * @param  string                                  $output
     * @param  array                                   $arguments
     * @throws \Exception
     * @return string|\Webcreate\Vcs\Common\VcsFileInfo[]
     */
    public function parseDiffOutput($output, array $arguments = array())
    {
        if (!isset($arguments['--name-status']) || false === $arguments['--name-status']) {
            // non name-status results are not supported
            return $output;
        }

        $lines = explode("\n", rtrim($output));

        $retval = array();
        foreach ($lines as $line) {
            if (preg_match('/([ACDMRTUXB])\s+(.*)?/', $line, $matches)) {
                list($fullmatch, $x, $file) = $matches;

                $file = new VcsFileInfo($file, $this->getClient()->getHead());
                $file->setStatus($x);

                $retval[] = $file;
            } else {
                throw new \Exception('Unable to parse line "'. $line . '"');
            }
        }

        return $retval;
    }
}
