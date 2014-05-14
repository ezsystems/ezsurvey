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
if ( $surveyWizard->state() != 'none' )
{
    $uri = 'survey/wizard';
    $Module->redirectTo( $uri );
}
else
{
    $offset = $Params['Offset'];
    if ( !$offset )
        $offset = 0;
    $limit = 25;

    $params = array( 'offset' => $offset,
                     'limit' => $limit );
    $count = 0;
    $surveyList = eZSurvey::fetchSurveyList( $params, $count );

    $viewParameters = array( 'offset' => $offset );

    $tpl = eZTemplate::factory();

    $tpl->setVariable( 'survey_list', $surveyList );
    $tpl->setVariable( 'count', $count );
    $tpl->setVariable( 'limit', $limit );
    $tpl->setVariable( 'view_parameters', $viewParameters );

    $Result = array();
    $Result['left_menu'] = 'design:parts/survey/menu.tpl';
    $Result['content'] = $tpl->fetch( 'design:survey/list.tpl' );
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezpI18n::tr( 'survey', 'Survey' ) ) );
}
?>
