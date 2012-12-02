<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Test\Example;

use Webcreate\Vcs\Test\Util\SvnReposGenerator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class ExampleS2Test extends AbstractExampleTest
{
    protected function getSourceFilename()
    {
        return __DIR__ . '/../../../../../docs/03-subversion.md';
    }

    protected function getSnippetId()
    {
        return 'Example S2';
    }

    public function setUp()
    {
        $this->tmpdir = sys_get_temp_dir() . '/' . uniqid('wbcrte-svn-');

        $svnReposGenerator = new SvnReposGenerator(__DIR__ . '/../Fixtures/skeleton/svn/');
        list($this->svndir, $this->wcdir) = $svnReposGenerator->generate($this->tmpdir);

        parent::setUp();
    }

    public function processSnippet($php)
    {
        $svnurl = 'file://' . $this->svndir;
        $php = str_replace('svn://someserver/somerepo', $svnurl, $php);

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