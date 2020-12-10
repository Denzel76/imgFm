<?php
/*
  Copyright (c) 2020, Denzel
  This work is licensed under a 
  Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License.
  You should have received a copy of the license along with this work.
  If not, see <http://creativecommons.org/licenses/by-nc-nd/4.0/>.
*/

class hook_admin_categories_imagesFilemanager {

  var $version = '2.0.0';
  var $fileManagerPath = '../ext/filemanager/dialog.php?type=0&relative_url=1';

  public function listen_insertCategoryUpdateCategoryAction() {
    global $categories_id;
      
// Update categories image
    tep_db_query("update categories set categories_image = '" . tep_db_input($_POST['fm_categories_image']) . "' where categories_id = '" . (int)$categories_id . "'");
  }

  public function listen_productActionSave() {
      global $products_id;

// Update products image
      $products_image = tep_db_prepare_input($_POST['fm_products_image']);
      if (tep_not_null($products_image)){
        tep_db_query("update products set products_image = '" . $products_image . "' where products_id = '" . (int)$products_id . "'");
      }

// Update existing large product images
    tep_db_query("delete from products_images where products_id = '" . (int)$products_id . "'");
    $pi_sort_order = 0;

    foreach ($_POST as $key => $value) {
      if (preg_match('/^fm_products_image_large_([0-9]+)$/', $key, $matches) && tep_not_null($value)) {
        $pi_sort_order++;
        $sql_data_array = ['products_id' => (int)$products_id,
                           'htmlcontent' => tep_db_prepare_input($_POST['fm_products_image_htmlcontent_' . $matches[1]]),
                           'image' => tep_db_prepare_input($value),
                           'sort_order' => $pi_sort_order];
        tep_db_perform('products_images', $sql_data_array);
      }
    }
  }

  public function listen_productTab() {
    global $pInfo;

    $this->load_lang();
    
    $tab_title = addslashes(SECTION_HEADING_IMAGES_FILEMANAGER);
    $tab_link  = '#section_imagesFilemanager_content';
    
    $textMainImage = TEXT_PRODUCTS_MAIN_IMAGE;
    $textChooseFile = TEXT_CHOOSE_FILE;
    $textProductsLargeImage = TEXT_PRODUCTS_LARGE_IMAGE;
    $imgLargeImage = TEXT_PRODUCTS_OTHER_IMAGES;
    $imgLargeImageHTML = TEXT_PRODUCTS_LARGE_IMAGE_HTML_CONTENT;
    $addLargeImage = TEXT_PRODUCTS_ADD_LARGE_IMAGE;
    $removeLargeImages = TEXT_PRODUCTS_REMOVE_LARGE_IMAGES;
    $imagesFilemanagerNotes = TEXT_IMAGES_FILEMANAGER_NOTES;
    $imagesFilemanagerNotesButton = TEXT_IMAGES_FILEMANAGER_NOTES_BUTTON;

    $imgLargeImages = $orphimg = $delOrphImgButton = '';

    $fmMainImage = tep_info_image($pInfo->products_image, $pInfo->products_name) . '</div><div class="col"><div class="input-group">' . tep_draw_input_field('fm_products_image', $pInfo->products_image, 'class="form-control" placeholder="' . TEXT_CHOOSE_FILE . '" id="products_image" required aria-required="true" onclick="openFilemanager(\'products_image\');"') . '<div class="input-group-append"><button data-toggle="modal" data-target="#filemanagerModal" class="btn btn-success" type="button" onclick="openFilemanager(\'products_image\');"><i class="fas fa-eject"></i></button></div></div><small class="form-text mb-2 text-muted">' . TEXT_PRODUCTS_MAIN_IMAGE . '</small>';
    $mainImageDummy = tep_draw_input_field('products_image', '', null, 'file', null, 'class="d-none"');
    $fmMultiImage = tep_draw_hidden_field('products_multiple_images_new', '', 'id="products_multiple_images_new"');
    $fmMultiImageButton = '<button data-toggle="modal" data-target="#filemanagerModal" class="btn btn-primary btn-sm mt-2" type="button" onclick="$(\'#filemanagerIframe\').attr(\'src\', \'' . $this->fileManagerPath . '\' + \'&field_id=products_multiple_images_new&multiple=1\');">' . TEXT_PRODUCTS_ADD_MULTI_LARGE_IMAGES . '</button>';

    $pi_counter = 0;
    foreach ($pInfo->products_larger_images as $pi) {
      $pi_counter++;
      if (file_exists(DIR_FS_CATALOG_IMAGES . $pi['image'])) {
        $imgLargeImages .= tep_draw_input_field('products_image_large_' . $pi['id'], $pi['image'], 'class="d-none"' . $pi['id'] . '"', 'file') . tep_draw_hidden_field('products_image_htmlcontent_' . $pi['id']) . '<div class="row mr-0 mb-2" id="piId' . $pi_counter . '"><div class="col-1 text-right"><span class="font-weight-bold h5">' . $pi_counter . '. </span><i class="fas fa-arrows-alt-v mr-2"></i><a href="#" onclick="trashPiForm(' . $pi_counter . '); return false;"><i class="fas fa-trash text-danger"></i></a></div><div class="col-2" id="image_large_' . $pi['id'] . '">' . tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'images/' . $pi['image'], $pInfo->products_name, "", "", "", TRUE, "rounded mb-2") . '</div><div class="col"><div class="input-group mb-2">' . tep_draw_input_field('fm_products_image_large_' . $pi['id'], $pi['image'], 'class="form-control" placeholder="' . TEXT_CHOOSE_FILE . '" id="products_image_large_' . $pi['id'] . '" onclick="openFilemanager(\'products_image_large_' . $pi['id'] . '\');"') . '<div class="input-group-append"><button data-toggle="modal" data-target="#filemanagerModal" class="btn btn-success" type="button" onclick="openFilemanager(\'products_image_large_' . $pi['id'] . '\');"><i class="fas fa-eject"></i></button></div></div><small class="form-text mb-2 text-muted">' . TEXT_PRODUCTS_LARGE_IMAGE . '</small>' . tep_draw_textarea_field('fm_products_image_htmlcontent_' . $pi['id'], 'soft', '70', '3', $pi['htmlcontent']) . '<small class="form-text text-muted">' . TEXT_PRODUCTS_LARGE_IMAGE_HTML_CONTENT . '</small></div></div>';
      } else {
        $orphimg = true;
        $imgLargeImages .= tep_draw_input_field('products_image_large_' . $pi['id'], $pi['image'], 'class="d-none"' . $pi['id'] . '"', 'file') . tep_draw_hidden_field('products_image_htmlcontent_' . $pi['id']) . '<div class="row mr-0 mb-2" id="piIdOrph' . $pi_counter . '"><div class="col-1 text-right"><span class="font-weight-bold h5">' . $pi_counter . '. </span><i class="fas fa-arrows-alt-v mr-2"></i><a href="#" onclick="trashPiForm(' . $pi_counter . '); return false;"><i class="fas fa-trash text-danger"></i></a></div><div class="col"><div class="input-group mb-2">' . tep_draw_input_field('fm_products_image_large_' . $pi['id'], $pi['image'], 'class="form-control is-invalid" placeholder="' . TEXT_CHOOSE_FILE . '" id="products_image_large_' . $pi['id'] . '"  onclick="openFilemanager(\'products_image_large_' . $pi['id'] . '\');"') . '<div class="input-group-append"><button data-toggle="modal" data-target="#filemanagerModal" class="btn btn-success" type="button" onclick="openFilemanager(\'products_image_large_' . $pi['id'] . '\');"><i class="fas fa-eject"></i></button></div><div class="invalid-feedback">' . TEXT_ERROR_FILE_NOT_FOUND . '</div></div>' . tep_draw_textarea_field('fm_products_image_htmlcontent_' . $pi['id'], 'soft', '70', '3', $pi['htmlcontent']) . '<small class="form-text text-muted">' . TEXT_PRODUCTS_LARGE_IMAGE_HTML_CONTENT . '</small></div></div>';
      }
    }
    
    if ($orphimg) {
      $delOrphImgButton = '<br><button type="button" class="btn btn-warning btn-sm text-white mt-2" onclick="remOrphPiForm();return false;">' . TEXT_REMOVE_ORPHANED_DB_ENTRIES . '</button>';
    }
    
    $output = <<<EOD
<div class="tab-pane fade" id="section_imagesFilemanager_content" role="tabpanel">
    <div class="mb-3">
        <div class="form-group row">
            <label for="pImg" class="col-form-label col-sm-3 text-left text-sm-right">{$textMainImage}</label>
            <div class="col-sm-9">
                <div class="row mb-2">
                    <div class="col-2 text-center" id="image">
                        {$fmMainImage}
                        {$mainImageDummy}
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="form-group row">
            <div class="col-sm-3 text-left text-sm-right">
                {$imgLargeImage}
                <br><a class="btn btn-info btn-sm text-white mt-2" role="button" href="#" id="add_image" onclick="addNewPiForm();return false;">{$addLargeImage}</a>
                <br>{$fmMultiImageButton}
                {$delOrphImgButton}
                <br><button type="button" class="btn btn-danger btn-sm text-white mt-2" onclick="remPiForm();return false;">{$removeLargeImages}</button>
            </div>
            <div class="col-sm-9" id="piList">
                {$fmMultiImage}
                {$imgLargeImages}
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3 text-left text-sm-right">
                {$imagesFilemanagerNotesButton}
            </div>
            <div class="col-sm-9">
                {$imagesFilemanagerNotes}
            </div>
        </div>
    </div>
</div>
<style type="text/css">
    #piList { list-style-type: none; margin: 0; padding: 0; }
    #piList li { margin: 5px 0; padding: 2px; }
</style>
<script>
    var piSize = {$pi_counter};
    function addNewPiForm() {
        piSize++;
        $('#piList').append('<div class="row mr-0 mb-2" id="piId' + piSize + '"><div class="col-1 text-right"><span class="font-weight-bold h5">' + piSize + '. </span><i class="fas fa-arrows-alt-v mr-2"></i><a href="#" onclick="trashPiForm(' + piSize + '); return false;"><i class="fas fa-trash text-danger"></i></a></div><div class="col-2 d-none" id="image_large_new_' + piSize + '"></div><div class="col"><div class="input-group"><input type="text" class="form-control" id="products_image_large_new_' + piSize + '" name="fm_products_image_large_' + piSize + '" placeholder="{$textChooseFile}" onClick="openFilemanager(products_image_large_new_' + piSize + ');"><div class="input-group-append"><button data-toggle="modal" data-target="#filemanagerModal" class="btn btn-success" type="button" onclick="openFilemanager(\'products_image_large_new_' + piSize + '\');"><i class="fas fa-eject"></i></button></div></div><small class="form-text mb-2 text-muted">{$textProductsLargeImage}</small><textarea name="fm_products_image_htmlcontent_' + piSize + '" wrap="soft" class="form-control" cols="70" rows="3"></textarea><small class="form-text text-muted">{$imgLargeImageHTML}</small></div></div>');
        openFilemanager('products_image_large_new_' + piSize);
    }
    function addMultiNewPiForms(n) {
        piSize++;
        $('#piList').append('<div class="row mr-0 mb-2" id="piId' + piSize + '"><div class="col-1 text-right"><span class="font-weight-bold h5">' + piSize + '. </span><i class="fas fa-arrows-alt-v mr-2"></i><a href="#" onclick="trashPiForm(' + piSize + '); return false;"><i class="fas fa-trash text-danger"></i></a></div><div class="col-2" id="image_large_new_' + piSize + '"><img src="../images/' + n + '" class="img-fluid"></div><div class="col"><div class="input-group"><input type="text" class="form-control" id="products_image_large_new_' + piSize + '" name="fm_products_image_large_' + piSize + '" value="' + n + '" placeholder="{$textChooseFile}"  onClick="openFilemanager(products_image_large_new_' + piSize + ');"><div class="input-group-append"><button data-toggle="modal" data-target="#filemanagerModal" class="btn btn-success" type="button" onclick="openFilemanager(\'products_image_large_new_' + piSize + '\');"><i class="fas fa-eject"></i></button></div></div><small class="form-text mb-2 text-muted">{$textProductsLargeImage}</small><textarea name="fm_products_image_htmlcontent_' + piSize + '" wrap="soft" class="form-control" cols="70" rows="3"></textarea><small class="form-text text-muted">{$imgLargeImageHTML}</small></div></div>');
    }
    function remPiForm() {
        $('div[id^="piId"').effect('blind').remove();
        piSize = 0;
    }
    function remOrphPiForm() {
        $('div[id^="piIdOrph"').effect('blind').remove();
    }
    function trashPiForm(p){
        $('#piId' + p).effect('blind').remove();
    }
    $(function() { 
        $('#productTabs ul.nav.nav-tabs').append('<li class="nav-item"><a class="nav-link" data-toggle="tab" href="{$tab_link}" role="tab">{$tab_title}</a></li>'); 
        $('#section_images_content').remove(); 
        $('a[href$="#section_images_content"]').parent().remove(); 
        $('#piList').sortable({
            containment: 'parent'
        });
        $('#products_multiple_images_new').on('change', function() {
            i = 0;
            var filenames = $(this).val().slice(1,-1).split(',');
            $.each(filenames, function (){
                addMultiNewPiForms(filenames[i].slice(1,-1));
                i++;
            });
            $('[id^="products_image_large_new_"]').change();
         });
    });
</script>
EOD;

    return $output;
    }

  public function listen_infoBox($parameters) {
    global $action, $pInfo, $cInfo, $cPath, $box;
    
    $heading =& $parameters['heading'];
    $contents =& $parameters['contents'];
    
      if ($action == 'edit_category')  {
        
        $contents = array();
        
        $contents = ['form' => tep_draw_form('categories', 'categories.php', 'action=update_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"') . tep_draw_hidden_field('categories_id', $cInfo->categories_id)];
        $contents[] = ['text' => TEXT_EDIT_INTRO];

        $category_inputs_string = $category_description_string = $category_seo_description_string = $category_seo_title_string = '';
        foreach (tep_get_languages() as $l) {
          $category_inputs_string .= '<div class="input-group mb-1">';
            $category_inputs_string .= '<div class="input-group-prepend">';
              $category_inputs_string .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name']) . '</span>';
            $category_inputs_string .= '</div>';
            $category_inputs_string .= tep_draw_input_field('categories_name[' . $l['id'] . ']', tep_get_category_name($cInfo->categories_id, $l['id']), 'required aria-required="true"');
          $category_inputs_string .= '</div>';
          $category_seo_title_string .= '<div class="input-group mb-1">';
            $category_seo_title_string .= '<div class="input-group-prepend">';
              $category_seo_title_string .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name']) . '</span>';
            $category_seo_title_string .= '</div>';
            $category_seo_title_string .= tep_draw_input_field('categories_seo_title[' . $l['id'] . ']', tep_get_category_seo_title($cInfo->categories_id, $l['id']));
          $category_seo_title_string .= '</div>';
         $category_description_string .= '<div class="input-group mb-1">';
            $category_description_string .= '<div class="input-group-prepend">';
              $category_description_string .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name']) . '</span>';
            $category_description_string .= '</div>';
            $category_description_string .= tep_draw_textarea_field('categories_description[' . $l['id'] . ']', 'soft', '80', '10', tep_get_category_description($cInfo->categories_id, $l['id']));
          $category_description_string .= '</div>';
          $category_seo_description_string .= '<div class="input-group mb-1">';
            $category_seo_description_string .= '<div class="input-group-prepend">';
              $category_seo_description_string .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name']) . '</span>';
            $category_seo_description_string .= '</div>';
            $category_seo_description_string .= tep_draw_textarea_field('categories_seo_description[' . $l['id'] . ']', 'soft', '80', '10', tep_get_category_seo_description($cInfo->categories_id, $l['id']));
          $category_seo_description_string .= '</div>';
        }

        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_NAME . $category_inputs_string];
        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_SEO_TITLE . $category_seo_title_string];
        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_DESCRIPTION . $category_description_string];
        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_SEO_DESCRIPTION . $category_seo_description_string];
        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_IMAGE . '<br>' . tep_info_image($cInfo->categories_image, $cInfo->categories_name)];
        $contents[] = ['text' => '<div class="input-group">' . tep_draw_input_field('categories_image', $cInfo->categories_image, 'class="d-none"', 'file') . tep_draw_input_field('fm_categories_image', $cInfo->categories_image, 'class="form-control" placeholder="' . TEXT_EDIT_CATEGORIES_IMAGE . '" id="categories_image" readonly') . '<div class="input-group-append"><button class="btn btn-danger" type="button" onclick="$(\'#categories_image\').val(\'\');$(name=\'categories_image\').val(\'\');$(\'#s_image\').html(\'' . TEXT_EDIT_CATEGORIES_IMAGE . '\');"><i class="fas fa-times"></i></button><button data-toggle="modal" data-target="#filemanagerModal" class="btn btn-success" type="button" onclick="openFilemanager(\'categories_image\');"><i class="fas fa-eject"></i></button></div></div>'];
        $contents[] = ['text' => TEXT_EDIT_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', $cInfo->sort_order, 'size="2"')];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success btn-block btn-lg mb-1') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times',  tep_href_link('categories.php', 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id), null, null, 'btn-light')];
      }
  }

  function listen_injectSiteEnd() {
      
    $textCategoriesImage = TEXT_EDIT_CATEGORIES_IMAGE;

    $output = <<<EOD
<div class="modal fade" id="filemanagerModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Filemanager</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>
      <div class="modal-body" style="padding:0px; margin:0px;">
        <iframe width="100%" height="400" src="" id="filemanagerIframe" frameborder="0" style="overflow: scroll; overflow-x: hidden; overflow-y: scroll;"></iframe>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
function setIframeHeight() {
  var iFrameID = document.getElementById('filemanagerIframe');
  if(iFrameID) {
    iFrameID.height = "";
    iFrameID.height = (window.innerHeight * 0.8)  + "px";
  }
}
function responsive_filemanager_callback(field_id){
    $('#'+field_id).change();
    var url=$('#'+field_id).val();
    $('#'+field_id.substr(9)).html(((field_id.substr(9) == "s_image")?'{$textCategoriesImage}<br>':'') + '<img src="../images/' + url + '" class="img-fluid">').removeClass('d-none');
}
function openFilemanager(inputID) {
    $('#filemanagerIframe').attr('src', '{$this->fileManagerPath}&field_id=' + inputID + '&multiple=0');
    setIframeHeight();
    $('#filemanagerModal').modal('show');
}
$('#categories_image').closest('tr').prev().children('td').attr('id', 's_image');
</script>
EOD;

    return $output;
  }
  
 function load_lang() {
    global $language;

    require(DIR_FS_CATALOG . 'includes/languages/' . $language . '/hooks/admin/categories/imagesFilemanager.php');
    }

}
