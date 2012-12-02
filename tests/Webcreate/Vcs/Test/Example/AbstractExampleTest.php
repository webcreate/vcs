<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Test\Example;

abstract class AbstractExampleTest extends \PHPUnit_Framework_TestCase
{
    protected $snippetFile;

    abstract protected function getSourceFilename();
    abstract protected function getSnippetId();

    public function setUp()
    {
        $file = $this->getSourceFilename();
        $id   = $this->getSnippetId();

        $this->extractSnippet($file, $id);
    }

    protected function extractSnippet($file, $id)
    {
        $contents = file_get_contents($file);

        if (preg_match('/``` php\n\/\/ '.preg_quote($id, '/').'([^`]+)```/s', $contents, $matches)) {
            list(,$php) = $matches;

            $php = "<?php\n" . $php;
            $php = $this->processSnippet($php);

            $this->snippetFile = tempnam(sys_get_temp_dir(), 'example');
            file_put_contents($this->snippetFile, $php);
        }
        else {
            $this->markTestIncomplete(sprintf('Unable to extract snippet from %s', $file));
        }
    }

    public function processSnippet($php)
    {
        return $php;
    }

    public function tearDown()
    {
        if (file_exists($this->snippetFile)) {
            unlink($this->snippetFile);
        }
    }
}