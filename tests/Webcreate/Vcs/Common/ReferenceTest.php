<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

use Webcreate\Vcs\Common\Reference;

class ReferenceTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $reference = new Reference('master', Reference::BRANCH);

        $this->assertInstanceOf('Webcreate\Vcs\Common\Reference', $reference);

        $reference = new Reference('master');

        $this->assertInstanceOf('Webcreate\Vcs\Common\Reference', $reference);

        $reference = new Reference('master', 'branch');

        $this->assertInstanceOf('Webcreate\Vcs\Common\Reference', $reference);

        $reference = new Reference('master', 'tag');

        $this->assertInstanceOf('Webcreate\Vcs\Common\Reference', $reference);
    }

    public function testGetName()
    {
        $reference = new Reference('master', 'tag');

        $this->assertEquals('master', $reference->getName());
    }

    public function testGetType()
    {
        $reference = new Reference('master', 'tag');

        $this->assertEquals(Reference::TAG, $reference->getType());
    }
}