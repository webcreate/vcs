<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Svn\Parser;

use Webcreate\Vcs\Common\Status;
use Webcreate\Vcs\Common\AbstractClient;
use Webcreate\Vcs\Svn;
use Webcreate\Vcs\Common\Commit;
use Webcreate\Vcs\Common\FileInfo;
use Webcreate\Vcs\Common\Parser\ParserInterface;

/**
 * Commandline output parser for Svn
 *
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 */
class CliParser implements ParserInterface
{
    /**
     * @var \Webcreate\Vcs\Svn
     */
    protected $client;

    /**
     * Returns client
     *
     * @return \Webcreate\Vcs\Svn
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
        if (false === $client instanceof Svn) {
            throw new \InvalidArgumentException(sprintf('Expected argument $client to be Webcreate\Vcs\Svn instead %s given.', get_class($client)));
        }

        $this->client = $client;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Webcreate\Vcs\Common\Parser.ParserInterface::parse()
     */
    public function parse($command, array $arguments = array(), $output)
    {
        switch($command) {
            case "status":
                return $this->parseStatusOutput($output);
                break;
            case "list":
                return $this->parseListOutput($output, $arguments);
                break;
            case "log":
                return $this->parseLogOutput($output, $arguments);
                break;
            case "diff":
                return $this->parseDiffOutput($output, $arguments);
                break;
        }

        return $output;
    }

    /**
     * Parse the status command output
     *
     * @param string $output
     * @throws \Exception
     * @return \Webcreate\Vcs\Common\FileInfo[]
     */
    public function parseStatusOutput($output)
    {
        $lines = explode("\n", rtrim($output));

        $retval = array();
        foreach($lines as $line) {
            if (preg_match('/([A-Z\?\s])([A-Z\?\s])([A-Z\?\s])([A-Z\?\s])([A-Z\?\s])([A-Z\?\s])([A-Z\?\s])\s(.*)/', $line, $matches)) {
                list($fullmatch, $x, , , , , , , $file) = $matches;

                $file = new FileInfo($file, FileInfo::FILE, null, $x);

                $retval[] = $file;
            }
            else {
                throw new \Exception('Unable to parse line "'. $line . '"');
            }
        }

        return $retval;
    }

    /**
     * Parse the Xml result from Subversion
     *
     * @param string $output
     * @param array  $arguments
     * @return array
     */
    public function parseListOutput($output, array $arguments = array())
    {
        if (!isset($arguments['--xml']) || false === $arguments['--xml']) {
            // non xml results are not supported
            return $output;
        }

        $sxml = simplexml_load_string($output);

        $retval = array();
        foreach($sxml->xpath('//entry') as $item) {
            $file = new FileInfo(
                    (string) $item->name,
                    (string) $item->attributes()->kind,
                    new Commit(
                            (string) $item->commit->attributes()->revision,
                            new \DateTime((string) $item->commit->date),
                            (string) $item->commit->author
                    )
            );

            $retval[] = $file;
        }

        return $retval;
    }

    /**
     * Parse the Xml result from Subversion
     *
     * @param string $output
     * @param array  $arguments
     * @return array
     */
    protected function parseLogOutput($output, array $arguments = array())
    {
        if (!isset($arguments['--xml']) || false === $arguments['--xml']) {
            // non xml results are not supported
            return $output;
        }

        $sxml = simplexml_load_string($output);

        $retval = array();
        foreach($sxml->logentry as $entry) {
            $retval[] = new Commit(
                    (string) $entry->attributes()->revision,
                    new \DateTime((string)  $entry->date),
                    (string) $entry->author,
                    (string) $entry->msg
            );
        }

        return $retval;
    }

    /**
     * Parse the Xml result from Subversion
     *
     * @param string $xml
     * @param array  $arguments
     * @return array
     */
    protected function parseDiffOutput($output, array $arguments = array())
    {
        if (!isset($arguments['--xml']) || false === $arguments['--xml']) {
            // non xml results are not supported
            return $output;
        }

        if (!isset($arguments['--summarize']) || false === $arguments['--summarize']) {
            // non summarized results are not supported
            return $output;
        }

        $sxml = simplexml_load_string($output);

        $retval = array();
        foreach($sxml->xpath('//path') as $item) {
            $url = (string) $item;
            $path = ltrim(str_replace($this->client->getSvnUrl(''), '', $url), '/');

            // @todo move to a Mapper class
            $status = (string) $item->attributes()->item;
            switch($status) {
                case "modified":
                    $status = Status::MODIFIED;
                    break;
                case "added":
                    $status = Status::ADDED;
                    break;
                case "deleted":
                    $status = Status::DELETED;
                    break;
            }

            $file = new FileInfo(
                    $path,
                    FileInfo::FILE,
                    null,
                    $status
            );

            $retval[] = $file;
        }

        return $retval;
    }
}