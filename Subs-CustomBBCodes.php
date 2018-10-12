<?php
/**********************************************************************************
* Subs-CustomBBCode.php - Subs of the Custom BBCode Manager mod
*********************************************************************************
* This program is distributed in the hope that it is and will be useful, but
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE,
**********************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');

/**********************************************************************************
* CustomBBCode hooks
**********************************************************************************/
function CustomBBCodes_Admin(&$admin_areas)
{
	global $txt, $sourcedir;

	if (!isset($admin_areas['config']['areas']['featuresettings']['subsections']['bbc']))
		$admin_areas['layout']['areas']['postsettings']['subsections']['custombbc'] = array($txt['CustomBBCode_List_Title']);
	else
	{
		$rebuild = array();
		foreach ($admin_areas['config']['areas']['featuresettings']['subsections'] as $id => $area)
		{
			$rebuild[$id] = $area;
			if ($id == 'bbc')
				$rebuild['custombbc'] = array($txt['CustomBBCode_List_Title']);
		}
		$admin_areas['config']['areas']['featuresettings']['subsections'] = $rebuild;
	}
	if ((isset($_REQUEST['area']) && ($_REQUEST['area'] == 'featuresettings' || $_REQUEST['area'] == 'postsettings')) && (isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'custombbc'))
		require_once($sourcedir . '/CustomBBCodes.php');
}

function Add_CustomBBCCodes(&$subActions)
{
	$subActions['custombbc'] = 'CustomBBCodes_Browse';
}

function CustomBBCodes_BBCodes(&$codes)
{
	global $smcFunc;

	if (($bbcodes = cache_get_data('bbcodes_custom', 86400)) == null)
	{
		// Build the subforum tree array:
		$request = $smcFunc['db_query']('', '
			SELECT *
			FROM {db_prefix}bbcodes
			WHERE enabled = {int:enabled}',
			array(
				'enabled' => 1
			)
		);
		$bbcodes = array();
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			// Strip unnecessary fields from the resulting array:
			if ($row['ctype'] != 'parsed_content')
				$row['ctype'] = $row['ctype'];
			if ($row['trim'] == 'none')
				unset($row['trim']);
			$row['block_level'] = ($row['block_level'] ? true : false);
			if (!$row['block_level'])
				unset($row['block_level']);
			switch($row['ctype'])
			{
				case 'unparsed_content':
				case 'closed':
				case 'unparsed_commas_content':
				case 'unparsed_equals_content':
					unset($row['before']);
					unset($row['after']);
					$row['content'] = stripslashes($row['content']);
					break;

				case 'parsed_equals':
				case 'unparsed_equals':
				case 'parsed_content':
				case 'unparsed_commas':
				default:
					unset($row['content']);
					$row['before'] = stripslashes($row['before']);
					$row['after'] = stripslashes($row['after']);
					break;
			}
			unset($row['ctype']);

			// Add the new BBCode to the array:
			$bbcodes[] = $row;
		}
		$smcFunc['db_free_result']($request);

		// Stuff this array into the cache for future use:
		cache_put_data('bbcodes_custom', $bbcodes, 86400);
	}
	$codes = array_merge($codes, $bbcodes);
}

function CustomBBCodes_Buttons(&$buttons)
{
	global $smcFunc;

	if (($cached = cache_get_data('bbcodes_buttons', 86400)) == null)
	{
		$request = $smcFunc['db_query']('', '
			SELECT tag, ctype, description
			FROM {db_prefix}bbcodes
			WHERE enabled = {int:enabled}
				AND button = {int:button}',
			array(
				'enabled' => 1,
				'button' => 1,
			)
		);

		// Build the Custom BBcode array:
		$cached = array();
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$tag = stripslashes($row['tag']);
			$tmp = array(
				'image' => $tag,
				'code' => $tag,
				'description' => stripslashes(empty($row['description']) ? $tag : $row['description']),
				'after' => ($row['ctype'] != 'closed' ? '[/' . $tag . ']' : ''),
			);

			switch($row['ctype'])
			{
				case 'unparsed_content':
				case 'closed':
				case 'unparsed_commas_content':
				case 'unparsed_equals_content':
					$tmp['before'] = '[' . $tag . ']';
					break;

				case 'parsed_equals':
				case 'unparsed_equals':
				case 'parsed_content':
				case 'unparsed_commas':
				default:
					$tmp['before'] = '[' . $tag . ']';
			}

			$cached[] = $tmp;
		}
		$smcFunc['db_free_result']($request);

		// Stuff this array into the cache for future use:
		cache_put_data('bbcodes_buttons', $cached, 86400);
	}
	$buttons[0] = array_merge($buttons[0], $cached);
}

?>