<?php
/*
  Copyright (c) 2020, Denzel
  This work is licensed under a 
  Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License.
  You should have received a copy of the license along with this work.
  If not, see <http://creativecommons.org/licenses/by-nc-nd/4.0/>.
*/

const PRODUCTS_IMAGES_FILEMANAGER_VERSION = "V2.00";
const SECTION_HEADING_IMAGES_FILEMANAGER = '<i class="fas fa-images fa-fw mr-1"></i>Product Images';
const TEXT_PRODUCTS_ADD_MULTI_LARGE_IMAGES = '<i class="fas fa-plus fa-fw mr-1"></i><i class="fas fa-plus fa-fw mr-1"></i>Add multiple Images';
const TEXT_PRODUCTS_REMOVE_LARGE_IMAGES = '<i class="fas fa-minus fa-fw mr-1"></i>Remove all Gallery Images';
const TEXT_REMOVE_ORPHANED_DB_ENTRIES  = '<i class="fas fa-minus fa-fw mr-1"></i>Remove orphaned DB-Entries';
const TEXT_ERROR_FILE_NOT_FOUND = 'Error: File not found.';
const TEXT_CHOOSE_FILE  = 'Choose file';
const TEXT_IMAGES_FILEMANAGER_NOTES_BUTTON = '<button class="btn btn-sm btn-warning" type="button" data-toggle="collapse" data-target="#imgFmInfo" aria-expanded="false" aria-controls="imgFmInfo">Information</button>';
const TEXT_IMAGES_FILEMANAGER_NOTES = '<div class="collapse" id="imgFmInfo"><div class="card bg-warning mb-3"><div class="card-header">Filemanager for Productimages Hook Informations:<small class="float-sm-right">' . PRODUCTS_IMAGES_FILEMANAGER_VERSION . '</small></div><div class="card card-body"><p class="card-text">This hook implements the Responsive Filemanager v9.14.0 made by Alberto Peripolli to the image handling. Please read the licence <a href="https://responsivefilemanager.com/index.php#download-section" target="_blank">here!</a><br><br>The Filemanager takes care of uploading, moving and deleting the images. Just click the inputfield or the ejectbutton to open it. Preset is images in images/ ;) This can be changed in ext/filemanager/config/config.php<br><br>With the "Add multiple Images" button you are able to choose multiple imagefiles at once. If orphaned database entries are found, a button appears to delete them.<br><br>Changes will be dispayed instantly, but you have to save your product to confirm the changes.</p></div></div></div>';
