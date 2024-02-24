<?php

require_once 'tplbytype.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function tplbytype_civicrm_config(&$config) {
  _tplbytype_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_install
 */
function tplbytype_civicrm_install() {
  return _tplbytype_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_enable
 */
function tplbytype_civicrm_enable() {
  return _tplbytype_civix_civicrm_enable();
}

/**
 * Alter tpl file to include a different tpl file based on contribution/financial type
 * (if one exists). It will look for
 * templates/CRM/Contribute/Form/Contribution/Type2/Main.php
 * where the form has a contribution or financial type of 2
 *
 * @param string $formName name of the form
 * @param object $form (reference) form object
 * @param string $context page or form
 * @param string $tplName (reference) change this if required to the altered tpl file
 *
 * @return string
 */
function tplbytype_civicrm_alterTemplateFile($formName, &$form, $context, &$tplName) {
  $formsToTouch = array(
    'CRM_Contribute_Form_Contribution_Main' => array('path' => 'CRM/Contribute/Form/Contribution/', 'file' => 'Main'),
    'CRM_Contribute_Form_Contribution_Confirm' => array('path' => 'CRM/Contribute/Form/Contribution', 'file' => 'Confirm'),
    'CRM_Contribute_Form_Contribution_ThankYou' => array('path' => 'CRM/Contribute/Form/Contribution', 'file' => 'ThankYou'),
  );

  if(!array_key_exists($formName, $formsToTouch)) {
    return;
  }
  if(isset($form->_values['financial_type_id'])) {
    $type = 'Type' . $form->_values['financial_type_id'];
  }
  if(isset($form->_values['contribution_type_id'])) {
    $type = 'Type' . $form->_values['contribution_type_id'];
  }

  if(isset($form->_values['campaign_id'])) {
    $campaign = 'Campaign' . $form->_values['campaign_id'];
  }

  if(empty($type) && empty($campaign)) {
    return;
  }
  $possibleTpl = $formsToTouch[$formName]['path'] . $type . '/' . $formsToTouch[$formName]['file']. '.tpl';
  $template = CRM_Core_Smarty::singleton();
  if ($template->template_exists($possibleTpl)) {
    $tplName = $possibleTpl;
    return $tplName;
  }

  if(empty($campaign)) {
    // return here to avoid it compiling a duff url
    return;
  }

  $possibleTemplates = array(
    $formsToTouch[$formName]['path'] . $campaign . '/' . $formsToTouch[$formName]['file']. '.tpl',
    $formsToTouch[$formName]['path'] . 'AnyCampaign/' . $formsToTouch[$formName]['file']. '.tpl',
  );
  foreach ($possibleTemplates as $possibleTpl) {
    if ($template->template_exists($possibleTpl)) {
      $tplName = $possibleTpl;
      return $tplName;
    }
  }
}
