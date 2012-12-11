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
	private function _enumerate($string, $stack=array())
	{
		$words = array();

		// don't include chains that are perfect subsets
		if(count($stack) > 1 && $this->_graph->contains(implode('', $stack)))
			return array();

		for($i=0; $i<=strlen($string); $i++)
		{
			$prefix = substr($string, 0, $i+1);

			// handle numeric prefixes
			if(is_numeric($prefix) && preg_match("/^\d+/", $string, $m))
			{
				$words[$m[0]] = $this->_enumerate(substr($string, strlen($m[0])));
				$i += (strlen($m[0])-1);
			}
			// handles strings
			else if((strlen($prefix) > 1 || in_array($prefix, array('i', 'a'))) &&
				$this->_graph->contains($prefix))
			{
				$newStack = array_merge($stack, array($prefix));

				// don't include chains with long chains of little words
				if(count($newStack) > 4 && floor($this->_avgLength(array_slice($newStack, -4))) <= 2)
					return array();

				$words[$prefix] = $this->_enumerate(substr($string, strlen($prefix)), $newStack);
			}
		}

		// prune incomplete chains
		foreach($words as $key=>$below)
			if(!count($words[$key]) && $key != $string)
				unset($words[$key]);

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

		return $this->_bestStack($stacks);
	}

	private function _bestStack($stacks)
	{
		$lowest = array();

		if(getenv('DEBUG'))
			printf("Found %d permutations\n", count($stacks));

		foreach($stacks as $stack)
		{
			if(getenv('DEBUG'))
			{
				printf("Stack %s [count %d avg %d score %d]\n",
					implode('|', $stack), count($stack), $this->_avgLength($stack), $this->_score($stack));
			}

			if(!isset($lowest[0]) ||
				(count($stack) < $lowest[1]) ||
				(count($stack) == $lowest[1] && $this->_score($stack) > $lowest[2]))
			{
				$lowest = array($stack, count($stack), $this->_score($stack));
			}
		}

		return $lowest[0];
	}

	private function _avgLength($array)
	{
		$lengths = array_map('strlen', $array);
		return count($array) ? array_sum($lengths) / count($array) : 0;
	}

	private function _median($numbers)
	{
		rsort($numbers);
		$middle = round(count($numbers) / 2);

		if(!count($numbers))
			return 0;
		if(count($numbers) % 2 == 1)
			return $numbers[$middle-1];
		else
			return ($numbers[$middle-1] + $numbers[$middle]) / 2;
	}

	private function _score($array)
	{
		$freq = array();

		foreach($array as $word)
			$freq []= $this->_graph->frequency($word);

		return array_sum($freq);
	}
}
