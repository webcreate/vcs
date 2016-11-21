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
R       Webcreate/Vcs/Svn/WorkingCopy.php
R062    Webcreate/Vcs/Svn/SvnAdmin.php
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
        $this->assertSame('Webcreate/Vcs/Svn/WorkingCopy.php', $fileInfo->getPathname());

        $fileInfo = next($parsedDiff);
        $this->assertSame('R', $fileInfo->getStatus());
        $this->assertSame('Webcreate/Vcs/Svn/SvnAdmin.php', $fileInfo->getPathname());
    }
}
