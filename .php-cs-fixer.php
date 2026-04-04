<?php

$finder = PhpCsFixer\Finder::create()
	->in(__DIR__ . '/src');

return (new PhpCsFixer\Config)
	->setRules([
		'array_indentation' => true,
		'array_syntax' => [
			'syntax' => 'short'
		],
		'blank_line_after_opening_tag' => true,
		'cast_spaces' => [
			'space' => 'single'
		],
		'no_closing_tag' => true,
		'no_empty_phpdoc' => true,
	])
	->setFinder($finder)
	->setIndent("\t")
	->setLineEnding("\n")
	->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect());
