<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Common\Adapter;

use Webcreate\Vcs\Common\AbstractClient;

/**
 * Interface for VCS backends
 *
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 */
interface AdapterInterface
{
    /**
     * Execute command
     *
     * @param  string $command
     * @param  array  $arguments
     * @param  string $cwd       path to run the command from
     * @return string
     */
    public function execute($command, array $arguments = array(), $cwd = null);

    /**
     * Informs the adapter about the client
     *
     * @param AbstractClient $client
     */
    public function setClient(AbstractClient $client);
}
