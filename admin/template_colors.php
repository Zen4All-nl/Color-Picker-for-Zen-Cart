<?php
/**
 * @package admin
 * @copyright Copyright 2003-2018 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: Zen4All
 */
require('includes/application_top.php');

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$cssArray = array();
$propertyArray = [
  [
    'id' => 'color',
    'text' => 'Text Color'
  ], [
    'id' => 'background-color',
    'text' => 'Background Color'
  ], [
    'id' => 'border-color',
    'text' => 'Border Color'
  ], [
    'id' => 'border-top-color',
    'text' => 'Top Border Color'
  ], [
    'id' => 'border-right-color',
    'text' => 'Right Border Color'
  ], [
    'id' => 'border-bottom-color',
    'text' => 'Bottom Border Color'
  ], [
    'id' => 'border-left-color',
    'text' => 'Left Border Color'
    ]];
if (zen_not_null($_GET['select_template'])) {
  $currentTemplate = $_GET['select_template'];
}
if (zen_not_null($_POST['select_template'])) {
  $currentTemplate = $_POST['select_template'];
}

switch ($action) {
  case 'set_template' :
    $cssArray = CssFileToArray($currentTemplate);
    break;
  case 'edit' :
    $file_writeable = true;
    if (!is_writeable(DIR_FS_CATALOG_TEMPLATES . $currentTemplate . '/css/' . 'stylesheet_colors.css')) {
      $file_writeable = false;
      $messageStack->reset();
      $messageStack->add(sprintf(ERROR_FILE_NOT_WRITEABLE, DIR_FS_CATALOG_TEMPLATES . $currentTemplate . '/css/' . 'stylesheet_colors.css'), 'error');
      echo $messageStack->output();
    } else {
      $cssArray = CssFileToArray($currentTemplate);
    }
    break;
  case 'save' :
    $cssPostArray = new objectInfo($_POST['css']);
    $cssFilePath = $_POST['file'];
    saveCssToFile($cssPostArray, $cssFilePath);
    zen_redirect(zen_href_link(FILENAME_TEMPLATE_COLORS, 'select_template=' . $currentTemplate));
    break;
  case 'insertElement' :
    break;
  default :
    if (!isset($currentTemplate) && $currentTemplate !== '') {
      $currentTemplateQuery = "SELECT template_dir
                               FROM " . TABLE_TEMPLATE_SELECT . "
                               LIMIT 1";
      $currentTemplateFields = $db->Execute($currentTemplateQuery);
      $currentTemplate = $currentTemplateFields->fields['template_dir'];
    }

    $cssArray = CssFileToArray($currentTemplate);
}
?>
<?php
$templateInfo = getTemplateInfo();
$templateArray = array();
foreach ($templateInfo as $key => $value) {
  $templateArray[] = array(
    'id' => $key,
    'text' => $value['name']);
}
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta charset="<?php echo CHARSET; ?>">
    <title><?php echo HEADING_TITLE; ?></title>
    <link rel="stylesheet" href="includes/stylesheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link rel="stylesheet" href="includes/css/colorpicker.css">
    <script src="includes/general.js"></script>
  </head>
  <body>
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->

    <!-- body //-->

    <div class="container-fluid">
      <h1><?php echo HEADING_TITLE; ?></h1>
      <div class="row">
          <?php echo zen_draw_form('template-select', FILENAME_TEMPLATE_COLORS, '', 'get', 'class="form-horizontal"'); ?>
          <?php echo zen_draw_label(LABEL_SELECT_TEMPLATE, 'select_template', 'class="control-label col-sm-3"'); ?>
        <div class="col-sm-9 col-md-6">
            <?php echo zen_draw_pull_down_menu('select_template', $templateArray, $currentTemplate, 'class="form-control" onchange="this.form.submit();"'); ?>
          <span class="help-block"><?php echo TEXT_HELP_TEMPLATE; ?></span>
          <?php echo zen_draw_hidden_field('action', 'set_template'); ?>
        </div>
        <?php echo '</form>'; ?>
      </div>
      <div class="row">
          <?php echo zen_draw_form('edit-css', FILENAME_TEMPLATE_COLORS, 'action=save', 'post', 'class="form-horizontal"'); ?>
          <?php echo zen_draw_hidden_field('file', DIR_FS_CATALOG_TEMPLATES . $currentTemplate . '/css/' . 'stylesheet_colors.css'); ?>
        <table class="table table-striped">
          <thead>
            <tr class="dataTableHeadingRow">
              <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_COLOR_ELEMENT; ?></th>
              <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_COLOR_TITLE; ?></th>
              <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_COLOR_PROPERTY; ?></th>
              <th colspan="2" class="dataTableHeadingContent"><?php echo TABLE_HEADING_COLOR_VALUE; ?></th>
              <th class="dataTableHeadingContent text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
              <?php
              $propertyCount = 0;
              $elementCount = 0;
              foreach ($cssArray as $element => $properties) {
                if ($action == 'edit' && $_GET['elementCount'] == $elementCount && $_GET['advanced'] == 'true') {
                  ?>
                <tr>
                  <td colspan="3" class="dataTableContent"><?php echo zen_draw_input_field($element, $element, 'class="form-control"' . ($_GET['advanced'] !== 'true' ? ' readonly' : '')); ?>
                  <td colspan="3">&nbsp;</td>
                </tr>
              <?php } else { ?>
                <tr>
                  <td colspan="3" class="dataTableHeadingContent">
                      <?php echo $element; ?>
                      <?php echo zen_draw_hidden_field('css[' . $element . ']', $element); ?>
                  </td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td class="text-right">
                      <?php if ($action == '' || $action == 'set_template') { ?>
                      <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, zen_get_all_get_params(array('action', 'newline')) . 'action=newSelector&newline=' . $elementCount); ?>" class="btn btn-primary" role="button">Add new property</a>
                    <?php } ?>
                  </td>
                </tr>
                <?php
              }
              foreach ($properties as $property) {
                if ($property['property'] == 'background') {
                  $property['property'] = 'background-color';
                }
                if ($action == 'edit' && $_GET['propertyCount'] == $propertyCount) {
                  ?>
                  <tr>
                    <td>&nbsp;</td>
                    <td class="dataTableContent"><?php echo zen_draw_input_field('css[' . $element . '][' . $propertyCount . '][description]', $property['description'], 'class="form-control"' . ($_GET['advanced'] !== 'true' ? ' readonly' : '')); ?></td>
                    <td class="dataTableContent"><?php echo zen_draw_pull_down_menu('css[' . $element . '][' . $propertyCount . '][property]', $propertyArray, $property['property'], 'class="form-control"' . ($_GET['advanced'] !== 'true' ? ' readonly' : '')); ?></td>
                    <td colspan="2" class="dataTableContent"><?php echo zen_draw_input_field('css[' . $element . '][' . $propertyCount . '][value]', htmlspecialchars($property['value'], ENT_COMPAT, CHARSET, TRUE), 'autofocus class="form-control" id="full-popover" data-color-format="hex"'); ?></td>
                    <td class="dataTableContent text-right">
                      <div class="btn-group" style="display:flex;float:right;">
                          <?php if (isset($_GET['advanced']) && $_GET['advanced'] == 'true') { ?>
                          <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, 'action=edit&select_template=' . $currentTemplate . '&propertyCount=' . $propertyCount); ?>" class="btn btn-primary" role="button" title="Default Edit"><i class="fa fa-minus-square-o fa-lg" aria-hidden="true"></i></a>
                        <?php } else { ?>
                          <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, 'action=edit&advanced=true&select_template=' . $currentTemplate . '&propertyCount=' . $propertyCount . '&elementCount=' . $elementCount); ?>" title="!! Advanced edit !! Be Careful" class="btn btn-primary" role="button"><i class="fa fa-plus-square-o fa-lg" aria-hidden="true"></i></a>
                        <?php } ?>
                        <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, 'select_template=' . $currentTemplate); ?>" class="btn btn-default" role="button"><i class="fa fa-ban fa-lg" aria-hidden="true"></i></a>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-lg"></i></button>
                      </div>
                    </td>
                  </tr>
                  <?php
                } elseif ($action == 'delete' && $_GET['propertyCount'] == $propertyCount) {
                  ?>
                  <tr>
                    <td></td>
                    <td><?php echo $property['description']; ?></td>
                    <td><?php echo htmlspecialchars($property['property'], ENT_COMPAT, CHARSET, TRUE); ?></td>
                    <td class="dataTableContent" style="background-color: <?php echo $property['value']; ?>;min-width:60px;">&nbsp;</td>
                    <td><?php echo htmlspecialchars($property['value'], ENT_COMPAT, CHARSET, TRUE); ?></td>
                    <td>
                      <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, 'select_template=' . $currentTemplate); ?>" class="btn btn-default" role="button"><i class="fa fa-ban fa-lg" aria-hidden="true"></i></a>
                      <button type="submit" class="btn btn-danger"><i class="fa fa-trash fa-lg"></i></button>
                      <?php echo zen_draw_hidden_field('deleteProperty', 'true'); ?>
                    </td>
                  </tr>
                  <?php
                } else {
                  ?>
                  <tr>
                    <td>&nbsp;</td>
                    <td class="dataTableContent text-nowrap">
                        <?php echo $property['description']; ?>
                        <?php echo zen_draw_hidden_field('css[' . $element . '][' . $propertyCount . '][description]', $property['description']); ?>
                    </td>
                    <td class="dataTableContent">
                        <?php echo htmlspecialchars($property['property'], ENT_COMPAT, CHARSET, TRUE); ?>
                        <?php echo zen_draw_hidden_field('css[' . $element . '][' . $propertyCount . '][property]', $property['property']); ?>
                    </td>
                    <td class="dataTableContent" style="background-color: <?php echo $property['value']; ?>;min-width:60px;">&nbsp;</td>
                    <td class="dataTableContent">
                        <?php echo htmlspecialchars($property['value'], ENT_COMPAT, CHARSET, TRUE); ?>
                        <?php echo zen_draw_hidden_field('css[' . $element . '][' . $propertyCount . '][value]', $property['value']); ?>
                    </td>
                    <td class="dataTableContent text-right">
                      <div class="btn-group" style="display:flex;float:right;">
                          <?php if ($action == '' || $action == 'set_template') { ?>
                          <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, 'action=edit&select_template=' . $currentTemplate . '&propertyCount=' . $propertyCount . '&elementCount=' . $elementCount); ?>" class="btn btn-primary" role="button"><i class="fa fa-edit fa-lg" aria-hidden="true"></i> <?php echo IMAGE_EDIT; ?></a>
                          <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, 'action=delete&select_template=' . $currentTemplate . '&propertyCount=' . $propertyCount . '&elementCount=' . $elementCount); ?>" class="btn btn-warning" role="button"><i class="fa fa-trash fa-lg"></i> <?php echo IMAGE_DELETE; ?></a>
                        <?php } ?>
                      </div>
                    </td>
                  </tr>
                  <?php
                }
                $propertyCount++;
              }
              if ($action == 'newSelector' && $elementCount == $_GET['newline']) {
                ?>
                <tr>
                  <td class="dataTableContent">&nbsp;</td>
                  <td class="dataTableContent"><?php echo zen_draw_input_field('css[' . $element . '][' . $propertyCount . '][description]', '', 'class="form-control" placeholder="Optional Comment"'); ?></td>
                  <td class="dataTableContent"><?php echo zen_draw_pull_down_menu('css[' . $element . '][' . $propertyCount . '][property]', $propertyArray, '', 'class="form-control"'); ?></td>
                  <td colspan="2" class="dataTableContent"><?php echo zen_draw_input_field('css[' . $element . '][' . $propertyCount . '][value]', '', 'autofocus class="form-control" id="full-popover" data-color-format="hex"'); ?></td>
                  <td class="dataTableContent">
                    <div class="btn-group back" style="display:flex;">
                      <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, zen_get_all_get_params(array('action'))); ?>" class="btn btn-default" role="button"><i class="fa fa-ban fa-lg" aria-hidden="true"></i> <?php echo TEXT_CANCEL; ?></a>
                      <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-lg" aria-hidden="true"></i> <?php echo IMAGE_SAVE; ?></button>
                    </div>
                  </td>
                </tr>
                <?php
              }
              $elementCount++;
            }
            ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="6">
                  <?php if ($_GET['action'] == '') { ?>
                  <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, zen_get_all_get_params(array('action')) . 'action=newElement'); ?>" class="btn btn-primary" role="button"><i class="fa fa-plus"></i> Add new element</a>
                <?php } ?>
              </td>
            </tr>
          </tfoot>
        </table>
        <?php echo '</form>'; ?>
        <?php
        if ($action == 'newElement') {
          ?>
          <?php echo zen_draw_form('insertElement', FILENAME_TEMPLATE_COLORS, 'action=insertElement', 'post', 'class="form-horizontal"'); ?>
          <table class="table">
            <tr>
              <td class="dataTableContent"><?php echo zen_draw_label('Element name (class or id)', 'element', 'class="control-label"'); ?></td>
              <td class="dataTableContent"><?php echo zen_draw_input_field('element', '', 'class="form-control" autofocus'); ?></td>
              <td class="dataTableContent">
                <div class="btn-group back" style="display:flex;">
                  <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, zen_get_all_get_params(array('action'))); ?>" class="btn btn-default" role="button"><i class="fa fa-ban fa-lg" aria-hidden="true"></i> <?php echo TEXT_CANCEL; ?></a>
                  <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-lg" aria-hidden="true"></i> <?php echo IMAGE_SAVE; ?></button>
                </div>
              </td>
            </tr>
          </table>
          <?php echo '</form>'; ?>
        </div>
        <?php
      }
      ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinycolor/0.11.1/tinycolor.min.js"></script>
    <script src="includes/javascript/colorpicker.js"></script>

    <script>
      $(document).ready(function () {
          $("input#full-popover").ColorPickerSliders({
              placement: 'auto left',
              hsvpanel: true,
              previewformat: 'hex',
              titleswatchesadd: 'Add color to swatches', // translate
              titleswatchesremove: 'Remove color from swatches', // translate
              titleswatchesreset: 'Reset to default swatches' // translate
          });
      });
    </script>
    <!-- body_eof //-->

    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php

function getTemplateInfo() {
  // get an array of template info
  $dir = dir(DIR_FS_CATALOG_TEMPLATES);
  if (!$dir) {
    die('DIR_FS_CATALOG_TEMPLATES NOT SET');
  }
  while ($file = $dir->read()) {
    if ((is_dir(DIR_FS_CATALOG_TEMPLATES . $file) && strtoupper($file) != 'CVS' && $file != 'template_default') && (file_exists(DIR_FS_CATALOG_TEMPLATES . $file . '/template_info.php'))) {
      require(DIR_FS_CATALOG_TEMPLATES . $file . '/template_info.php');
      $template_info[$file] = array(
        'name' => $template_name
      );
    }
  }
  $dir->close();

  return $template_info;
}

function CssFileToArray($currentTemplate) {
  $cssFile = DIR_FS_CATALOG_TEMPLATES . $currentTemplate . '/css/' . 'stylesheet_colors.css';
  if (file_exists($cssFile)) {
    $cssFileString = file_get_contents($cssFile);
  }
  $cssArray = array();
  preg_match_all('/(?ims)([a-z0-9\s\,\.\:#_\-@*()\[\]"=]+)\{([^\}]*)\}/', $cssFileString, $cssArray);

  $result = array();
  foreach ($cssArray[0] as $i => $x) {
    $selector = trim($cssArray[1][$i]);
    $rules = explode(';', trim($cssArray[2][$i]));
    $result[$selector] = array();
    foreach ($rules as $strRule) {
      if (!empty($strRule)) {
        $commentRaw = $strRule;
        $comment = preg_replace('/\/\*/', '', substr($commentRaw, 0, strpos($commentRaw, '*/')));
        if ($comment != '') {
          $rule = explode(":", substr($strRule, strpos($strRule, '*/') + 2));
        } else {
          $rule = explode(":", $strRule);
        }
        $result[$selector][] = [
          'property' => trim($rule[0]),
          'value' => trim($rule[1]),
          'description' => trim($comment)];
      }
    }
  }
  return $result;
}

function saveCssToFile($cssPostArray, $newCssFile) {
  global $messageStack;

  $tempFile = fopen(DIR_FS_LOGS . '/css_temp.txt', 'w');
  foreach ($cssPostArray as $newElement => $newCssBlock) {

    fwrite($tempFile, $newElement . '{' . "\n");
    foreach ($newCssBlock as $newCssLine) {
      ($newCssLine['description'] !== '' ? fwrite($tempFile, '/* ' . $newCssLine['description'] . ' */' . "\n") : '');
      fwrite($tempFile, $newCssLine['property'] . ':' . $newCssLine['value'] . ';' . "\n");
    }
    fwrite($tempFile, '}' . "\n");
  }
  fclose($tempFile);

  $copyTemp = copy(DIR_FS_LOGS . '/css_temp.txt', $newCssFile);

  if ($copyTemp) {
    $messageStack->add_session(TEXT_INFO_COPY_COMPLETED . ': ' . $newCssFile, 'success');
    unlink(DIR_FS_LOGS . '/css_temp.txt');
  } else {
    $messageStack->add_session($newCssFile . TEXT_INFO_NOT_CHANGED);
  }
}

function minimizeCSS($css) {
  $css = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $css); // negative look ahead
  $css = preg_replace('/\s{2,}/', ' ', $css);
  $css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
  $css = preg_replace('/;}/', '}', $css);
  return $css;
}
?>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
