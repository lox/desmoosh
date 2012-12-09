<?php

namespace Desmoosh;

class Splitter
{
	private $_graph;

	public function __construct($graph)
	{
		$this->_graph = $graph;
	}

	/**
	 * Returns a tree structure of assoc arrays showing all
	 * word permutations
	 */
	private function _enumerate($string)
	{
		$words = array();

		for($i=0; $i<=strlen($string); $i++)
		{
			$prefix = substr($string, 0, $i+1);

			if($this->_graph->contains($prefix))
			{
				$words[$prefix] = $this->_enumerate(substr($string, strlen($prefix)));

				// prune words with no complete sub strings
				if(!count($words[$prefix]) && $prefix != $string)
					unset($words[$prefix]);
			}
		}

		return $words;
	}

	/**
	 * Depth first traversal of the tree structure from {@link _enumerate()},
	 * calls a closure when all words are found
	 */
	private function _traverse($words, $stack, $closure)
	{
		if(count($words) == 0)
			$closure($stack);
		else
			foreach($words as $w=>$ws)
				$this->_traverse($ws, array_merge($stack, array($w)), $closure);
	}

	/**
	 * Splits a string into words, for instance thisisasentence becomes [this,is,a,sentence]
	 */
	public function split($string)
	{
		$stacks = array();

		$this->_traverse($this->_enumerate($string), array(), function($stack) use(&$stacks) {
			$stacks []= $stack;
		});

		usort($stacks, function($a, $b) {
			return strcmp(count($a), count($b)) * -1;
		});

		return array_pop($stacks);
	}
}
