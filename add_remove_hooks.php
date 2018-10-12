<?php
/**********************************************************************************
* add_remove_hooks.php                                                            *
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
if (SMF == 'SSI')
	db_extend('packages');
	
// Define the hooks
$hook_functions = array(
// SMF 2.x hooks:
	'integrate_pre_include' => '$sourcedir/Subs-CustomBBCodes.php',
	'integrate_bbc_codes' => 'CustomBBCodes_BBCodes',
	'integrate_bbc_buttons' => 'CustomBBCodes_Buttons',
	'integrate_load_permissions' => 'CustomBBCodes_Permissions',
	'integrate_load_theme' => 'CustomBBCodes_LoadTheme',
// SMF 2.x Admin hooks:
	'integrate_admin_include' => '$sourcedir/CustomBBCodesAdmin.php',
	'integrate_admin_areas' => 'CustomBBCodes_Admin',
// SMF 2.1 Admin hooks:
	'integrate_modify_features' => 'CustomBBCodes_Modify_Features',
);

// Adding or removing them?
if (!empty($context['uninstalling']))
	$call = 'remove_integration_function';
else
	$call = 'add_integration_function';

// Do the deed
foreach ($hook_functions as $hook => $function)
	$call($hook, $function);

if (SMF == 'SSI')
   echo 'Congratulations! You have successfully installed this mod!';

?>