<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
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

    $tpl = eZTemplate::factory();

    $tpl->setVariable( 'config', $config );
    $surveyWizard = eZSurveyWizard::instance();
    $tpl->setVariable( 'content_class_list', $surveyWizard->attribute( 'content_class_list' ) );

    $Result = array();
    $Result['left_menu'] = 'design:parts/survey/menu.tpl';
    $Result['content'] = $tpl->fetch( 'design:survey/relatedobjectconfig.tpl' );
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezpI18n::tr( 'survey', 'Survey' ) ) );

}

?>
