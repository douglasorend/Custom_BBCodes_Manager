<?php
/**********************************************************************************
* Subs-CustomBBCodeAdmin.php - Admin Subs of the Custom BBCode Manager mod
*********************************************************************************
* This program is distributed in the hope that it is and will be useful, but
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE.
**********************************************************************************/
if (!defined('SMF')) 
	die('Hacking attempt...');

/**********************************************************************************
* Functions dealing with listing the BBcodes for the user:
*********************************************************************************/
function get_bbc_count()
{
	global $smcFunc;
	
	isAllowedTo('admin_forum');
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(*)
		FROM {db_prefix}bbcodes',
		array(
		)
	);
	list ($total_bbc) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	return $total_bbc;
}

function get_bbc_data($start, $items_per_page, $sort)
{
	global $smcFunc, $txt, $settings, $scripturl, $context;

	isAllowedTo('admin_forum');
	$request = $smcFunc['db_query']('', '
		SELECT *
		FROM {db_prefix}bbcodes
		ORDER BY {raw:sort}
		LIMIT {int:start}, {int:per_page}',
		array(
			'sort' => $sort,
			'start' => $start,
			'per_page' => $items_per_page,
		)
	);
	$bbcode = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		if (url_exists($settings['images_url'] . '/images/bbc/' . $row['tag'] . '.gif'))
 			$row['button'] = '<center><a href="' . $scripturl . '?action=admin;area=postsettings;sa=custombbc;edit=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><img src="' . $settings['images_url'] . '/bbc/' . $row['tag'] . '.gif" alt="" width=23 height=22 /></a></center>';
		else
			$row['button'] = '';
		$row['form'] = readable_bbc_type( $row['ctype'], $row['tag'] );
		$row['tag'] = '<a href="' . $scripturl . '?action=admin;area=postsettings;sa=custombbc;edit=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '">' . $row['tag'] . '</a>';
		$bbcode[] = $row;
	}
	$smcFunc['db_free_result']($request);

	return $bbcode;
}

function get_bbc_actions($row)
{
	global $scripturl, $txt, $context;
	return '<a href="' . $scripturl . '?action=admin;area=postsettings;sa=custombbc;edit=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '">' . $txt['List_modify'] . '</a>&nbsp;&nbsp;'.
		'<a href="' . $scripturl . '?action=admin;area=postsettings;sa=custombbc;' . ($row['enabled'] ? 'disable' : 'enable') . '=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '">' . ($row['enabled'] ? $txt['List_disable'] : $txt['List_enable']) . '</a>&nbsp;&nbsp;'.
		'<a href="' . $scripturl . '?action=admin;area=postsettings;sa=custombbc;delete=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" onclick="return confirm(\'' . $txt['List_delete_bbcode'] . '\');">' . $txt['List_delete'] . '</a>';
}

function readable_bbc_type($ctype, $tag)
{
	global $txt;
	
	// Translate the ctype variable into human-readable form:
	switch ($ctype)
	{
		case 'unparsed_equals':
			return '[' . $tag . '=xyz]' . $txt['Edit_content'] . '[/' . $tag . ']';
		case 'parsed_equals':
			return '[' . $tag . '=' . $txt['Edit_data'] . ']' . $txt['Edit_content'] . '[/' . $tag . ']';
		case 'unparsed_content':
			return '[' . $tag . ']' . $txt['Edit_uncontent'] . '[/' . $tag . ']';
		case 'closed':
			return '[' . $tag . '], [' . $tag . '/], [' . $tag . ' /]';
		case 'unparsed_commas':
			return '[' . $tag . '=1,2,3]' . $txt['Edit_content'] . '[/' . $tag . ']';
		case 'unparsed_commas_content':
			return '[' . $tag . '=1,2,3]' . $txt['Edit_uncontent'] . '[/' . $tag . ']';
		case 'unparsed_equals_content':
			return '[' . $tag . '=...]' . $txt['Edit_uncontent'] . '[/' . $tag . ']';
		case 'parsed_content':
		default:
			return '[' . $tag . ']' . $txt['Edit_content'] . '[/' . $tag . ']';
	}
}

/**********************************************************************************
* Adminstrative functions dealing with the BBcodes:
*********************************************************************************/
function get_bbc_row($id)
{
	global $smcFunc;
	
	isAllowedTo('admin_forum');
	$request = $smcFunc['db_query']('', '
		SELECT * 
		FROM {db_prefix}bbcodes 
		WHERE id = {int:id}',
		array(
			'id' => (int) $id,
		)
	);
	$row = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);
	$row['description'] = stripslashes( $row['description'] );
	$row['content'] = stripslashes( $row['content'] );
	$row['before'] = stripslashes( $row['before'] );
	$row['after'] = stripslashes( $row['after'] );
	$row['test'] = stripslashes( $row['test'] );
	return $row;
}

function remove_bbc_tag($id)
{
	global $smcFunc;

	isAllowedTo('admin_forum');
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}bbcodes 
		WHERE id = {int:id}',
		array(
			'id' => (int) $id,
		)
	);
}

function update_bbc_tag($id, $arr)
{
	global $smcFunc;

	isAllowedTo('admin_forum');
	$field_type = array(
		'id' => 'int', 
		'content' => 'text', 
		'before' => 'text', 
		'after' => 'text', 
		'enabled' => 'int', 
		'tag' => 'text',
		'description' => 'text', 
		'block_level' => 'int', 
		'trim' => 'text', 
		'ctype' => 'text', 
		'button' => 'int',
	);
	$values = array(
		'id' => (int) $id,
	);
	$sql = '';
	foreach ($arr as $key => $value)
	{
    if ($field_type[$key] == 'int')
      $values[$key] = (int) $value;
    else
      $values[$key] = $value;
		$sql = ($sql != '' ? ', ' : '') . $key . ' = {' . $field_type[$key] . ':' . $key . '}';
	}
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}bbcodes 
		SET '.$sql.'
		WHERE id = {int:id}',
		$values
	);
}

function copy_gif_to_themes($tag)
{
	global $smcFunc, $boarddir, $modSettings;

	isAllowedTo('admin_forum');

	// Make sure this is actually a GIF file is that less than 10kb:
	$extension = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
	if ($_FILES["file"]["type"] != "image/gif" || $_FILES["file"]["size"] > 10240 || $extension != 'gif')
		fatal_lang_error('Edit_Upload_invalid_upload', false);
	if ($_FILES["file"]["error"] > 0)
		fatal_lang_error('Edit_Upload_error', false);

	// Copy the image to the images/bbc folders in each theme:
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
	$dest = $boarddir . '/Themes/default/images/bbc/' . $tag . '.gif';
	move_uploaded_file($_FILES['file']['tmp_name'], $dest);
	while ($theme = $smcFunc['db_fetch_assoc']($request))
		copy($dest, $theme['value'] . '/images/bbc/' . $tag . '.gif');
	$smcFunc['db_free_result']($request);
}

function remove_gif_from_themes($tag)
{
	global $smcFunc, $boarddir;
	isAllowedTo('admin_forum');

	$request = $smcFunc['db_query']('', '
		SELECT value
		FROM {db_prefix}themes
		WHERE variable = {string:name}
			AND id_member = 0
			AND id_theme IN ({raw:known})',
		array(
			'name' => 'theme_dir', 
			'known' => $modSettings['knownThemes']
		)
	);
	while ($theme = $smcFunc['db_fetch_assoc']($request))
		unlink($theme['value'] . '/images/bbc/' . $tag . '.gif');
	$smcFunc['db_free_result']($request);
}

function get_max_bbcode_id()
{
	global $smcFunc;
	isAllowedTo('admin_forum');
	
	$request = $smcFunc['db_query']('', '
		SELECT MAX(id) AS max_id
		FROM {db_prefix}bbcodes',
		array()
	);
	$row = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);
	return $row['max_id'];
}

function replace_tag($data)
{
	global $smcFunc;
	isAllowedTo('admin_forum');
	
	$smcFunc['db_insert']('replace',
		'{db_prefix}bbcodes',
		array(
			'id' => 'int',  
			'content' => 'text', 
			'before' => 'text', 
			'after' => 'text', 
			'enabled' => 'int', 
			'tag' => 'text',
			'block_level' => 'int', 
			'trim' => 'text', 
			'ctype' => 'text', 
		),
		array(
			(int) $data['id'], 
			$data['content'], 
			$data['before'], 
			$data['after'], 
			(int) $data['enabled'], 
			$data['tag'],
			(int) $data['block_level'], 
			$data['trim'], 
			$data['ctype'], 
		),
		array('id')
	);
}

function url_exists($url) 
{
    // Version 4.x supported
    $handle   = curl_init($url);
    if (false === $handle)
        return false;
    curl_setopt($handle, CURLOPT_HEADER, false);
    curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
    curl_setopt($handle, CURLOPT_NOBODY, true);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
    $connectable = curl_exec($handle);
    curl_close($handle);   
    return $connectable;
}

/**********************************************************************************
* Function to test to see if the bbcode already exists:
*********************************************************************************/
function bbcode_exists($tag, $id)
{
	global $smcFunc, $test_tag;

	// Is this bbcode is already defined by SMF itself or another mod?
	$test_tag = $tag;
	remove_integration_function('integrate_bbc_codes', 'CustomBBCodes_BBCodes');
	add_integration_function('integrate_bbc_codes', 'bbcode_test');
	$data = parse_bbc('[b]' . $tag . '[/b]');
	remove_integration_function('integrate_bbc_codes', 'bbcode_test');
	add_integration_function('integrate_bbc_codes', 'CustomBBCodes_BBCodes');
	if ($test_tag === true)
		return true;
		
	// Okay, we got here.  Is this bbcode defined by using this mod?
	$request = $smcFunc['db_query']('', '
		SELECT *  
		FROM {db_prefix}bbcodes 
		WHERE tag = {string:tag}
			AND id <> {int:id}
		LIMIT 1',
		array(
			'tag' => addslashes($tag),
			'id' => (int) $id,
		)
	);
	$row = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);
	return !empty($row);
}

function bbcode_test(&$codes)
{
	global $test_tag;
	foreach ($codes as $bbcode)
	{
		if ($bbcode['tag'] == $test_tag)
		{
			$test_tag = true;
			return;
		}
	}
}

?>