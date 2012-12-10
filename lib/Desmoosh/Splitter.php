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

		return $this->_bestStack($stacks);
	}

	private function _bestStack($stacks)
	{
		$lowest = array();

		foreach($stacks as $stack)
		{
			//printf("Stack %s [count %d avg %d median %d]\n",
			//	implode('|', $stack), count($stack), self::avg($stack), self::median($stack));

			if(!isset($lowest[0]) ||
				(count($stack) <= $lowest[1]) ||
				(count($stack) == $lowest[1] && self::median($stack) < $lowers[2]))
			{
				$lowest = array($stack, count($stack), self::median($stack));
			}
		}

		return $lowest[0];
	}

	public static function median($array)
	{
		$lengths = array_map('strlen', $array);
		rsort($lengths);
    $middle = round(count($lengths) / 2);
		return $lengths[$middle-1];
	}

	public static function avg($array)
	{
		$lengths = array_map('strlen', $array);
		return array_sum($lengths) / count($array);
	}
}
