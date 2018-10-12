<?php
/**********************************************************************************
* CustomBBCode.php - PHP implementation of the Custom BBCode Manager mod
*********************************************************************************
* This program is distributed in the hope that it is and will be useful, but
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE .
**********************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');

function CustomBBCodes_Browse()
{
	global $modSettings, $txt, $scripturl, $sourcedir, $context;

	// Load some basic stuff related to both the CustomBBCode edit and browse functions:
	isAllowedTo('admin_forum');
	loadTemplate('CustomBBCodes');
	loadLanguage('CustomBBCodes');
	require_once($sourcedir . '/Subs-CustomBBCodesAdmin.php');

	// Editing or Creating a tag?
	if (isset($_POST['newtag']))
	{
		checkSession('get');
		return CustomBBCodes_Edit(-1);
	}
	if (isset($_GET['edit']))
	{
		checkSession('get');
		return CustomBBCodes_Edit((int) $_GET['edit']);
	}

	if (isset($_GET['delete']))
	{
		checkSession('get');
		remove_bbc_tag((int) $_GET['delete']);
		redirectexit('action=admin;area=' . $_GET['area'] . ';sa=custombbc');
	}

	// Enabling or disabling a tag?
	if (isset($_GET['enable']))
	{
		checkSession('get');
		update_bbc_tag((int) $_GET['enable'], array(
			'enabled' => 1,
		));
		redirectexit('action=admin;area=' . $_GET['area'] . ';sa=custombbc');
	}
	if (isset($_GET['disable']))
	{
		checkSession('get');
		update_bbc_tag((int) $_GET['disable'], array(
			'enabled' => 0,
		));
		redirectexit('action=admin;area=' . $_GET['area'] . ';sa=custombbc');
	}

	// Get latest version of the mod and display whether current mod is up-to-date:
	if (($file = cache_get_data('ila_mod_version', 86400)) == null)
	{
		$file = file_get_contents('http://www.xptsp.com/tools/mod_version.php?url=Custom_BBCodes_Manager');
		cache_put_data('ila_mod_version', $file, 86400);
	}
	if (preg_match('#Custom_BBCodes_Manager_v(.+?)\.zip#i', $file, $version))
	{
		if (isset($modSettings['ila_version']) && $version[1] > $modSettings['ila_version'])
			$context['settings_message'] = '<strong>' . sprintf($txt['bbc_no_update'], $version[1]) . '</strong>';
		else
			$context['settings_message'] = '<strong>' . $txt['bbc_no_update'] . '</strong>';
	}

	// Build the array required for "createList" function:
	$list_options = array(
		'id' => 'list_bbc',
		'title' => $txt['CustomBBCode_List_Title'],
		'items_per_page' => 30,
		'base_href' => $scripturl . '?action=admin;area=' . $_GET['area'] . ';sa=custombbc',
		'default_sort_col' => 'tag',
		'get_items' => array(
			'function' => 'get_bbc_data',
		),
		'get_count' => array(
			'function' => 'get_bbc_count',
		),
		'no_items_label' => $txt['List_no_tags'],
		'columns' => array(
			'button' => array(
				'header' => array(
					'value' => $txt['List_button'],
				),
				'data' => array(
					'db' => 'button',
					'style' => 'width: 8%;',
				),
			),
			'tag' => array(
				'header' => array(
					'value' => $txt['List_tag'],
				),
				'data' => array(
					'db' => 'tag',
					'style' => 'width: 20%;',
				),
				'sort' =>  array(
					'default' => 'tag',
					'reverse' => 'tag DESC',
				),
			),
			'ctype' => array(
				'header' => array(
					'value' => $txt['Edit_type'],
				),
				'data' => array(
					'db' => 'ctype',
					'style' => 'width: 20%;',
				),
				'sort' =>  array(
					'default' => 'ctype',
					'reverse' => 'ctype DESC',
				),
			),
			'form' => array(
				'header' => array(
					'value' => $txt['List_name'],
				),
				'data' => array(
					'db' => 'form',
					'style' => 'width: 54%;',
				),
			),
			'actions' => array(
				'header' => array(
					'value' => $txt['List_actions'],
				),
				'data' => array(
					'function' => 'get_bbc_actions',
					'style' => 'width: 20%; text-align: center;',
				),
			),
		),
	);

	// Let's build the list now:
	$context['page_title'] = $txt['CustomBBCode_List_Title'];
	$context[$context['admin_menu_name']]['tab_data']['tabs']['custombbc'] = array(
		'description' => $txt['List_Header_Desc'],
	);
	$context['sub_template'] = 'CustomBBCode_Browse';
	require_once($sourcedir . '/Subs-List.php');
	createList($list_options);
}

function CustomBBCodes_Edit($tag)
{
	global $txt, $scripturl, $context, $smcFunc, $settings;

	// Display the template with the information about the requested tag .
	checkSession('request');
	isAllowedTo('admin_forum');
	$context['sub_template'] = 'CustomBBCode_Edit';
	$context['page_title'] = $txt['Edit_title'];
	$context['post_url'] = $scripturl . '?action=admin;area=' . $_GET['area'] . ';sa=custombbc;edit=' . $tag . ';' . $context['session_var'] . '=' . $context['session_id'];

	// If new tag, define the fields.  Otherwise, get the tag from the database:
	if ($tag != -1)
		$row = get_bbc_row($tag);
	else
		$row = array(
			'id' => -1,
			'enabled' => 1,
			'button' => 0,
			'tag' => '',
			'description' => '',
			'block_level' => 0,
			'trim' => 'none',
			'ctype' => 'parsed_content',
			'before' => '',
			'after' => '',
			'content' => '',
		);

	// Uploading button?
	if (isset($_GET['upload']))
	{
		checkSession();
		if (isset($_POST['remove']))
		{
			// Removing button?
			remove_image_from_themes($row['tag']);
			update_bbc_tag($tag, array(
				'button' => 0,
			));
		}
		else
		{
			// Not removing button...  Must be adding one.
			copy_image_to_themes($row['tag']);
			update_bbc_tag($tag, array(
				'button' => 1,
			));
		}
		redirectexit('action=admin;area=' . $_GET['area'] . ';sa=custombbc;tag=' . $row['id']);
	}

	// Saving?
	if (isset($_GET['save']))
	{
		checkSession();

		// Populate the data fields with information supplied:
		$row = array(
			'id' => ($tag == -1 ? get_max_bbcode_id() + 1 : $tag),
			'content' => '',
			'before' => '',
			'after' => '',
			'enabled' => (isset($row['enabled']) ? (int) $row['enabled'] : 1),
			'tag' => (isset($_POST['tag']) ? $_POST['tag'] : ''),
			'block_level' => (isset($_POST['block']) ? 1 : 0),
			'trim' => (isset($_POST['cb_trim']) ? $_POST['cb_trim'] : 'none'),
			'ctype' => (isset($_POST['cb_type']) ? $_POST['cb_type'] : 'parsed_content'),
			'button' => (isset($_POST['button']) ? $_POST['button'] : 0),
			'description' => (isset($_POST['description']) ? $_POST['description'] : ''),
		);

		// Make sure that the bbcode doesn't exist.  If it does, error out....
		if (bbcode_exists($row['tag'], $row['id']))
			fatal_lang_error('bbcode_exists', false);

		// Break the given HTML string into a more understandable form for SMF....
		$text = isset($_POST['html']) ? $_POST['html'] : '';
		switch($row['ctype'])
		{
			case 'closed':
				break;

			case 'unparsed_content':
				$row['content'] = str_replace('$1', '{content}', $text);
				break;

			case 'unparsed_commas_content':
			case 'unparsed_equals_content':
				$search = array('{content}', '{option1}', '{option2}', '{option3}', '{option4}', '{option5}', '{option6}', '{option7}', '{option8}');
				$replace = array('$1', '$2', '$3', '$4', '$5', '$6', '$7', '$8', '$9');
				$row['content'] = str_replace($search, $replace, $text);
				break;

			case 'parsed_equals':
			case 'parsed_content':
			case 'unparsed_equals':
			case 'unparsed_commas':
			default:
				$search = array('{option1}', '{option2}', '{option3}', '{option4}', '{option5}', '{option6}', '{option7}', '{option8}');
				$replace = array('$1', '$2', '$3', '$4', '$5', '$6', '$7', '$8');
				$pos = strpos($text, '{content}');
				if ($pos === false)
					$row['before'] = str_replace($search, $replace, $text);
				else
				{
					$row['before'] = str_replace($search, $replace, substr($text, 0, $pos));
					$row['after'] = str_replace($search, $replace, substr($text, $pos + 9));
				}
				break;
		}

		// Insert the information, then return to the CustomBBCodes listing page:
		replace_tag($row);
		redirectexit('action=admin;area=' . $_GET['area'] . ';sa=custombbc');
	}

	// Let's put the entire HTML replacement code back together so the user understands it better:
	switch($row['ctype'])
	{
		case 'closed':
			$row['html'] = $row['content'];
			break;

		case 'unparsed_content':
			 $row['html'] = str_replace('$1', '{content}', $row['content']);
			break;

		case 'unparsed_commas_content':
		case 'unparsed_equals_content':
			$search = array('$1', '$2', '$3', '$4', '$5', '$6', '$7', '$8', '$9');
			$replace = array('{content}', '{option1}', '{option2}', '{option3}', '{option4}', '{option5}', '{option6}', '{option7}', '{option8}');
			$row['html'] = str_replace($search, $replace, $row['content']);
			break;

		case 'parsed_equals':
		case 'unparsed_equals':
		case 'parsed_content':
		case 'unparsed_commas':
		default:
			$search = array('$1', '$2', '$3', '$4', '$5', '$6', '$7', '$8');
			$replace = array('{option1}', '{option2}', '{option3}', '{option4}', '{option5}', '{option6}', '{option7}', '{option8}');
			$row['html'] = str_replace($search, $replace, $row['before']) . '{content}' . str_replace($search, $replace, $row['after']);
			break;
	}

	// Let's make the BROWSER output this HTML string for the user correctly!
	$row['html'] = htmlentities($row['html']);

	// Let's get the path to the button image used by the editor:
	$row['image'] = $settings['images_url'] . '/bbc/' . $row['tag'] . '.gif';
	$row['url_exists'] = file_exists($settings['theme_dir'] . '/images/bbc/' . $row['tag'] . '.gif');

	// Store the row in the context variable:
	$context['this_BBC'] = $row;
}

?>