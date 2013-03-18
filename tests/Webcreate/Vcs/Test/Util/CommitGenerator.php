<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Test\Util;

use Webcreate\Vcs\VcsInterface;

class CommitGenerator
{
    public static $filenameStubs = array(
        'hello.txt',
        'workd.md'
    );

    public function __construct(VcsInterface $client)
    {
        $this->client = $client;
    }

    public function generate($checkoutDir, array $messages = array())
    {
        // first we need a checkout
        $result = $this->client->checkout($checkoutDir);

        foreach ($messages as $message) {
            // create a file
            $filename = array_rand(self::$filenameStubs);

            $tmpfile = $checkoutDir . '/' . $filename;
            file_put_contents($tmpfile, uniqid(null, true));

            // added to the staged file list
            $this->client->add($filename);

            // now let's commit it
            $this->client->commit($message);
        }
    }
}
