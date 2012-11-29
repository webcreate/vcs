<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Git\Parser;

use Webcreate\Vcs\Common\Commit;
use Webcreate\Vcs\Git;
use Webcreate\Vcs\Common\FileInfo;
use Webcreate\Vcs\Common\AbstractClient;
use Webcreate\Vcs\Common\Parser\ParserInterface;

class CliParser implements ParserInterface
{
    protected $client;

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(AbstractClient $client)
    {
        $this->client = $client;
        return $this;
    }

    public function parse($command, array $arguments = array(), $output)
    {
        switch($command) {
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

    public function parseLogOutput($output, array $arguments = array())
    {
        if (!isset($arguments['--pretty=']) || Git::PRETTY_FORMAT !== $arguments['--pretty=']) {
            // non name-status results are not supported
            return $output;
        }

        $xml = '<log>' . $output . '</log>';

        $sxml = simplexml_load_string($xml);

        $retval = array();
        foreach($sxml->logentry as $entry) {
            $retval[] = new Commit(
                    (string) $entry->commit,
                    new \DateTime((string)  $entry->date),
                    (string) $entry->author,
                    (string) $entry->msg
            );
        }

        return $retval;
    }

    public function parseStatusOutput($output, array $arguments = array())
    {
        if (!isset($arguments['--porcelain']) || false === $arguments['--porcelain']) {
            // non name-status results are not supported
            return $output;
        }

        $lines = explode("\n", rtrim($output));

        $retval = array();
        foreach($lines as $line) {
            if (preg_match('/([A-Z\?\s])([A-Z\?\s])\s(.*)( -> (.*))?/', $line, $matches)) {
                list($fullmatch, $x, $y, $file) = $matches;

                $file = new FileInfo($file, FileInfo::FILE, null, $x);

                $retval[] = $file;
            }
            else {
                throw new \Exception('Unable to parse line "'. $line . '"');
            }
        }

        return $retval;
    }

    public function parseDiffOutput($output, array $arguments = array())
    {
        if (!isset($arguments['--name-status']) || false === $arguments['--name-status']) {
            // non name-status results are not supported
            return $output;
        }

        $lines = explode("\n", rtrim($output));

        $retval = array();
        foreach($lines as $line) {
            if (preg_match('/([ACDMRTUXB])\s+(.*)?/', $line, $matches)) {
                list($fullmatch, $x, $file) = $matches;

                $file = new FileInfo(
                        $file,
                        FileInfo::FILE,
                        null,
                        $x
                );

                $retval[] = $file;
            }
            else {
                throw new \Exception('Unable to parse line "'. $line . '"');
            }
        }

        return $retval;
    }
}