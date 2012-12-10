<?php

require_once(__DIR__.'/../vendor/autoload.php');

ini_set('memory_limit', '4096M');

use Desmoosh\WordGraph;
use Desmoosh\Splitter;

/**
 * Desmooshes phrases from STDIN
 */

if(count($argv) == 1)
	die("usage: {$argv[0]} graph.json");

$splitter = new Splitter(WordGraph::fromJson($argv[1]));
$stdin = fopen('php://stdin', 'r');

while($line = trim(fgets($stdin)))
{
	$time = microtime(true);
	printf("%s => ", $line);
	$words = $splitter->split($line);

	printf("%s (in %.2fms)\n",
		implode(' ', $words), (microtime(true)-$time)*1000);
}
