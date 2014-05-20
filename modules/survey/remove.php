<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

$http = eZHTTPTool::instance();

$Module = $Params['Module'];

$surveyID = $Params['SurveyID'];
$survey = eZSurvey::fetch( $surveyID );

if ( !$survey )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

if ( $http->hasPostVariable( 'SurveyRemoveCommit' ) )
{
    $survey->remove();
    $Module->redirectTo( '/survey/list' );
}
else if ( $http->hasPostVariable( 'SurveyRemoveCancel' ) )
{
    $Module->redirectTo( '/survey/list' );
}
else
{
    $tpl = eZTemplate::factory();

    $tpl->setVariable( 'survey', $survey );

    $Result = array();
    $Result['content'] = $tpl->fetch( 'design:survey/remove.tpl' );
    $Result['path'] = array( array( 'url' => '/survey/list',
                                    'text' => ezpI18n::tr( 'survey', 'Survey' ) ),
                             array( 'url' => false,
                                    'text' => ezpI18n::tr( 'survey', 'Remove' ) ) );
}

?>
