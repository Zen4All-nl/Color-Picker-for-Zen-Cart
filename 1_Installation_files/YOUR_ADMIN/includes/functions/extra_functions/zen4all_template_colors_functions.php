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
function zen4all_getTemplateInfo()
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
function zen4all_cssFileToArray($currentTemplate)
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
        ];
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
function zen4all_saveCssToFile($cssPostArray, $newCssFile)
{
  global $messageStack;

  $tempFile = fopen(DIR_FS_LOGS . '/css_temp.txt', 'w');
  foreach ($cssPostArray as $newElement => $newCssBlock) {

    fwrite($tempFile, $newCssBlock['element'] . ' {' . "\n");
    unset($newCssBlock['element']);
    foreach ($newCssBlock as $newCssLine) {
      // ($newCssLine['description'] !== '' ? fwrite($tempFile, '/* ' . $newCssLine['description'] . ' */' . "\n") : '');
      fwrite($tempFile, $newCssLine['property'] . ':' . $newCssLine['value'] . ($newCssLine['important'] == '!important' ? ' ' . $newCssLine['important'] : '') . ';' . "\n");
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
 * 
 * @global array $messageStack
 * @param string $cssNewElement
 * @param string $newCssFile
 */
function zen4all_insertElementToFile($cssNewElement, $newCssFile)
{
  global $messageStack;

  $tempFile = fopen(DIR_FS_LOGS . '/css_temp.txt', 'w');
  // Open the file to get existing content
  $current = file_get_contents($newCssFile);
  // Append
  $current .= $cssNewElement . ' {' . "\n";
  $current .= '}' . "\n";
  // Write the contents back to the file
  fwrite($tempFile, $current);
  fclose($tempFile);
  // Copy to current file
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
function zen4all_minimizeCSS($cssRaw)
{
  $step1 = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $cssRaw); // negative look ahead
  $step2 = preg_replace('/\s{2,}/', ' ', $step1);
  $step3 = preg_replace('/\s*([:;{}])\s*/', '$1', $step2);
  $cssMin = preg_replace('/;}/', '}', $step3);
  return $cssMin;
}

function zen4all_createNewCssFile($currentTemplate)
{
  global $messageStack;
  if (!file_exists(DIR_FS_CATALOG_TEMPLATES . $currentTemplate . '/css/' . ZEN4ALL_COLORPICKER_STYLESHEET)) {
    $newCssFile = fopen(DIR_FS_CATALOG_TEMPLATES . $currentTemplate . '/css/' . ZEN4ALL_COLORPICKER_STYLESHEET, 'w');
    fclose($newCssFile);
  }
  if ($newCssFile) {
    $messageStack->add_session(TEXT_INFO_COPY_COMPLETED . ': ' . $newCssFile, 'success');
  } else {
    $messageStack->add_session($newCssFile . TEXT_INFO_NOT_CREATED);
  }
}
