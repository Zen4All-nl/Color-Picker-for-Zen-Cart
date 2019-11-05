<?php
/**
 * Template Colors module
 * @version 1.0.0
 * @author Zen4All
 * @copyright (c) 2014-2019, Zen4All
 * @license http://www.gnu.org/licenses/gpl.txt GNU General Public License V2.0
 */
require('includes/application_top.php');

// temp location for define
if (!defined('ZEN4ALL_COLORPICKER_STYLESHEET')) {
  define('ZEN4ALL_COLORPICKER_STYLESHEET', 'stylesheet_colors.css');
}

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
  ]
];
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
    if (!is_writeable(DIR_FS_CATALOG_TEMPLATES . $currentTemplate . '/css/' . ZEN4ALL_COLORPICKER_STYLESHEET)) {
      $file_writeable = false;
      $messageStack->reset();
      $messageStack->add(sprintf(ERROR_FILE_NOT_WRITEABLE, DIR_FS_CATALOG_TEMPLATES . $currentTemplate . '/css/' . ZEN4ALL_COLORPICKER_STYLESHEET), 'error');
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" href="includes/stylesheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link rel="stylesheet" href="includes/css/zen4all_template_colors/colorpicker.css">
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
        <?php echo zen_draw_hidden_field('file', DIR_FS_CATALOG_TEMPLATES . $currentTemplate . '/css/' . ZEN4ALL_COLORPICKER_STYLESHEET); ?>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr class="dataTableHeadingRow">
                <th class="dataTableHeadingContent" id="headingCssElement"><?php echo TABLE_HEADING_COLOR_ELEMENT; ?></th>
                <th class="dataTableHeadingContent text-right" id="headingAction"><?php echo TABLE_HEADING_ACTION; ?></th>
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
                    <td class="dataTableContent CssElement"><?php echo zen_draw_input_field($element, $element, 'class="form-control"' . ($_GET['advanced'] !== 'true' ? ' readonly' : '')); ?>
                    <td>&nbsp;</td>
                  </tr>
                <?php } else { ?>
                  <tr>
                    <td class="dataTableContent CssElement">
                      <?php echo $element; ?>
                      <?php echo zen_draw_hidden_field('css[' . $element . ']', $element); ?>
                    </td>
                    <td class="Action text-right">
                      <?php if ($action == '' || $action == 'set_template') { ?>
                        <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, zen_get_all_get_params(array('action', 'newline')) . 'action=newSelector&newline=' . $elementCount); ?>" class="btn btn-primary btn-xs" role="button"><i class="fa fa-plus"></i> Add property</a>
                      <?php } ?>
                    </td>
                  </tr>
                  <?php
                }
                foreach ($properties as $property) {
                  if ($property['property'] == 'background') {
                    $property['property'] = 'background-color';
                  }
                  ?>
                  <tr>
                    <td colspan="2">
                      <table class="table">
                        <thead>
                          <tr>
                            <th class="dataTableHeadingContent" id="headingColorDescription" style="width:40%"><?php echo TABLE_HEADING_COLOR_TITLE; ?></th>
                            <th class="dataTableHeadingContent" id="headingColorPorperty" style="width:30%"><?php echo TABLE_HEADING_COLOR_PROPERTY; ?></th>
                            <th class="dataTableHeadingContent" id="headingColorValue" style="width:10%"><?php echo TABLE_HEADING_COLOR_VALUE; ?></th>
                            <th style="width:10%"></th>
                            <th style="width:10%"></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if ($action == 'edit' && $_GET['propertyCount'] == $propertyCount) { ?>

                            <tr>
                              <td class="dataTableContent"><?php echo $property['description']; ?></td>
                              <td class="dataTableContent"><?php echo $property['property']; ?></td>
                              <td colspan="2" class="dataTableContent"><?php echo zen_draw_input_field('css[' . $element . '][' . $propertyCount . '][value]', htmlspecialchars($property['value'], ENT_COMPAT, CHARSET, TRUE), 'autofocus class="form-control" id="full-popover" data-color-format="hex"'); ?> <div class="checkbox"><label><?php echo zen_draw_checkbox_field('css[' . $element . '][' . $propertyCount . '][important]', '!important', ($property['important'] == '!important')) . ' ' . TEXT_IMPORTANT; ?></label></div></td>
                              <td class="dataTableContent text-right">
                                <?php if (isset($_GET['advanced']) && $_GET['advanced'] == 'true') { ?>
                                  <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, 'action=edit&select_template=' . $currentTemplate . '&propertyCount=' . $propertyCount); ?>" class="btn btn-primary btn-sm" role="button" title="Default Edit"><i class="fa fa-minus-square-o fa-lg" aria-hidden="true"></i></a>
                                <?php } else { ?>
                                  <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, 'action=edit&advanced=true&select_template=' . $currentTemplate . '&propertyCount=' . $propertyCount . '&elementCount=' . $elementCount); ?>" title="!! Advanced edit !! Be Careful" class="btn btn-primary btn-sm" role="button"><i class="fa fa-plus-square-o fa-lg" aria-hidden="true"></i></a>
                                <?php } ?>
                                <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, 'select_template=' . $currentTemplate); ?>" class="btn btn-default btn-sm" role="button"><i class="fa fa-ban fa-lg" aria-hidden="true"></i></a>
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save fa-lg"></i></button>
                              </td>
                            </tr>
                          <?php } elseif ($action == 'delete' && $_GET['propertyCount'] == $propertyCount) { ?>
                            <tr>
                              <td><?php echo $property['description']; ?></td>
                              <td><?php echo htmlspecialchars($property['property'], ENT_COMPAT, CHARSET, TRUE); ?></td>
                              <td class="dataTableContent" style="background-color: <?php echo $property['value']; ?>;min-width:60px;">&nbsp;</td>
                              <td><?php echo htmlspecialchars($property['value'], ENT_COMPAT, CHARSET, TRUE); ?></td>
                              <td>
                                <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, 'select_template=' . $currentTemplate); ?>" class="btn btn-default btn-sm" role="button"><i class="fa fa-ban fa-lg" aria-hidden="true"></i></a>
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash fa-lg"></i></button>
                                <?php echo zen_draw_hidden_field('deleteProperty', 'true'); ?>
                              </td>
                            </tr>
                          <?php } else { ?>
                            <tr>
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
                                <?php echo htmlspecialchars($property['value'], ENT_COMPAT, CHARSET, TRUE) . ' ' . $property['important']; ?>
                                <?php echo zen_draw_hidden_field('css[' . $element . '][' . $propertyCount . '][value]', $property['value']); ?>
                              </td>
                              <td class="dataTableContent text-right">
                                <?php if ($action == '' || $action == 'set_template') { ?>
                                  <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, 'action=edit&select_template=' . $currentTemplate . '&propertyCount=' . $propertyCount . '&elementCount=' . $elementCount); ?>" class="btn btn-primary btn-sm" role="button"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a>
                                  <a href="<?php echo zen_href_link(FILENAME_TEMPLATE_COLORS, 'action=delete&select_template=' . $currentTemplate . '&propertyCount=' . $propertyCount . '&elementCount=' . $elementCount); ?>" class="btn btn-warning btn-sm" role="button"><i class="fa fa-trash fa-lg"></i></a>
                                <?php } ?>
                              </td>
                            </tr>
                            <?php
                          }
                          $propertyCount++;
                          ?>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <?php
                }
                if ($action == 'newSelector' && $elementCount == $_GET['newline']) {
                  ?>
                  <tr>
                    <td class="dataTableContent"><?php echo zen_draw_input_field('css[' . $element . '][' . $propertyCount . '][description]', '', 'class="form-control" placeholder="Optional Comment"'); ?></td>
                    <td class="dataTableContent"><?php echo zen_draw_pull_down_menu('css[' . $element . '][' . $propertyCount . '][property]', $propertyArray, '', 'class="form-control"'); ?></td>
                    <td colspan="2" class="dataTableContent"><?php echo zen_draw_input_field('css[' . $element . '][' . $propertyCount . '][value]', '', 'class="form-control" id="full-popover" data-color-format="hex"'); ?></td>
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
        </div>
        <?php echo '</form>'; ?>
        <?php if ($action == 'newElement') { ?>
          <?php echo zen_draw_form('insertElement', FILENAME_TEMPLATE_COLORS, 'action=insertElement', 'post', 'class="form-horizontal"'); ?>
          <table class="table">
            <tr>
              <td class="dataTableContent"><?php echo zen_draw_label('Element name (class or id)', 'element', 'class="control-label"'); ?></td>
              <td class="dataTableContent"><?php echo zen_draw_input_field('element', '', 'class="form-control"'); ?></td>
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
      <?php } ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinycolor/0.11.1/tinycolor.min.js"></script>
    <script src="includes/javascript/zen4all_template_colors/colorpicker.js"></script>

    <script>
      $(document).ready(function () {
        $('#full-popover').focus().blur();
        $('#full-popover').ColorPickerSliders({
          'placement': 'auto left',
          'hsvpanel': true,
          'previewformat': 'hex',
          'titleswatchesadd': 'Add color to swatches', // translate
          'titleswatchesremove': 'Remove color from swatches', // translate
          'titleswatchesreset': 'Reset to default swatches' // translate
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
require(DIR_WS_INCLUDES . 'application_bottom.php');
