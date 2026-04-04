<?php

$finder = PhpCsFixer\Finder::create()
	->in(__DIR__ . '/src');

return (new PhpCsFixer\Config)
	->setRules([
	])
	->setFinder($finder)
	->setIndent("\t")
	->setLineEnding("\n")
	->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect());
