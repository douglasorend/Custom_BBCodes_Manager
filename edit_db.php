<?php
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

if ((SMF == 'SSI') && !$user_info['is_admin'])
   die('Admin priveleges required.');
db_extend('packages');

// Build the bbcodes table:
$columns = array(
	array(
		'name' => 'id',
		'type' => 'int',
		'size' => 8,
		'unsigned' => true,
	),
	array(
		'name' => 'enabled',
		'type' => 'int',
		'size' => 2,
		'unsigned' => true,
	),
	array(
		'name' => 'button',
		'type' => 'int',
		'size' => 2,
		'unsigned' => true,
	),
	array(
		'name' => 'tag',
		'type' => 'text',
		'size' => 255,
	),
	array(
		'name' => 'description',
		'type' => 'text',
	),
	array(
		'name' => 'block_level',
		'type' => 'int',
		'size' => 2,
		'unsigned' => true,
	),
	array(
		'name' => 'trim',
		'type' => 'text',
		'size' => 10,
	),
	array(
		'name' => 'ctype',
		'type' => 'text',
		'size' => 20,
	),
	array(
		'name' => 'before',
		'type' => 'text',
	),
	array(
		'name' => 'after',
		'type' => 'text',
	),
	array(
		'name' => 'content',
		'type' => 'text',
	),
	array(
		'name' => 'css',
		'type' => 'text',
	),
);
$indexes = array(
	array(
		'type' => 'primary',
		'columns' => array('id'),
	),
);
$smcFunc['db_create_table']('{db_prefix}bbcodes', $columns, $indexes, array(), 'update_remove');

// Force all tags to be lowercase.  It's important for some reason! :p
$smcFunc['db_query']('', '
	UPDATE {db_prefix}bbcodes
	SET tag = LOWER(tag)',
	array()
);

// Echo that we are done if necessary:
if (SMF == 'SSI')
	echo 'DB Changes should be made now...';
?>