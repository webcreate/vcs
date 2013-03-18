<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Common\Parser;

use Webcreate\Vcs\Common\AbstractClient;

/**
 * Interface for output parsers.
 *
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 */
interface ParserInterface
{
    /**
     * Parse output of the adapter
     *
     * @param string $command   command issued to the adapter
     * @param array  $arguments arguments for the command
     * @param string $output    output of the command
     */
    public function parse($command, array $arguments = array(), $output);

    /**
     * Informs the parser about the client
     *
     * @param AbstractClient $client
     */
    public function setClient(AbstractClient $client);
}
