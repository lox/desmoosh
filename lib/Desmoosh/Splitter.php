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

		for($i=0; $i<=strlen($string); $i++)
		{
			$prefix = substr($string, 0, $i+1);

			// hack for numerics
			if(is_numeric($prefix) && preg_match("/^\d+/", $string, $m))
			{
				$words[$m[0]] = $this->_enumerate(substr($string, strlen($m[0])));
				$i += (strlen($m[0])-1);
				continue;
			}

			if(
				(strlen($prefix) > 1 || in_array($prefix, array('i', 'a'))) &&
				$this->_graph->contains($prefix))
			{
				$newStack = array_merge($stack, array($prefix));

				// don't follow routes with long chains of little words
				if(count($newStack) > 4 && floor($this->_avg($newStack)) <= 2)
					continue;

				$words[$prefix] = $this->_enumerate(substr($string, strlen($prefix)), $newStack);

				// prune enumerations that are missing pieces
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

		return $this->_bestStack($stacks);
	}

	private function _bestStack($stacks)
	{
		$lowest = array();

		foreach($stacks as $stack)
		{
			if(getenv('DEBUG'))
			{
				printf("Stack %s [count %d avg %d score %d]\n",
					implode('|', $stack), count($stack), $this->_avg($stack), $this->_frequency($stack));
			}

			if(!isset($lowest[0]) ||
				(count($stack) < $lowest[1]) ||
				(count($stack) == $lowest[1] && $this->_frequency($stack) > $lowest[2]))
			{
				$lowest = array($stack, count($stack), $this->_frequency($stack));
			}
		}

		return $lowest[0];
	}

	private function _avg($array)
	{
		$lengths = array_map('strlen', $array);
		return count($array) ? array_sum($lengths) / count($array) : 0;
	}

	private function _frequency($array)
	{
		$sum = 0;

		foreach($array as $word)
			$sum += $this->_graph->frequency($word);

		return $sum;
	}
}
