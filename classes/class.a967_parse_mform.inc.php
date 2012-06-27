<?php
/*
class.a967_parsemform.inc.php

@author mail[at]joachim-doerr[dot]com Joachim Doerr
@author <a href="http://joachim-doerr.com">joachim-doerr.com</a>

@package redaxo4
@version 2.1.2
*/

// MFROM PARSER CLASS
////////////////////////////////////////////////////////////////////////////////
class a967_parsemform
{
  /**/
  // define defaults
  /**/
  
  var $strOutput;
  var $strTemplateStyle;
  
  
  /**/
  // generate fields
  /**/
  
  /*
  html, headline, description
  */
  function generateLineElement($arrElement)
  {
    $strTemplate = 'single_line';
    if ($arrElement['type'] == 'headline')
    {
      $strTemplate = 'single_headline';
    }
    if ($arrElement['type'] == 'description')
    {
      $strTemplate = 'description_line';
    }
    
    $strElement = <<<EOT
      
      <mform:output>{$arrElement['default']}</mform:output>
      
EOT;
    return $this->parseElementToTemplate($strElement,$strTemplate);
  }
  
  /*
  hidden, text, password
  */
  function generateInputElement($arrElement)
  {
    $strTemplate = 'default_line';
    if ($arrElement['type'] == 'hidden')
    {
      $strTemplate = 'hidden';
    }
    $arrElement['attributes'] = $this->getAttributes($arrElement['attributes']);
    $strElement = <<<EOT
      
      <mform:label><label for="rv{$arrElement['id']}">{$arrElement['label']}</label></mform:label>
      <mform:element><input id="rv{$arrElement['id']}" type="{$arrElement['type']}" name="VALUE[{$arrElement['id']}]" value="{$arrElement['default']}" {$arrElement['attributes']} /></mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,$strTemplate);
  }
  
  /*
  textarea, markitup
  */
  function generateAreaElement($arrElement)
  {
    $arrElement['attributes'] = $this->getAttributes($arrElement['attributes']);
    $strElement = <<<EOT
      
      <mform:label><label for="rv{$arrElement['id']}">{$arrElement['label']}</label></mform:label>
      <mform:element><textarea id="rv{$arrElement['id']}" name="VALUE[{$arrElement['id']}]" {$arrElement['attributes']} >{$arrElement['default']}</textarea></mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,'default_line');
  }
  
  /*
  select, multiselect
  */
  function generateOptionsElement($arrElement)
  {
    $arrElement['attributes'] = $this->getAttributes($arrElement['attributes']);
    $strSelectAttributes = ''; $strMultiselectJavascript = ''; $strMultiselectHidden = ''; $arrHiddenValue = array(); $strOptions = '';
    
    $strSelectAttributes = (is_numeric($arrElement['size']) === true) ? 'size="' . $arrElement['size'] . '"' : '' ;
    if ($arrElement['size'] == 'full')
    {
      $strSelectAttributes = 'size="' . sizeof($arrElement['options']) . '"';
    }
    
    if ($arrElement['multi'] === true)
    {
      $strSelectAttributes .= ' multiple="multiple"';
      $strMultiselectJavascript = <<<EOT
        <script type="text/javascript">
          /* <![CDATA[ */
            jQuery(document).ready(function($){
              $("#rv{$arrElement['id']}").change(function() {
                $("#hidden_rv{$arrElement['id']}").val($(this).val());
              });
            });
          /* ]]> */
        </script>
EOT;
      $strMultiselectHidden = <<<EOT
        <input id="hidden_rv{$arrElement['id']}" type="hidden" name="VALUE[{$arrElement['id']}]" value="{$arrElement['default']}" />
EOT;
      if ($arrElement['default'] != '')
      {
        $arrHiddenValue = explode(',',$arrElement['default']);
      }
    }
    else
    {
      $arrHiddenValue = array($arrElement['default']);
    }
    
    foreach ($arrElement['options'] as $intKey => $strValue) {
      $strOptions .= '<option value="' . $intKey . '" ';
        foreach ($arrHiddenValue as $strHiddenValue) {
          if ($intKey == $strHiddenValue)
          {
            $strOptions .= 'selected="selected" ';
          }
        }
      $strOptions .= '>' . $strValue . '</option>';
    }
    $strElement = <<<EOT
      
      <mform:label><label for="rv{$arrElement['id']}">{$arrElement['label']}</label>$strMultiselectJavascript</mform:label>
      <mform:element><select id="rv{$arrElement['id']}" name="VALUE[{$arrElement['id']}]" {$arrElement['attributes']} $strSelectAttributes>$strOptions</select>$strMultiselectHidden</mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,'default_line');
  }
  
  /*
  radio
  */
  function generateRadioElement($arrElement)
  {
    $intCount = 0;
    foreach ($arrElement['options'] as $intKey => $strValue)
    {
      $intCount++;
      $strOptions .= '<div class="radio_element"><input id="rv' . $arrElement['id'] . $intCount . '" type="radio" name="VALUE[' . $arrElement['id'] . ']" value="' . $intKey . '" ';
      if ($intKey == $arrElement['default'])
      {
        $strOptions .= 'checked="checked" ';
      }
      $strOptions .= ' /><span for="rv' . $arrElement['id'] . '" class="radio_description"><label class="description" for="rv' . $arrElement['id'] . $intCount . '">' . $strValue . '</label></span></div>';
    }
    $strElement = <<<EOT
      
      <mform:label><label for="rv{$arrElement['id']}">{$arrElement['label']}</label></mform:label>
      <mform:element>$strOptions</mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,'default_line');
  }
  
  /*
  checkbox
  */
  function generateCheckboxElement($arrElement)
  {
    $arrElement['options'] = array(end(array_keys($arrElement['options'])) => end($arrElement['options']));
    foreach ($arrElement['options'] as $intKey => $strValue)
    {
      $strOptions .= '<div class="radio_element"><input id="rv' . $arrElement['id'] . '" type="checkbox" name="VALUE[' . $arrElement['id'] . ']" value="' . $intKey . '" ';
      if ($intKey == $arrElement['default'])
      {
        $strOptions .= 'checked="checked" ';
      }
      $strOptions .= ' /><span for="rv' . $arrElement['id'] . '" class="radio_description"><label class="description" for="rv' . $arrElement['id'] . '">' . $strValue . '</label></span></div>';
    }
    $strElement = <<<EOT
      
      <mform:label><label for="rv{$arrElement['id']}">{$arrElement['label']}</label></mform:label>
      <mform:element>$strOptions</mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,'default_line');
  }
  
  /*
  link, linklist
  */
  function generateLinkElement($arrElement)
  {
    $arrID = explode('-', $arrElement['id']);
    if ($arrElement['type'] == 'link')
    {
      $strOptions = rex_var_link::getLinkButton($arrID[1], $arrElement['default'], $arrElement['cid']);
    }
    if ($arrElement['type'] == 'linklist')
    {
      $strOptions = rex_var_link::getLinkListButton($arrID[1], $arrElement['default'], $arrElement['cid']);
    }
    $strElement = <<<EOT
      
      <mform:label><label>{$arrElement['label']}</label></mform:label>
      <mform:element>$strOptions</mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,'default_line');
  }
  
  /*
  media, medialist
  */
  function generateMediaElement($arrElement) 
  {
    $arrID = explode('-', $arrElement['id']);
    if ($arrElement['type'] == 'media')
    {
      $strOptions = rex_var_media::getMediaButton($arrID[1], $arrElement['cid'], $arrElement['parameter']);
      $strOptions = str_replace('REX_MEDIA['. $arrID[1] .']', $arrElement['default'], $strOptions);
    }
    if ($arrElement['type'] == 'medialist')
    {
      $strOptions = rex_var_media::getMediaListButton($arrID[1], $arrElement['default'], $arrElement['cid'], $arrElement['parameter']);        
    }
    $strElement = <<<EOT
      
      <mform:label><label>{$arrElement['label']}</label></mform:label>
      <mform:element>$strOptions</mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,'default_line');
  }
  
  
  /**/
  // get attributes
  /**/
  
  function getAttributes($arrAttributes)
  {
    $strAttributes = NULL;
    if (sizeof($arrAttributes) > 0)
    {
      foreach ($arrAttributes as $strKey => $strValue)
      {
        if (
             $strKey == 'style'
          or $strKey == 'class'
          or $strKey == 'rel'
          or $strKey == 'alt'
          or $strKey == 'title'
        )
        {
          $strAttributes .= ' '.$strKey.'="'.$strValue.'"';
        }
      }
    }
    return $strAttributes;
  }
  
  /**/
  // parse form fields by types
  /**/
  
  function parseFormFields($arrElements)
  {
    if (sizeof($arrElements) > 0)
    {
      foreach ($arrElements as $intKey => $arrElement)
      {
        switch ($arrElement['type'])
        {
          case 'html':
          case 'single_line':
          case 'headline':
          case 'description':
            $this->generateLineElement($arrElement);
            break;
          
          case 'text':
          case 'hidden':
          case 'password':
            $this->generateInputElement($arrElement);
            break;
                    
          case 'textarea':
          case 'markitup':
            $this->generateAreaElement($arrElement);
            break;
          
          case 'select':
          case 'multiselect':
            $this->generateOptionsElement($arrElement);
            break;
          
          case 'radio':
          case 'radiobutton':
            $this->generateRadioElement($arrElement);
            break;
          
          case 'checkbox':
            $this->generateCheckboxElement($arrElement);
            break;
          
          case 'link':
          case 'linklist':
            $this->generateLinkElement($arrElement);
            break;
          
          case 'media':
          case 'medialist':
            $this->generateMediaElement($arrElement);
            break;
        }
      }
    }
  }
  
  
  /**/
  // parse form to template
  /**/
  
  function parseElementToTemplate($strElement, $strTemplateKey, $boolParseFinal = false)
  {
    global $myroot;
    $strTemplate = implode(file($myroot . "/templates/mform_" . $strTemplateKey . ".ini", FILE_USE_INCLUDE_PATH));
    
    preg_match('|<mform:label>(.*?)</mform:label>|ism', $strElement, $arrLabel);
    preg_match('|<mform:element>(.*?)</mform:element>|ism', $strElement, $arrElement);
    
    switch ($strTemplateKey)
    {
      case 'default_line':
        if ($strTemplate != '')
        {
          $strElement = str_replace(array(' />','<mform:label/>','<mform:element/>'), array('/>',$arrLabel[1],$arrElement[1]), $strTemplate);
        }
        break;
        
      case 'single_line':
      case 'wrapper':
      default:
        if (isset($arrLabel[1]) === true or isset($arrElement[1]) === true)
        {
          $strElement = $arrLabel[1].$arrElement[1];
        }
        if ($strTemplate != '')
        {
          $strElement = str_replace(array(' />','<mform:output/>'), array('/>',$strElement), $strTemplate);
        }
        break;
        
      case 'hidden':
        $strElement = $arrElement[1];
        break;
    }
    if ($boolParseFinal === true)
    {
      $this->strOutput = $strElement;
    }
    else
    {
      $this->strOutput .= $strElement;
    }
  }
  
  /*
  final parseing
  */
  function parse_mform($arrElements)
  {
    $this->parseFormFields($arrElements);
    $this->parseElementToTemplate($this->strOutput,'wrapper',true);
    return $this->strOutput;
  }
}