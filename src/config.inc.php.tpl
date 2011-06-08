<?php
	require('/var/www/libs/onphp-dev/global.inc.php.tpl');

	define('PATH_BASE', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

	define('PATH_CLASSES', PATH_BASE.'classes'.DIRECTORY_SEPARATOR);
	define('PATH_CONTROLLERS', PATH_BASE.'controllers'.DIRECTORY_SEPARATOR);
	define('PATH_TEMPLATES', PATH_BASE.'templates'.DIRECTORY_SEPARATOR);

	define('PATH_WEB', 'http://<PROJECT-DOMAIN-NAME>/');
	define('PATH_WEB_RELATIVE', '/');

	ini_set(
		'include_path', get_include_path().PATH_SEPARATOR
		.PATH_CLASSES.PATH_SEPARATOR
		.PATH_CLASSES.'Auto'.PATH_SEPARATOR
		.PATH_CLASSES.'Auto'.DIRECTORY_SEPARATOR.'Business'.PATH_SEPARATOR
		.PATH_CLASSES.'Auto'.DIRECTORY_SEPARATOR.'DAOs'.PATH_SEPARATOR
		.PATH_CLASSES.'Auto'.DIRECTORY_SEPARATOR.'Proto'.PATH_SEPARATOR
		.PATH_CLASSES.'Business'.PATH_SEPARATOR
		.PATH_CLASSES.'DAOs'.PATH_SEPARATOR
		.PATH_CLASSES.'Proto'.PATH_SEPARATOR
		.PATH_CLASSES.'Utils'.PATH_SEPARATOR

		.PATH_CONTROLLERS.PATH_SEPARATOR
		.PATH_CONTROLLERS.'decorators'.PATH_SEPARATOR
	);

	$dbLink = DB::spawn('PgSQL', 'invitation-service', 'invitation-service', 'localhost', 'invitation');

	DBPool::me()->
		addLink('invitation', $dbLink)->
		setDefault($dbLink);

	Cache::setPeer(
		WatermarkedPeer::create(
			Memcached::create(),
			'invitation'
		)
	);

	Cache::setDefaultWorker('NullDaoWorker');

	RouterRewrite::me()->
		addRoute(
			'*',
			RouterTransparentRule::create(':key')->
				setDefaults(array('area' => 'Invitation', 'key' => ''))
		);
?>
