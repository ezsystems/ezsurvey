<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

$Module = array( 'name' => 'Survey',
                 'variable_params' => true );

$ViewList = array();
$ViewList['wizard'] = array(
    'script' => 'wizard.php',
    'functions' => array( 'administration' ),
    'params' => array ( ),
    'single_post_actions' => array( 'BrowseSurveyRelatedNode' => 'BrowseForObjects' ),
    'unordered_params' => array(),
    'default_navigation_part' => 'ezsurveynavigationpart' );
$ViewList['list'] = array(
    'script' => 'list.php',
    'functions' => array( 'administration' ),
    'params' => array ( ),
    'unordered_params' => array( 'offset' => 'Offset' ),
    'default_navigation_part' => 'ezsurveynavigationpart' );
$ViewList['relatedobjectconfig'] = array(
    'script' => 'relatedobjectconfig.php',
    'functions' => array( 'administration' ),
    'single_post_actions' => array( 'BrowseSurveyRelatedNode' => 'BrowseForObjects' ),
    'params' => array (),
    'default_navigation_part' => 'ezsurveynavigationpart' );
$ViewList['edit'] = array(
    'script' => 'edit.php',
    'functions' => array( 'administration' ),
    'params' => array( 'SurveyID' ),
    'default_navigation_part' => 'ezsurveynavigationpart' );
$ViewList['copy'] = array(
    'script' => 'copy.php',
    'functions' => array( 'administration' ),
    'params' => array( 'SurveyID' ),
    'default_navigation_part' => 'ezsurveynavigationpart' );
$ViewList['preview'] = array(
    'script' => 'preview.php',
    'functions' => array( 'administration' ),
    'params' => array( 'SurveyID' ),
    'default_navigation_part' => 'ezsurveynavigationpart' );
$ViewList['view'] = array(
    'script' => 'view.php',
    'functions' => array( 'filling' ),
    'params' => array( 'SurveyID' ),
    'default_navigation_part' => 'ezsurveynavigationpart' );
$ViewList['action'] = array(
    'script' => 'action.php',
    'functions' => array( 'filling' ),
    'params' => array(),
    'default_navigation_part' => 'ezcontentnavigationpart' );
$ViewList['result'] = array(
    'script' => 'result.php',
    'functions' => array( 'administration' ),
    'params' => array( 'ContentObjectID', 'ContentClassAttributeID', 'LanguageCode' ),
    'default_navigation_part' => 'ezsurveynavigationpart' );
$ViewList['rview'] = array(
    'script' => 'rview.php',
    'functions' => array( 'administration' ),
    'params' => array( 'ContentObjectID', 'ContentClassAttributeID', 'LanguageCode', 'SurveyResultID' ),
    'unordered_params' => array( 'offset' => 'Offset' ),
    'default_navigation_part' => 'ezsurveynavigationpart' );
$ViewList['result_list'] = array(
    'script' => 'result_list.php',
    'functions' => array( 'administration' ),
    'params' => array( 'ContentObjectID', 'ContentClassAttributeID', 'LanguageCode' ),
    'unordered_params' => array( 'offset' => 'Offset' ),
    'default_navigation_part' => 'ezsurveynavigationpart' );
$ViewList['result_edit'] = array(
    'script' => 'result_edit.php',
    'functions' => array( 'administration' ),
    'params' => array( 'ResultID' ),
    'default_navigation_part' => 'ezsurveynavigationpart' );
$ViewList['rremove'] = array(
    'script' => 'rremove.php',
    'functions' => array( 'administration' ),
    'params' => array( 'ResultID' ),
    'default_navigation_part' => 'ezsurveynavigationpart' );
$ViewList['remove'] = array(
    'script' => 'remove.php',
    'functions' => array( 'administration' ),
    'params' => array( 'SurveyID' ),
    'default_navigation_part' => 'ezsurveynavigationpart' );
$ViewList['export'] = array(
    'script' => 'export.php',
    'functions' => array( 'administration' ),
    'params' => array( 'ContentObjectID', 'ContentClassAttributeID', 'LanguageCode' ),
    'default_navigation_part' => 'ezsurveynavigationpart' );

$FunctionList['administration'] = array( );
$FunctionList['filling'] = array( );

?>
