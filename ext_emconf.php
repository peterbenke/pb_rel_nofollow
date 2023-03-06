<?php

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Rel nofollow',
	'description' => 'Adds a rel-nofollow-attribute to extern links. You can exclude URLs by typoscript.',
	'category' => 'fe',
	'version' => '11.5.1',
	'state' => 'stable',
	'author' => 'Peter Benke',
	'author_email' => 'info@typomotor.de',
	'author_company' => null,
	'constraints' =>[
		'depends' => [
			'typo3' => '11.5.0-11.5.99',
		],
	],
);
