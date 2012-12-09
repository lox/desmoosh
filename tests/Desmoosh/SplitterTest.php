<?php

namespace Desmoosh;

class SplitterTest extends \PHPUnit_Framework_TestCase
{
	public function testBasicSplitting()
	{
		$graph = new WordGraph(array('llamas','love','lettuce'));
		$splitter = new Splitter($graph);

		$this->assertEquals(
			$splitter->split('llamaslovelettuce'),
			array('llamas','love','lettuce')
		);
	}

	public function testSplittingWithSubwords()
	{
		$graph = new WordGraph(array('llamas','love','lettuce', 'llama', 'let', 'am'));
		$splitter = new Splitter($graph);

		$this->assertEquals(
			$splitter->split('llamaslovelettuce'),
			array('llamas','love','lettuce')
		);
	}
}
