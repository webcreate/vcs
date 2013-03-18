<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Test\Functional;

use Webcreate\Vcs\Common\Reference;
use Webcreate\Vcs\Common\VcsFileInfo;
use Webcreate\Vcs\Common\Status;
use Symfony\Component\Filesystem\Filesystem;
use Webcreate\Vcs\Test\Util\CommitGenerator;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Webcreate\Vcs\VcsInterface
     */
    protected $client;

    protected $checkoutDir;
    protected $exportDir;

    public function setUp()
    {
        $this->client = $this->getClient();

        $this->checkoutDir = sys_get_temp_dir() . '/' . uniqid('wbcrte-1');
        $this->exportDir = sys_get_temp_dir() . '/'. uniqid('wbcrte-2');
    }

    public function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->checkoutDir);
        $filesystem->remove($this->exportDir);
    }

    abstract public function getClient();
    abstract public function existingPathProvider();
    abstract public function existingSubfolderProvider();

    /**
     * Should provide atleast two revisions sorted new to old
     *
     * @return array()
     */
    abstract public function existingRevisionProvider();

    public function testLs()
    {
        $result = $this->client->ls('');

        $this->assertInternalType('array', $result);
        $this->assertContainsOnlyInstancesOf('Webcreate\\Vcs\\Common\\VcsFileInfo', $result);
    }

    /**
     * @expectedException Webcreate\Vcs\Exception\NotFoundException
     */
    public function testLsForNonExistingPathThrowsException()
    {
        $result = $this->client->ls('/non/existing/path');
    }

    /**
     * @dataProvider existingSubfolderProvider
     */
    public function testLsForSubfolder($subfolder)
    {
        $result = $this->client->ls($subfolder);

        $this->assertInternalType('array', $result);
        $this->assertContainsOnlyInstancesOf('Webcreate\\Vcs\\Common\\VcsFileInfo', $result);
    }


    /**
     * @dataProvider existingPathProvider
     */
    public function testLog($path)
    {
        $result = $this->client->log($path);

        $this->assertContainsOnlyInstancesOf('Webcreate\\Vcs\\Common\\Commit', $result);
    }

    public function testLogForEmptyPath()
    {
        $result = $this->client->log('');

        $this->assertContainsOnlyInstancesOf('Webcreate\\Vcs\\Common\\Commit', $result);
    }

    public function testChangelog()
    {
        $generator = new CommitGenerator($this->client);
        $generator->generate($this->checkoutDir, array(
            'First commit',
            'Another commit',
            'Awesome',
            'Still going!',
            'fixed issue #345'
        ));

        $log = $this->client->log('');
        $revisions = array_map(function ($commit) {
            return $commit->getRevision();
        }, $log);

        $result = $this->client->changelog($revisions[3], $revisions[1]);

        $this->assertContainsOnlyInstancesOf('Webcreate\\Vcs\\Common\\Commit', $result);
        $this->assertCount(3, $result);

        $messages = array_map(function ($commit) {
            return $commit->getMessage();
        }, $result);

        $expected = array(
            'Still going!',
            'Awesome',
            'Another commit',
        );

        $this->assertEquals($expected, $messages);
    }

    /**
     * @dataProvider existingPathProvider
     */
    public function testCat($path)
    {
        $result = $this->client->cat($path);

        $this->assertNotEmpty($result);
    }

    /**
     * @expectedException Webcreate\Vcs\Exception\NotFoundException
     */
    public function testCatForNonExistingPathThrowsException()
    {
        $result = $this->client->cat('/non/existing');
    }

    public function testCheckout()
    {
        $result = $this->client->checkout($this->checkoutDir);

        foreach($this->existingPathProvider() as $data) {
            list($filename) = $data;
            $this->assertFileExists($this->checkoutDir . '/' . $filename);
        }
    }

    public function testStatusUnversionedFile()
    {
        // first we need a checkout
        $result = $this->client->checkout($this->checkoutDir);

        // next add a unversioned file
        $tmpfile = tempnam($this->checkoutDir, 'statustest');

        $result = $this->client->status();

        $file = new VcsFileInfo(basename($tmpfile), $this->client->getHead());
        $file->setStatus(Status::UNVERSIONED);
        $expected = array($file);

        $this->assertContainsOnlyInstancesOf('Webcreate\\Vcs\\Common\\VcsFileInfo', $result);
        $this->assertEquals($expected, $result);
    }

    public function testStatusAddedFile()
    {
        // first we need a checkout
        $result = $this->client->checkout($this->checkoutDir);

        // next add a unversioned file
        $tmpfile = tempnam($this->checkoutDir, 'statustest');

        $this->client->add(basename($tmpfile));

        $result = $this->client->status('');

        $file = new VcsFileInfo(basename($tmpfile), $this->client->getHead());
        $file->setStatus(Status::ADDED);
        $expected = array($file);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider existingPathProvider
     */
    public function testStatusModifiedFile($filename)
    {
        // first we need a checkout
        $result = $this->client->checkout($this->checkoutDir);

        // next modify a file
        $tmpfile = $this->checkoutDir . '/' . $filename;
        file_put_contents($tmpfile, uniqid(null, true));

        $this->client->add($filename); // stage the change (needed for git)

        $result = $this->client->status();

        $file = new VcsFileInfo(basename($tmpfile), $this->client->getHead());
        $file->setStatus(Status::MODIFIED);
        $expected = array($file);

        $this->assertEquals($expected, $result);
    }

    public function testExportRootPath()
    {
        $this->client->export('', $this->exportDir);

        $provider = $this->existingPathProvider();
        foreach($provider as $entry) {
            list($filename) = $entry;
            $this->assertFileExists($this->exportDir . '/' . $filename);
        }
    }

    /**
     * @dataProvider existingPathProvider
     */
    public function testExportSingleFile($filename)
    {
        // we need to make sure the destination exists
        $filesystem = new Filesystem();
        $filesystem->mkdir($this->exportDir);

        $this->client->export($filename, $this->exportDir);

        $this->assertFileExists($this->exportDir . '/' . $filename);
    }

    /**
     * @dataProvider existingPathProvider
     */
    public function testDiff($filename)
    {
        // first we need a checkout
        $result = $this->client->checkout($this->checkoutDir);

        // next modify a file
        $tmpfile = $this->checkoutDir . '/' . $filename;
        file_put_contents($tmpfile, uniqid(null, true));

        // added to the staged file list
        $this->client->add($filename);

        // now let's commit it
        $this->client->commit("changed file contents");

        // get the log
        $log = $this->client->log($filename);
        $firstLog = end($log);
        $firstRevision = $firstLog->getRevision();

        $diff = $this->client->diff($filename, $filename, $firstRevision);

        $file = new VcsFileInfo($filename, $this->client->getHead());
        $file->setStatus(Status::MODIFIED);
        $expected = array($file);

        $this->assertContainsOnlyInstancesOf('Webcreate\Vcs\Common\VcsFileInfo', $diff);
        $this->assertEquals($expected, $diff);
    }

    public function testBranches()
    {
        $branches = $this->client->branches();

        $branchNames = array_map(function($ref) {
            return $ref->getName();
        }, $branches);

        $this->assertInternalType('array', $branches);
        $this->assertContains('feature1', $branchNames);
        $this->assertContainsOnlyInstancesOf('Webcreate\Vcs\Common\Reference', $branches);
    }

    public function testTags()
    {
        $tags = $this->client->tags();

        $this->assertInternalType('array', $tags);
    }

    public function testSwitchingToDifferentBranch()
    {
        $result = $this->client->setHead(new Reference('feature1'));
    }

    public function testRevisionCompare()
    {
        list ($newerRevision, $olderRevision) = $this->existingRevisionProvider();

        $this->assertEquals(1, $this->client->revisionCompare($newerRevision, $olderRevision));
        $this->assertEquals(0, $this->client->revisionCompare($olderRevision, $olderRevision));
        $this->assertEquals(-1, $this->client->revisionCompare($olderRevision, $newerRevision));
    }
}
