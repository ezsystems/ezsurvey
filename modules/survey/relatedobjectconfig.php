<?php
//
// Definition of Relatedobjectconfig class
//
// Created on: <19-Feb-2008 14:58:09 br>
//
// Copyright (C) 1999-2010 eZ Systems AS. All rights reserved.
//
// This source file is part of the eZ Publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE included in
// the packaging of this file.
//
// Licencees holding a valid "eZ Publish professional licence" version 2
// may use this file in accordance with the "eZ Publish professional licence"
// version 2 Agreement provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ Publish professional licence" version 2 is available at
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

/*! \file relatedobjectconfig.php
 */


$http = eZHTTPTool::instance();
$Module = $Params['Module'];
$surveyWizard = eZSurveyWizard::instance();
if ( $surveyWizard->databaseStatus() === false )
{
    $uri = 'survey/wizard';
    $Module->redirectTo( $uri );
}
else
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

    if ( $http->hasPostVariable( 'UpdateSurveyRelatedConfig' ) or
         $http->hasPostVariable( 'SelectedNodeIDArray' ) or
         $Module->isCurrentAction( 'BrowseForObjects' ) )
    {
        if ( $http->hasPostVariable( 'SurveyRelatedClassID' ) )
        {
            $classID = $http->postVariable( 'SurveyRelatedClassID' );
            if ( eZContentClass::fetch( $classID ) )
            {
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
                                        'from_page' => $Module->redirectionURI( 'survey', 'relatedobjectconfig', array() ) ),
                                 $Module );

        return eZModule::HOOK_STATUS_CANCEL_RUN;
    }


    require_once( 'kernel/common/template.php' );
    $tpl = templateInit();

    $tpl->setVariable( 'config', $config );
    $surveyWizard = eZSurveyWizard::instance();
    $tpl->setVariable( 'content_class_list', $surveyWizard->attribute( 'content_class_list' ) );

    $Result = array();
    $Result['left_menu'] = 'design:parts/survey/menu.tpl';
    $Result['content'] = $tpl->fetch( 'design:survey/relatedobjectconfig.tpl' );
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezi18n( 'survey', 'Survey' ) ) );

}

?>
