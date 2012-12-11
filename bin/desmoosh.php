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

while($original = trim(fgets($stdin)))
{
	// trim of tld's from domain names
	$line = preg_replace('/\.(\w{2,})$/','',strtolower($original));
	$line = preg_replace('/[^a-z]+/i','',$line);
	$time = microtime(true);

	printf("%s => ", $original);
	$words = $splitter->split($line);

	printf("%s (in %.2fms)\n",
		implode(' ', $words) ?: 'FAILED', (microtime(true)-$time)*1000);

}
