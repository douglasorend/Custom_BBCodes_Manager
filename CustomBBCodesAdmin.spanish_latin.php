<?php
/**********************************************************************************
* CustomBBCodeAdmin.english.php - Custom BBCode Manager mod english language file
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:
*	http://opensource.org/licenses/BSD-2-Clause
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE.
**********************************************************************************/

$txt['CustomBBCode_List_Title'] = 'BBCodes personalizados';

// All strings used in the List Custom BBCodes template:
$txt['List_Header_Desc'] = 'Aqu&iacute; puede gestionar todos sus BBCodes personalizados.';
$txt['List_no_tags'] = 'No hay etiquetas definidas';
$txt['List_name'] = 'BBCode usados';
$txt['List_tag'] = 'Nombre de la etiqueta';
$txt['List_description'] = 'Descripci&oacute;n de la etiqueta para el bot&oacute;n';
$txt['List_button'] = 'Bot&oacute;n';	
$txt['List_modify'] = 'Modificar';
$txt['List_delete'] = 'Borrar';
$txt['List_enable'] = 'Habilitar';
$txt['List_disable'] = 'Inhabilitar';
$txt['List_actions'] = 'Comportamiento';
$txt['List_add'] = 'Crear nueva etiqueta';
$txt['List_delete_bbcode'] = '¿Est&aacute; seguro de que desea eliminar este c&oacute;digo BBCode?';
$txt['List_simple'] = 'Crear etiqueta simple';
$txt['List_Status'] = 'Estado';

// All strings used in the Edit Custom BBCode template:
$txt['Edit_title'] = 'Ajustes BBCode personalizada';
$txt['Edit_name'] = 'Nombre de la etiqueta';
$txt['Edit_description'] = $txt['List_description'];
$txt['Edit_type'] = 'Tipo de etiqueta';
$txt['Edit_tag'] = 'Etiqueta';
$txt['Edit_tag_format'] = 'Formato BBCode que utilizar&aacute;:';
$txt['Edit_content'] = 'Contenido analizado';
$txt['Edit_uncontent'] = 'Sin analizar el contenido';
$txt['Edit_data'] = 'Contenido analizado';
$txt['Edit_trim'] = 'Recortar los espacios en blanco';
$txt['Edit_trim_no'] = '(Sin ajuste)';
$txt['Edit_trim_in'] = 'Dentro';
$txt['Edit_trim_out'] = 'Fuera de';
$txt['Edit_trim_both'] = 'Ambos';
$txt['Edit_block_level'] = 'Nivel del bloque';
$txt['Edit_html'] = 'El HTML que se utiliza para la etiqueta:';
$txt['Edit_test_bbcode'] = 'Prueba';
$txt['Edit_button'] = 'Bot&oacute;n de la etiqueta';
$txt['Edit_remove'] = 'Retirar el bot&oacute;n';
$txt['Edit_remove_confirm'] = '¿Est&aacute; seguro de que desea eliminar el bot&oacute;n para esta etiqueta?';
$txt['Edit_show_button'] = 'Bot&oacute;n para mostrar la etiqueta';
$txt['Edit_Upload_Title'] = 'Subir bot&oacute;n de la etiqueta';
$txt['Edit_20_Upload_description'] = 'Puede representar a su nueva BBCode personalizado con un bot&oacute;n en el Editor. Debe ser un archivo GIF que es de tama&ntilde;o 23x22, con un fondo transparente. Puede ser animado. El tama&ntilde;o m&aacute;ximo es de 10 kb.';
$txt['Edit_21_Upload_description'] = 'Puede representar a su nueva BBCode personalizado con un bot&oacute;n en el Editor. Debe ser un archivo PNG que es de tama&ntilde;o 16x16, con un fondo transparente. Puede ser animado. El tama&ntilde;o m&aacute;ximo es de 10 kb.';
$txt['Edit_Filename'] = 'Nombre del archivo:';
$txt['Edit_Upload'] = 'Subir';
$txt['Edit_test'] = 'M&aacute;scara de entrada para probar en contra:';
$txt['bbcode_exists'] = 'El BBCode est&aacute; intentando insertar ya existe.';
$txt['upload_file'] = 'Subir';
$txt['remove_file'] = 'Borrar';
$txt['clear_file'] = 'Limpiar';
$txt['Edit_Preview'] = 'Vista previa de bot&oacute;n BBCode';
$txt['Edit_accept_urls'] = '¿Asumir que <strong>{content}</strong> es un URL?';

// DO NOT change the {content} or {option} text, as they are required by the mod!
$txt['Edit_subtext0'] = '{content} se sustituye con el contenido de las etiquetas.';
$txt['Edit_subtext1'] = '{content} (o $1) se sustituye con el contenido de las etiquetas.<br />{option1} (o $2) se sustituyen por 1&ordf; opci&oacute;n.<br/>{option2} (o $3), {option3} (o $4), para m&aacute;s opciones etc.';
$txt['Edit_subtext2'] = '{option1} (o $1) se sustituyen por 1&ordf; opci&oacute;n.<br/>{option2} (o $2), {option3} (o $3), para m&aacute;s opciones etc.';

// Description of the bbcode types....
$txt['parsed_content'] = 'Analizada contenido';
$txt['unparsed_equals'] = 'Iguales no analizadas';
$txt['parsed_equals'] = 'Iguales Analizada';
$txt['unparsed_content'] = 'Contenido no analizado';
$txt['closed'] = 'Cerrado';
$txt['unparsed_commas'] = 'Las comas no analizado';
$txt['unparsed_commas_content'] = 'Las comas no analizado, contenido';
$txt['unparsed_equals_content'] = 'No analizado es igual a contenido';

// Version update information:
$txt['bbc_new_version'] = 'Administrador de BBCode personalizados tiene una nueva versi&oacute;n %s que est&aacute; disponible para su descarga!';
$txt['bbc_no_update'] = 'Su instalaci&oacute;n del administrador de BBCode personalizados esta actualizado!';

// Upload error messages:
$txt['Edit_no_file_error'] = 'Ning&uacute;n archivo especificado para cargar!';
$txt['Edit_upload_error'] = 'Se ha producido un error durante la carga del archivo.';
$txt['Edit_extension_error'] = 'Su bot&oacute;n fue rechazado porque no ten&iacute;a una %s extensi&oacute;n.';
$txt['Edit_mime_type_error'] = 'Su bot&oacute;n fue rechazada porque se detect&oacute; mal tipo mime.';
$txt['Edit_file_size_error'] = 'Su bot&oacute;n fue rechazado porque era m&aacute;s de 10kb.';
$txt['Edit_width_error'] = 'Su bot&oacute;n fue rechazada porque no es %d p&iacute;xeles de ancho.';
$txt['Edit_height_error'] = 'Su bot&oacute;n fue rechazada porque no es %d p&iacute;xeles de altura.';
$txt['Edit_single_content_error'] = 'Usted tiene m&aacute;s de una {content} marcador en su reemplazo HTML.\nEl analizador &uacute;nica sustituir&aacute; a la {content} marcador de una vez, debido a la forma en que se construy&oacute; el analizador.\n\n¿Continuar?';
$txt['Edit_no_content_error'] = 'Usted tiene por lo menos una {content} marcador en su reemplazo HTML.\nEl analizador no reemplazar&aacute; a la {content} marcador, debido a la forma en que se construy&oacute; el analizador.\n\n¿Continuar?';

// Permission Names:
//$txt['permissionname_manage_custom_bbcodes'] = 'Can Manage Custom BBCodes';

?>