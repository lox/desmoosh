<?php

namespace Desmoosh;

class WordGraph
{
	const WORD_START='^';
	const WORD_END='$';

	private $_graph;

  public function __construct($words=array())
	{
		$this->_graph = array('^'=>array('$'=>array()));

		foreach($words as $word)
			$this->add($word);
	}

	public function add($word)
	{
		$word = preg_replace('/[^a-z]+/',' ',trim(strtolower($word))).self::WORD_END;
		$node =& $this->_graph[self::WORD_START];

		foreach(str_split($word) as $chr)
		{
			if(!isset($node[$chr]))
				$node[$chr] = array();

			$node =& $node[$chr];
		}
	}

	public function contains($word)
	{
		return ($node = $this->lookup($word)) && isset($node[self::WORD_END]);
	}

	public function lookup($string)
	{
		$node =& $this->_graph[self::WORD_START];

		foreach(str_split($string) as $chr)
		{
			if(!isset($node[$chr]))
				return false;

			$node =& $node[$chr];
		}

		return $node;
	}

	public static function fromJson($file)
	{
		$graph = new self();
		$graph->_graph = json_decode(file_get_contents($file), true);
		return $graph;
	}

	public function toJson($file)
	{
		file_put_contents($file, json_encode($this->_graph));
	}
}
