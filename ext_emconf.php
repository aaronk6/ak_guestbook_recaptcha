<?php
$EM_CONF[$_EXTKEY] = array(
	'title' => 'Guestbook Invisible reCAPTCHA',
	'description' => 'Adds Invisible reCAPTCHA to ve_guestbook',
	'category' => 'plugin',
	'author' => 'aaronk',
	'author_company' => '',
	'author_email' => '',
	'dependencies' => 'extbase',
	'state' => 'alpha',
	'clearCacheOnLoad' => '1',
	'version' => '0.1.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '7.6.0-7.6.99',
			'extbase' => '1.0.0-0.0.0'
			)
		)
);
