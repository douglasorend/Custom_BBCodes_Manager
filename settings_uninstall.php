<?php
/**********************************************************************************
* settings_uninstall.php                                                          *
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:
*	http://opensource.org/licenses/BSD-2-Clause
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
***********************************************************************************
* This file is a simplified database installer. It does what it is suppoed to.    *
**********************************************************************************/

// If we have found SSI.php and we are outside of SMF, then we are running standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');

// Attach some other crap for subforums to prevent 404 errors:
global $boarddir;
$request = $smcFunc['db_query']('', '
	SELECT value
	FROM {db_prefix}themes
	WHERE variable = {string:name}
		AND id_member = 0
		AND id_theme IN ({raw:known})',
	array(
		'name' => 'theme_dir',
		'known' => $modSettings['knownThemes'],
	)
);
while ($row = $smcFunc['db_fetch_assoc']($request))
{
	$oldFile = @file_get_contents($row['value'] . '/images/bbc/.htaccess');
	$insert = "\n\n# CUSTOM BBCODES MANAGER BEGIN\nRewriteEngine on\nRewriteRule ^(.*)\.(gif|png)_(\d+) $1\.$2\nRewriteRule ^(.*)_(\d+)\.(gif|png) $1\.$3\n# CUSTOM BBCODES MANAGER ENDS\n";
	$oldFile = str_replace($insert, '', $oldFile);
	if ($handle = fopen($row['value'] . '/images/bbc/.htaccess', 'w'))
	{
		fwrite($handle, $oldFile);
		fclose($handle);
		@chmod($subforum['forumdir'] . '/.htaccess', 0755);
	}
}
$smcFunc['db_free_result']($request);

// Echo that we are done if necessary:
if (SMF == 'SSI')
	echo 'Settings changes should be made now...';
?>