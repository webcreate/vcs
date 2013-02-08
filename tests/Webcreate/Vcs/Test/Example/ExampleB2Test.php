<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Test\Example;

use Webcreate\Vcs\Test\Util\GitReposGenerator;
use Webcreate\Vcs\Test\Util\SvnReposGenerator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * @group example
 *
 */
class ExampleB2Test extends AbstractExampleTest
{
    protected function getSourceFilename()
    {
        return __DIR__ . '/../../../../../docs/01-basic-usage.md';
    }

    protected function getSnippetId()
    {
        return 'Example B2';
    }

    public function setUp()
    {
        $this->tmpdir = sys_get_temp_dir() . '/' . uniqid('wbcrte-git-');

        $generator = new GitReposGenerator(__DIR__ . '/../Fixtures/skeleton/git/');
        list($this->vcsdir, $this->wcdir) = $generator->generate($this->tmpdir);

        parent::setUp();
    }

    public function processSnippet($php)
    {
        $vcsurl = 'file://' . $this->vcsdir;
        $php = str_replace('https://someserver/somerepo', $vcsurl, $php);

        return $php;
    }

    public function tearDown()
    {
        parent::tearDown();

        $filesystem = new Filesystem();
        $filesystem->remove($this->tmpdir);
    }

    /**
     * @coversNothing
     */
    public function testExample()
    {
        require $this->snippetFile;
    }
}