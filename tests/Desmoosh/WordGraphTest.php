<?php

namespace Desmoosh;

class WordGraphTest extends \PHPUnit_Framework_TestCase
{
	public function testBuildingWordGraph()
	{
		$graph = new WordGraph(array('llamas','love','lettuce'));

		$this->assertTrue($graph->contains('llamas'));
		$this->assertTrue($graph->contains('love'));
		$this->assertTrue($graph->contains('lettuce'));
		$this->assertFalse($graph->contains('lunch'));
	}
}
