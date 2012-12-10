<?php

require_once(__DIR__.'/../vendor/autoload.php');

if(count($argv) == 1)
	die("usage: {$argv[0]} graph.json");

ini_set('memory_limit', '4096M');

$graph = new \Desmoosh\WordGraph();
$stdin = fopen('php://stdin', 'r');
$counter = 0;

while($line = trim(fgets($stdin)))
{
	// break into words
	preg_match_all("/[A-z0-9]+\S/", $line, $match);

	foreach($match[0] as $word)
	{
		$graph->add($word);

		printf("Processed %s words (%.2f Mbytes of mem)\n",
			number_format($counter++), memory_get_usage()/1024/1024);
	}
}

printf("Writing %s\n", $argv[1]);
$graph->toJson($argv[1]);

