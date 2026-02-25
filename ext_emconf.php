<?php

$EM_CONF[$_EXTKEY] =  [
	'title' => 'Rel nofollow',
	'description' => 'Adds a rel-nofollow-attribute to extern links. You can exclude URLs by typoscript.',
	'category' => 'fe',
	'version' => '13.4.2',
	'state' => 'stable',
	'author' => 'Peter Benke',
	'author_email' => 'info@typomotor.de',
	'author_company' => null,
	'constraints' =>[
		'depends' => [
            'typo3' => '13.4.0-13.4.99',
		],
	],
];
