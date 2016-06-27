<?php

require_once 'caselink.civix.php';

function caselink_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Case_Form_Case') {
    //set default values
    CRM_Caselink_Form_Case::setDefaultCaseLink($formName, $form);
  }
  if ($form instanceof CRM_Event_Form_ManageEvent_EventInfo) {
    CRM_Caselink_Form_EventInfo::setDefaultCaseLink($formName, $form);
  }
}

function caselink_civicrm_caseSummary($caseId) {
  /**
   * Make from the Link case to another case field a link to the linked case.
   */
  $page = new CRM_Caselink_Page_CaseLink($caseId);
  $content['caselink_case_id']['value'] = $page->run();

  /**
   * Build a tab with all the linked cases
   */
  $cases = new CRM_Caselink_Page_Cases($caseId);
  $content['caselink_cases']['value'] = $cases->run();

  /**
   * Build a tab with all the linked events
   */
  $events = new CRM_Caselink_Page_Events($caseId);
  $content['caselink_events']['value'] = $events->run();

  return $content;
}

function caselink_civicrm_pageRun(&$page) {
  if ($page instanceof CRM_Event_Page_EventInfo) {
    /**
     * Do not show Link to Case field on event information page.
     * Do this by removing the field from the assigned custom data sets.
     */
    $eventConfig = CRM_Caselink_Config_CaseLinkEvent::singleton();
    $viewCustomData = $page->get_template_vars('viewCustomData');
    unset($viewCustomData[$eventConfig->getCustomGroup('id')]);
    $page->assign_by_ref('viewCustomData', $viewCustomData);
  }
}

/**
 * Options for event link and case link
 *
 * @param int $fieldID
 * @param array $options
 * @param bool $detailedFormat
 */
function caselink_civicrm_customFieldOptions( $fieldID, &$options, $detailedFormat = false ) {
  $caseConfig = CRM_Caselink_Config_CaseLinkCase::singleton();
  $eventConfig = CRM_Caselink_Config_CaseLinkEvent::singleton();
  //auto fill option list for link to case field
  if ($fieldID == $caseConfig->getCaseIdField('id') || $fieldID == $eventConfig->getCaseIdField('id')) {
    /**
     * Build an oprion list with all the cases in the system.
     */
    $sql = "
        SELECT `civicrm_case`.*, civicrm_contact.display_name 
        FROM `civicrm_case` 
        INNER JOIN civicrm_case_contact ON civicrm_case.id = civicrm_case_contact.case_id
        INNER JOIN civicrm_contact ON civicrm_case_contact.contact_id = civicrm_contact.id
        WHERE civicrm_case.`is_deleted` = 0
        ORDER BY civicrm_contact.display_name, civicrm_case.subject";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $label = $dao->display_name.' :: '.$dao->subject;
      if ($detailedFormat) {
        $options[$dao->id] = array(
          'id' => $dao->id,
          'value' => $dao->id,
          'label' => $label
        );
      }
      else {
        $options[$dao->id] = $label;
      }
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function caselink_civicrm_config(&$config) {
  _caselink_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function caselink_civicrm_xmlMenu(&$files) {
  _caselink_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function caselink_civicrm_install() {
  _caselink_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function caselink_civicrm_uninstall() {
  _caselink_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function caselink_civicrm_enable() {
  _caselink_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function caselink_civicrm_disable() {
  _caselink_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function caselink_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _caselink_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function caselink_civicrm_managed(&$entities) {
  _caselink_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function caselink_civicrm_caseTypes(&$caseTypes) {
  _caselink_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function caselink_civicrm_angularModules(&$angularModules) {
_caselink_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function caselink_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _caselink_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function caselink_civicrm_preProcess($formName, &$form) {

}

*/