<?php
//
// Created on: <11-Jun-2008 15:47:51 br>
//
// Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE included in
// the packaging of this file.
//
// Licencees holding a valid "eZ publish professional licence" version 2
// may use this file in accordance with the "eZ publish professional licence"
// version 2 Agreement provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" version 2 is available at
// http://ez.no/ez_publish/licences/professional/ and in the file
// PROFESSIONAL_LICENCE included in the packaging of this file.
// For pricing of this licence please contact us via e-mail to licence@ez.no.
// Further contact information is available at http://ez.no/company/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*! \file wizard.php
*/

$http = eZHTTPTool::instance();

$surveyWizard = eZSurveyWizard::instance();
$databaseStatus = $surveyWizard->databaseStatus();

$Module = $Params['Module'];

if ( $http->hasPostVariable( 'ImportDatabase' ) )
{
    $surveyWizard->importDatabase();
}

if ( $http->hasPostVariable( 'ImportSurveyPackage' ) )
{
    $surveyWizard->importPackage();
}

if ( $http->hasPostVariable( 'ImportSurveyManual' ) )
{
    // redirect to the media folder.
    $uri = 'class/grouplist';
    $Module->redirectTo( $uri );
}

if ( $http->hasPostVariable( 'AddNewSurveyRelatedNode' ) )
{
    $contentINI = eZINI::instance( 'content.ini' );
    $mediaNodeID = (int)$contentINI->variable( 'NodeSettings', 'MediaRootNode' );
    $mediaNode = eZContentObjectTreeNode::fetch( $mediaNodeID );
    if ( $mediaNode instanceof eZContentObjectTreeNode )
    {
        $Module->redirectTo( $mediaNode->attribute( 'url_alias' ) );
    }
}

$state = $surveyWizard->state();

$contentClassList = false;
$surveyAttributeFound = false;
$browseAttribute = false;

if ( $state == 'conf_survey_classattribute_parent' )
{
    $list = eZSurveyRelatedConfig::fetchList();
    if ( $http->hasPostVariable( 'ConfigureSurveyAttribute' ) and
         count( $list ) > 0 and
         (int)$list[0]->attribute( 'node_id' ) == 0 )
    {
        // Need to select a parent for the survey attribute.
        $browseAttribute = true;
    }
}

if ( $state == 'survey_class' or
     $state == 'survey_classattribute' or
     $state == 'conf_survey_classattribute' or
     $state == 'conf_survey_classattribute_parent' or
     $http->hasPostVariable( 'SelectedNodeIDArray' ) )
{
    $contentClassList = $surveyWizard->attribute( 'content_class_list' );
    $configList = eZSurveyRelatedConfig::fetchList();
    if ( count( $configList ) == 0 )
    {
        $config = eZSurveyRelatedConfig::create();

        // if the ezsurvey_attribue exist, set it as default.
        foreach ( $contentClassList as $contentClass )
        {
            if ( $contentClass->attribute( 'identifier' ) == 'survey_attribute' )
            {
                $config->setAttribute( 'contentclass_id', $contentClass->attribute( 'id' ) );
                $config->store();
                $surveyAttributeFound = true;
            }
        }
    }
    else
    {
        $config = $configList[0];
    }
}
else
{
    $config = false;
}

if ( $http->hasPostVariable( 'ConfigureSurveyAttribute' ) or
     $http->hasPostVariable( 'SelectedNodeIDArray' ) or
     $Module->isCurrentAction( 'BrowseForObjects' ) )
{
    if ( $http->hasPostVariable( 'SurveyRelatedClassID' ) )
    {
        $classID = $http->postVariable( 'SurveyRelatedClassID' );
        if ( eZContentClass::fetch( $classID ) and $config !== false )
        {
            if ( $config === false )
            {
                $configList = eZSurveyRelatedConfig::fetchList();
                if ( count( $configList ) == 0 )
                {
                    $config = eZSurveyRelatedConfig::create();

                }
                else
                {
                    $config = $configList[0];
                }
            }
            $config->setAttribute( 'contentclass_id', $classID );
            $config->store();
        }
    }
}

if ( $http->hasPostVariable( 'SelectedNodeIDArray' ) )
{
    $nodeIDArray = $http->postVariable( 'SelectedNodeIDArray' );
    $nodeID = 0;
    if ( count( $nodeIDArray ) > 0 and is_numeric( $nodeIDArray[0] ) )
    {
        // update the database.
        $nodeID = $nodeIDArray[0];

        if ( $config === false )
        {
            $configList = eZSurveyRelatedConfig::fetchList();
            if ( count( $configList ) == 0 )
            {
                $config = eZSurveyRelatedConfig::create();

            }
            else
            {
                $config = $configList[0];
            }
        }

        $config->setAttribute( 'node_id', $nodeID );
        $config->store();
    }
}

if ( $Module->isCurrentAction( 'BrowseForObjects' ) )
{
    $assignedNodesIDs = array();

    eZContentBrowse::browse( array( 'action_name' => 'AddRelatedSurveyNode',
                                    'description_template' => 'design:content/browse_related.tpl',
                                    'content' => array(),
                                    'keys' => array(),
                                    'ignore_nodes_select' => $assignedNodesIDs,
                                    'from_page' => $Module->redirectionURI( 'survey', 'wizard', array() ) ),
                             $Module );

    return eZModule::HOOK_STATUS_CANCEL_RUN;
}

$tpl = eZTemplate::factory();

$tpl->setVariable( 'state', $state );
$tpl->setVariable( 'content_class_list', $contentClassList );

if ( $config !== false )
{
    $tpl->setVariable( 'config', $config );
    $tpl->setVariable( 'survey_attribute_found', $surveyAttributeFound );
    $tpl->setVariable( 'browse_attribute', $browseAttribute );
}

$Result = array();
$Result['content'] = $tpl->fetch( 'design:survey/wizard.tpl' );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'survey', 'Survey Wizard' ) ) );

?>
