<?php

namespace Desmoosh;

class WordGraph
{
	const WORD_BOUNDARY='$';

	private $_words=array();



  public function __construct($words=array())
	{
		foreach($words as $word)
			$this->word($word);
	}

	public function word($word)
	{
		$word = preg_replace('/[^a-z]+/',' ',trim(strtolower($word)));

		for($i=0; $i<=strlen($word); $i++)
		{
			$prefix = substr($word, 0, $i) ?: '^';
			$chr = substr($word, $i, 1) ?: '$';

			if(!isset($this->_words[$prefix][$chr]))
				$this->_words[$prefix][$chr] = 0;

			$this->_words[$prefix][$chr]++;
		}
	}

	public function sentence($string)
	{
		// break into words
		preg_match_all("/[A-z0-9]+\S/", $sentence, $match);

		foreach($match[0] as $word)
		{
			for($i=1; $i<=strlen($word); $i++)
				$this->word(substr($word, 0, $i));
		}
	}

	public function contains($word)
	{
		return isset($this->_words[$word][self::WORD_BOUNDARY]);
	}

	public static function fromJson($file)
	{
		$graph = new self();
		$graph->_words = json_decode(file_get_contents($file), true);
		return $graph;
	}

	public function toJson($file)
	{
		file_put_contents($file, json_encode($this->_words));
	}
}
