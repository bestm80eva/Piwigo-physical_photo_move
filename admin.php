<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2017 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
include_once(PPM_PATH.'include/functions.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | Basic checks                                                          |
// +-----------------------------------------------------------------------+

$_GET['image_id'] = $_GET['tab'];

check_input_parameter('image_id', $_GET, false, PATTERN_ID);

$admin_photo_base_url = get_root_url().'admin.php?page=photo-'.$_GET['image_id'];

// +-----------------------------------------------------------------------+
// | Process form                                                          |
// +-----------------------------------------------------------------------+

if (isset($_POST['move_photo']))
{

  $ppm_test_mode = ppm_check_test_mode();

  // check selected target category and act accordingly
  if (isset($_POST['cat_id']))
  {
    $target_cat = $_POST['cat_id'];
    ppm_move_item($target_cat, $_GET['image_id'], $ppm_test_mode);
  }
  else // no destination selected
  {
    array_push(
      $page['messages'],
      l10n('MSG_NO_DEST')
      );
  }

} 

// +-----------------------------------------------------------------------+
// | Tabs                                                                  |
// +-----------------------------------------------------------------------+

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

$page['tab'] = 'ppm';

$tabsheet = new tabsheet();
$tabsheet->set_id('photo');
$tabsheet->select('ppm');
$tabsheet->assign();

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(
  array(
    'plugin_admin_content' => dirname(__FILE__).'/admin.tpl'
    )
  );

// retrieve the id of the storage category of the current photo
$image_info = get_image_infos($_GET['image_id']);
$storage_cat_id = $image_info['storage_category_id'];

// retrieve the name of the storage category of the photo
$storage_cat_info = get_cat_info($storage_cat_id);

// populate the selection scroll with physical albums
ppm_list_physical_albums();

$template->assign(
  array(
    'TITLE' => render_element_name($image_info),
    'TN_SRC' => DerivativeImage::thumb_url($image_info),
    'current_path' => $image_info['path'],
    'storage_category' => $storage_cat_info['name'],
    )
  );

// +-----------------------------------------------------------------------+
// | sending html code                                                     |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
?>
