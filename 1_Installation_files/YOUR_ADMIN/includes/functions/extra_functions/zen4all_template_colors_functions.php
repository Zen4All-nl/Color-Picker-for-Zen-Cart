<?php

/**
 * Template Colors module
 * @version 1.0.0
 * @author Zen4All
 * @copyright (c) 2014-2019, Zen4All
 * @license http://www.gnu.org/licenses/gpl.txt GNU General Public License V2.0
 */

/**
 * 
 * @return array
 */
function getTemplateInfo()
{
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

/**
 * 
 * @param string $currentTemplate
 * @return array
 */
function CssFileToArray($currentTemplate)
{
  $cssFile = DIR_FS_CATALOG_TEMPLATES . $currentTemplate . '/css/' . ZEN4ALL_COLORPICKER_STYLESHEET;
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
          'value' => trim(str_replace('!important', '', $rule[1])),
          'important' => strstr($rule[1], '!'),
          'description' => trim($comment)];
      }
    }
  }
  return $result;
}

/**
 * 
 * @global array $messageStack
 * @param array $cssPostArray
 * @param string $newCssFile
 */
function saveCssToFile($cssPostArray, $newCssFile)
{
  global $messageStack;

  $tempFile = fopen(DIR_FS_LOGS . '/css_temp.txt', 'w');
  foreach ($cssPostArray as $newElement => $newCssBlock) {

    fwrite($tempFile, $newElement . ' {' . "\n");
    foreach ($newCssBlock as $newCssLine) {
      ($newCssLine['description'] !== '' ? fwrite($tempFile, '/* ' . $newCssLine['description'] . ' */' . "\n") : '');
      fwrite($tempFile, $newCssLine['property'] . ':' . $newCssLine['value'] . ($newCssLine['important'] == '1!important' ? ' ' . $newCssLine['important'] : '') . ';' . "\n");
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

/**
 * Future function, unused for now
 * @param type $cssRaw
 * @return type
 */
function minimizeCSS($cssRaw)
{
  $step1 = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $cssRaw); // negative look ahead
  $step2 = preg_replace('/\s{2,}/', ' ', $step1);
  $step3 = preg_replace('/\s*([:;{}])\s*/', '$1', $step2);
  $cssMin = preg_replace('/;}/', '}', $step3);
  return $cssMin;
}
