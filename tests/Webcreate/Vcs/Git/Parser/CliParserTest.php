<?php

namespace Test\Webcreate\Vcs\Git\Parser;

use Webcreate\Vcs\Git\Parser\CliParser;

class CliParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CliParser
     */
    private $parser;

    public function setUp()
    {
        $client = $this->prophesize('Webcreate\Vcs\Git');

        $this->parser = new CliParser();
        $this->parser->setClient(
            $client->reveal()
        );
    }

    /**
     * @test
     */
    public function it_parses_diff_output()
    {
        $diffOutput = <<<EOT
A       tests/Webcreate/Vcs/Git/Parser/CliParserTest.php
R       Webcreate/Vcs/Svn/WorkingRename.php
R062    Webcreate/Vcs/Svn/WorkingCopy.php    Webcreate/Vcs/Svn/WorkingRename.php
D       Webcreate/Vcs/Svn/AbstractSvn.php
M       Webcreate/Vcs/Svn.php
M001    Webcreate/Vcs/Svn.php
EOT;

        $parsedDiff = $this->parser->parseDiffOutput(
            $diffOutput,
            $arguments = array('--name-status' => true)
        );

        $fileInfo = current($parsedDiff);
        $this->assertSame('A', $fileInfo->getStatus());
        $this->assertSame('tests/Webcreate/Vcs/Git/Parser/CliParserTest.php', $fileInfo->getPathname());

        $fileInfo = next($parsedDiff);
        $this->assertSame('R', $fileInfo->getStatus());
        $this->assertSame('Webcreate/Vcs/Svn/WorkingRename.php', $fileInfo->getPathname());

        $fileInfo = next($parsedDiff);
        $this->assertSame('R', $fileInfo->getStatus());
        $this->assertSame('Webcreate/Vcs/Svn/WorkingRename.php', $fileInfo->getPathname());

        $fileInfo = next($parsedDiff);
        $this->assertSame('D', $fileInfo->getStatus());
        $this->assertSame('Webcreate/Vcs/Svn/AbstractSvn.php', $fileInfo->getPathname());

        $fileInfo = next($parsedDiff);
        $this->assertSame('M', $fileInfo->getStatus());
        $this->assertSame('Webcreate/Vcs/Svn.php', $fileInfo->getPathname());

        $fileInfo = next($parsedDiff);
        $this->assertSame('M', $fileInfo->getStatus());
        $this->assertSame('Webcreate/Vcs/Svn.php', $fileInfo->getPathname());
    }
}
