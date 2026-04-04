<?php

$finder = PhpCsFixer\Finder::create()
	->in(__DIR__ . '/src');

return (new PhpCsFixer\Config)
	->setRules([
		'array_indentation' => true,
		'array_syntax' => [
			'syntax' => 'short'
		],
	])
	->setFinder($finder)
	->setIndent("\t")
	->setLineEnding("\n")
	->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect());
