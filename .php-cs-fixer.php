<?php

$finder = PhpCsFixer\Finder::create()
	->in(__DIR__ . '/src');

return (new PhpCsFixer\Config)
	->setRules([
		'array_indentation' => true,
		'array_syntax' => [
			'syntax' => 'short'
		],
		'binary_operator_spaces' => [
			'default' => 'single_space'
		],
		'blank_line_after_namespace' => true,
		'blank_line_after_opening_tag' => true,
		'cast_spaces' => [
			'space' => 'single'
		],
		'concat_space' => [
			'spacing' => 'one'
		],
		'elseif' => true,
		'indentation_type' => true,
		'no_closing_tag' => true,
		'no_empty_phpdoc' => true,
		'no_extra_blank_lines' => true,
		'no_superfluous_phpdoc_tags' => [
			'allow_mixed' => true,
		],
		'no_trailing_whitespace' => true,
		'no_trailing_whitespace_in_comment' => true,
		'no_whitespace_in_blank_line' => true,
		'unary_operator_spaces' => true
	])
	->setFinder($finder)
	->setIndent("\t")
	->setLineEnding("\n")
	->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect());