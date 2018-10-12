<?php
/**********************************************************************************
* CustomBBCode, template, php - Custom BBCode Manager mod template file
*********************************************************************************
* This program is distributed in the hope that it is and will be useful, but
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE,
**********************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');

function template_CustomBBCode_Browse()
{
	global $txt, $context, $scripturl;

	if (!empty($context['settings_message']))
		echo '
		<div class="information">', $context['settings_message'], '</div>';
	template_show_list('list_bbc');
	echo '
		<form action="', $scripturl, '?action=admin;area=', $_GET['area'], ';sa=custombbc;edit=-1;sesc=' , $context['session_id'] , '"" name="restore" method="post" accept-charset="', $context['character_set'], '">
			<div align="right"><input type="submit" class="button_submit" name="submit" value="', $txt['List_add'], '" /></div>
		</form>';
}

function template_CustomBBCode_Edit()
{
	global $txt, $context;

	$tag = $context['this_BBC'];
	echo '
		<div class="cat_bar">
			<h3 class="catbg">', $txt['Edit_title'], '</h3>
		</div>
		<div id="help_container">
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="content">
					<form action="', $context['post_url'], ';save" method="post" accept-charset="', $context['character_set'], '" name="custombbc">
						<dl class="settings">
							<dt>', $txt['Edit_name'], ':</dt>
							<dd><input type="text" name="tag" id="tag" value="', $tag['tag'], '" size="30" class="input_text" /></dd>

							<dt>', $txt['List_description'], ':</dt>
							<dd><input type="text" name="description" id="description" value="', $tag['description'], '" size="60" class="input_text" /></dd>
						</dl>

						<hr class="hrcolor clear" />
						<dl class="settings">
							<dt>', $txt['Edit_type'], ':</dt>
							<dd>
								<select name="cb_type" ONCHANGE="Process();">
									<option value="parsed_content" ', ($tag['ctype'] == 'parsed_content' ? ' selected="selected"' : ''), '>', $txt['parsed_content'], '</option>
									<option value="unparsed_equals" ', ($tag['ctype'] == 'unparsed_equals' ? ' selected="selected"' : ''), '>', $txt['unparsed_equals'], '</option>
									<option value="parsed_equals" ', ($tag['ctype'] == 'parsed_equals' ? ' selected="selected"' : ''), '>', $txt['parsed_equals'], '</option>
									<option value="unparsed_content" ', ($tag['ctype'] == 'unparsed_content' ? ' selected="selected"' : ''), '>', $txt['unparsed_content'], '</option>
									<option value="closed" ', ($tag['ctype'] == 'closed' ? ' selected="selected"' : ''), '>', $txt['closed'], '</option>
									<option value="unparsed_commas" ', ($tag['ctype'] == 'unparsed_commas' ? ' selected="selected"' : ''), '>', $txt['unparsed_commas'], '</option>
									<option value="unparsed_commas_content" ', ($tag['ctype'] == 'unparsed_commas_content' ? ' selected="selected"' : ''), '>', $txt['unparsed_commas_content'], '</option>
									<option value="unparsed_equals_content" ', ($tag['ctype'] == 'unparsed_equals_content' ? ' selected="selected"' : ''), '>', $txt['unparsed_equals_content'], '</option>
								</select>
							</dd>

							<dt>', $txt['List_tag'], ':</dt>
							<dd><input type="text" id="formDiv" value="" size="60" class="input_text" disabled="disabled" /></dd>

							<dt>', $txt['Edit_trim'], ':</dt>
							<dd>
								<select name="cb_trim">
									<option value="none" ', ($tag['trim'] == 'none' ? ' selected="selected"' : ''), '>', $txt['Edit_trim_no'], '</option>
									<option value="inside" ', ($tag['trim'] == 'inside' ? ' selected="selected"' : ''), '>', $txt['Edit_trim_in'], '</option>
									<option value="outside" ', ($tag['trim'] == 'outside' ? ' selected="selected"' : ''), '>', $txt['Edit_trim_out'], '</option>
									<option value="both" ', ($tag['trim'] == 'both' ? ' selected="selected"' : ''), '>', $txt['Edit_trim_both'], '</option>
								</select>
							</dd>

							<dt>', $txt['Edit_block_level'], ':</dt>
							<dd><input type="checkbox" name="block" id="block"', ($tag['block_level'] ? ' checked="checked"' : ''), ' value="1" class="input_check" /></dd>
						</dl>

						<hr class="hrcolor clear" />
						<dl class="settings">
							<dt>', $txt['Edit_text'], '<div class="smalltext" id="htmlDiv" style="visibility:visible">', $txt['Edit_test_bbcode'], '</div></dt>
							<dd><textarea name="html" id="html" style="height: 120px; width: 450px;">', $tag['html'], '</textarea></dd>
						</dl>';
						
	if ($tag['url_exists'])
		echo '
						<hr class="hrcolor clear" />
						<dl class="settings">
							<dt>', $txt['Edit_button'], ':</dt>
							<dd><img src="', $tag['image'], '" alt="" /></dd>

							<dt>', $txt['Edit_show_button'], ':</dt>
							<dd><input type="checkbox" name="button" id="button"', ($tag['button'] == 1 ? ' checked="checked"' : ''), ' value="1" class="input_check" /></dd>
						</dl>';

	// Finish the rest of this template:
	echo '

						<hr class="hrcolor clear" />
						<div align="right">
							<input type="submit" class="button_submit" name="submit" value="', $txt['attachment_manager_save'], '" />
						</div>
						<input type="hidden" name="sc" value="', $context['session_id'], '" />
					</form>
				</div>
				<span class="botslice"><span></span></span>
			</div>
		</div>

		<div class="cat_bar">
			<h3 class="catbg">', $txt['Edit_Upload_Title'], '</h3>
		</div>
		<div id="help_container">
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="content">
					<form action="', $context['post_url'], ';upload" method="post" enctype="multipart/form-data" accept-charset="', $context['character_set'], '">';
	echo '
						<p>', $txt['Edit_Upload_description'], '</p>
						<br class="brcolor clear" />
						<dl class="settings">
							<dt>', $txt['Edit_Filename'], '</dt>
							<dd><input type="file" name="file" id="file" size="40"></dd>
						</dl>
						<hr class="hrcolor clear" />
						<div align="right">
							<input type="submit" class="button_submit" name="submit" value="', $txt['Edit_Upload'], '" />';
	if ($tag['url_exists'])
		echo '
							<input type="submit" class="button_submit" name="remove" value="', $txt['Edit_remove'], '" onclick="return confirm(\'', $txt['Edit_remove_confirm'], '\');" />';
	echo '
						</div>
						<input type="hidden" name="sc" value="', $context['session_id'], '" />
					</form>
				</div>
				<span class="botslice"><span></span></span>
			</div>
		</div>

		<script type="text/javascript">
		<!--
		function Process()
		{
			switch(document.custombbc.cb_type.selectedIndex)
			{
			case 0: <!-- parsed_content -->
				document.getElementById("formDiv").value = "[', $txt['Edit_tag'], ']', $txt['Edit_content'], '[/', $txt['Edit_tag'], ']";
				document.getElementById("htmlDiv").innerHTML = "', $txt['Edit_subtext0'], '";
				break;

			case 1: <!-- unparsed_equals -->
				document.getElementById("formDiv").value = "[', $txt['Edit_tag'], '=xyz]', $txt['Edit_content'], '[/', $txt['Edit_tag'], ']";
				document.getElementById("htmlDiv").innerHTML = "', $txt['Edit_subtext2'], '";
				break;

			case 2: <!-- parsed_equals -->
				document.getElementById("formDiv").value = "[', $txt['Edit_tag'], '=', $txt['Edit_data'], ']', $txt['Edit_content'], '[/', $txt['Edit_tag'], ']";
				document.getElementById("htmlDiv").innerHTML = "', $txt['Edit_subtext2'], '";
				break;

			case 3: <!-- unparsed_content -->
				document.getElementById("formDiv").value = "[', $txt['Edit_tag'], ']', $txt['Edit_uncontent'], '[/', $txt['Edit_tag'], ']";
				document.getElementById("htmlDiv").innerHTML = "', $txt['Edit_subtext0'], '";
				break;

			case 4: <!-- closed -->
				document.getElementById("formDiv").value = "[', $txt['Edit_tag'], '], [', $txt['Edit_tag'], '/], [', $txt['Edit_tag'], ' /]";
				document.getElementById("htmlDiv").innerHTML = "";
				break;

			case 5: <!-- unparsed_commas -->
				document.getElementById("formDiv").value = "[', $txt['Edit_tag'], '=1,2,3]', $txt['Edit_content'], '[/', $txt['Edit_tag'], ']";
				document.getElementById("htmlDiv").innerHTML = "', $txt['Edit_subtext2'], '";
				break;

			case 6: <!-- unparsed_commas_content -->
				document.getElementById("formDiv").value = "[', $txt['Edit_tag'], '=1,2,3]', $txt['Edit_uncontent'], '[/', $txt['Edit_tag'], ']";
				document.getElementById("htmlDiv").innerHTML = "', $txt['Edit_subtext1'], '";
				break;

			case 7: <!-- unparsed_equals_content -->
				document.getElementById("formDiv").value = "[', $txt['Edit_tag'], '=...]', $txt['Edit_uncontent'], '[/', $txt['Edit_tag'], ']";
				document.getElementById("htmlDiv").innerHTML = "', $txt['Edit_subtext1'], '";
				break;
			}
		}
		window.onload = Process;
		//-->
		</script>';
}

/*
*/
?>