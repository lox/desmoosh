<?php

require_once(__DIR__.'/../vendor/autoload.php');

if(count($argv) == 1)
	die("usage: {$argv[0]} graph.json");

ini_set('memory_limit', '4096M');
gc_enable();

$graph = new \Desmoosh\WordGraph();
$stdin = fopen('php://stdin', 'r');
$counter = 0;

while($line = trim(fgets($stdin)))
{
	// break into word => frequency
	if(preg_match("/^([A-z0-9]+)\s*(\d+)?$/", $line, $match))
	{
		$graph->add($match[1], $match[2]);

		printf("Processed %s words, last frequency was %d (%.2f Mbytes of mem)\n",
			number_format($counter++), $match[2], memory_get_usage()/1024/1024);

		unset($match);
		unset($word);
	}
}

printf("Writing %s\n", $argv[1]);
$graph->toJson($argv[1]);

