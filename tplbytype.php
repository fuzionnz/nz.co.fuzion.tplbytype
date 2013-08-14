<?php

require_once 'tplbytype.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function tplbytype_civicrm_config(&$config) {
  _tplbytype_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function tplbytype_civicrm_xmlMenu(&$files) {
  _tplbytype_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function tplbytype_civicrm_install() {
  return _tplbytype_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function tplbytype_civicrm_uninstall() {
  return _tplbytype_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function tplbytype_civicrm_enable() {
  return _tplbytype_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function tplbytype_civicrm_disable() {
  return _tplbytype_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function tplbytype_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _tplbytype_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function tplbytype_civicrm_managed(&$entities) {
  return _tplbytype_civix_civicrm_managed($entities);
}

/**
 * Alter tpl file to include a different tpl file based on contribution/financial type
 * (if one exists). It will look for
 * templates/CRM/Contribute/Form/Contribution/Type2/Main.php
 * where the form has a contribution or financial type of 2
 * @param string $formName name of the form
 * @param object $form (reference) form object
 * @param string $context page or form
 * @param string $tplName (reference) change this if required to the altered tpl file
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

  $possibleTpl = $formsToTouch[$formName]['path'] . $campaign . '/' . $formsToTouch[$formName]['file']. '.tpl';
  if ($template->template_exists($possibleTpl)) {
    $tplName = $possibleTpl;
    return $tplName;
  }
}
