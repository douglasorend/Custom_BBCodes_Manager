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
	global $smcFunc, $settings, $scripturl, $context;
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
		if (file_exists($settings['theme_dir'] . '/images/bbc/' . $row['tag'] . '.gif'))
 			$row['button'] = '<center><a href="' . $scripturl . '?action=admin;area=postsettings;sa=custombbc;edit=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><img src="' . $settings['images_url'] . '/bbc/' . $row['tag'] . '.gif" alt="" width=23 height=22 /></a></center>';
		else
			$row['button'] = '';
		$row['form'] = readable_bbc_type($row['ctype'], $row['tag']);
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
			return '[' . $tag . '=xyz]' . $txt['Edit_uncontent'] . '[/' . $tag . ']';
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
	if (!empty($row))
	{
		$row['tag'] = stripslashes($row['tag']);
		$row['description'] = stripslashes($row['description']);
		$row['content'] = stripslashes($row['content']);
		$row['before'] = stripslashes($row['before']);
		$row['after'] = stripslashes($row['after']);
		$row['trim'] = stripslashes($row['trim']);
		$row['ctype'] = stripslashes($row['ctype']);
	}
	return $row;
}

function remove_bbc_tag($id)
{
	global $smcFunc;
	isAllowedTo('admin_forum');

	// Retrieve the tag name and remove the image button from the server:
	$tag = get_bbc_row($id);
	if (isset($tag['tag']))
		remove_gif_from_themes($tag['tag']);
	
	// Delete the entry from the database:
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}bbcodes
		WHERE id = {int:id}',
		array(
			'id' => (int) $id,
		)
	);

	// Empty out the cached arrays so that it updates properly:
	cache_put_data('bbcodes_custom', null, 86400);
	cache_put_data('bbcodes_buttons', null, 86400);
}

function update_bbc_tag($id, $new)
{
	global $smcFunc;
	isAllowedTo('admin_forum');

	$values = get_bbc_row((int) $id);
	if (!isset($values['id']) && $values['id'] != ((int) $id))
		return;
	$values = array(
		'id' => (int) $id,
		'content' => isset($new['content']) ? $new['content'] : $values['content'],
		'before' => isset($new['before']) ? $new['before'] : $values['before'],
		'after' => isset($new['after']) ? $new['after'] : $values['after'],
		'enabled' => (int) isset($new['enabled']) ? $new['enabled'] : $values['enabled'],
		'tag' => isset($new['tag']) ? $new['tag'] : $values['tag'],
		'description' => isset($new['description']) ? $new['description'] : $values['description'],
		'block_level' => (int) isset($new['block_level']) ? $new['block_level'] : $values['block_level'],
		'trim' => isset($new['trim']) ? $new['trim'] : $values['trim'],
		'ctype' => isset($new['ctype']) ? $new['ctype'] : $values['ctype'],
		'button' => (int) isset($new['button']) ? $new['button'] : $values['button'],
	);
	replace_tag($values);
}

function replace_tag($data)
{
	global $smcFunc;
	isAllowedTo('admin_forum');

	// Insert the information:
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
			'button' => 'int',
			'description' => 'text',
		),
		array(
			(int) $data['id'],
			isset($data['content']) ? addslashes($data['content']) : '',
			isset($data['before']) ? addslashes($data['before']) : '',
			isset($data['after']) ? addslashes($data['after']) : '',
			(int) isset($data['enabled']) ? $data['enabled'] : 1,
			isset($data['tag']) ? addslashes($smcFunc['strtolower']($data['tag'])) : '',
			(int) isset($data['block_level']) ? $data['block_level'] : 0,
			isset($data['trim']) ? addslashes($data['trim']) : '',
			isset($data['ctype']) ? addslashes($data['ctype']) : '',
			(int) isset($data['button']) ? $data['button'] : 0,
			isset($data['description']) ? addslashes($data['description']) : '',
		),
		array('id')
	);

	// Empty out the cached arrays so that it updates properly:
	cache_put_data('bbcodes_custom', null, 86400);
	cache_put_data('bbcodes_buttons', null, 86400);
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
	global $smcFunc, $boarddir, $modSettings;
	isAllowedTo('admin_forum');

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
	while ($theme = $smcFunc['db_fetch_assoc']($request))
		@unlink($theme['value'] . '/images/bbc/' . $tag . '.gif');
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

/**********************************************************************************
* Function to test to see if the bbcode already exists:
*********************************************************************************/
function bbcode_exists($tag, $id)
{
	global $smcFunc;
	isAllowedTo('admin_forum');

	// Is this bbcode is already defined by SMF itself or another mod?
	remove_integration_function('integrate_bbc_codes', 'CustomBBCodes_BBCodes');
	$temp = parse_bbc(false);
	add_integration_function('integrate_bbc_codes', 'CustomBBCodes_BBCodes');
	$bbcTags = array();
	foreach ($temp as $tagname)
		$bbcTags[] = $tagname['tag'];
	$bbcTags = array_unique($bbcTags);
	if (in_array($tag, $bbcTags))
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

?>