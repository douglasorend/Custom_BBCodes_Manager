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
	require_once($sourcedir . '/CustomBBCodes.php');
	$admin_areas['layout']['areas']['postsettings']['subsections']['custombbc'] = array($txt['CustomBBCode_List_Title']);
}

function CustomBBCodes_BBCodes(&$codes)
{
	global $smcFunc, $settings;

	// Build the subforum tree array:
	$request = $smcFunc['db_query']('', '
		SELECT *
		FROM {db_prefix}bbcodes 
		WHERE enabled = {int:enabled}',
		array(
			'enabled' => 1
		)
	);
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		// Strip unnecessary fields from the resulting array:
		if ($row['ctype'] != 'parsed_content') 
			$row['type'] = $row['ctype'];
		if ($row['trim'] == 'none') 
			unset($row['trim']);
		$row['block_level'] = ($row['block_level'] ? true : false);
		if (!$row['block_level'])
			unset($row['block_level']);
		if (empty($row['test']))
			unset($row['test']);
		else
			$row['test'] = stripslashes($row['test']);
		switch($row['ctype'])
		{
			case 'unparsed_content':
			case 'closed':
			case 'unparsed_commas_content':
			case 'unparsed_equals_content':
				unset($row['before']);
				unset($row['after']);
				$row['content'] = stripslashes( $row['content'] ); 
				break;
				
			case 'parsed_equals':
			case 'unparsed_equals':
			case 'parsed_content':
			case 'unparsed_commas':
			default:
				unset($row['content']);
				$row['before'] = stripslashes( $row['before'] );
				$row['after'] = stripslashes( $row['after'] );
				break;
		}
		unset($row['ctype']);

		// Add the new BBCode to the array:
		$codes[] = $row;
		//if ($row['tag'] == 'float') {print_r($row); exit;}
	}
	$smcFunc['db_free_result']($request);
}

function CustomBBCodes_Buttons(&$codes)
{
	global $smcFunc;

	// Build the subforum tree array:
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
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$tag = $row['tag'];
		$codes[1][$tag]['code'] = $tag;
		$codes[1][$tag]['image'] = $tag;
		$codes[1][$tag]['description'] = (empty($row['description']) ? $tag : $row['description']);

		switch($row['type'])
		{
			case 'unparsed_content':
			case 'closed':
			case 'unparsed_commas_content':
			case 'unparsed_equals_content':
				$codes[1][$tag]['before'] = '[' . $tag . ']'; 
				break;
				
			case 'parsed_equals':
			case 'unparsed_equals':
			case 'parsed_content':
			case 'unparsed_commas':
			default:
				$codes[1][$tag]['before'] = '[' . $tag . '=]'; 
		}
		if ($row['type'] != 'closed')
			$codes[1][$tag]['after'] = '[/' . $tag . ']'; 
	}
	$smcFunc['db_free_result']($request);
}

?>