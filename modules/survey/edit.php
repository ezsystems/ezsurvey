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

if ( $http->hasPostVariable( 'SurveyDiscardButton' ) )
{
    $Module->redirectTo( '/survey/list' );
    return;
}

$surveyID = $Params['SurveyID'];
$survey = eZSurvey::fetch( $surveyID );

if ( !$survey )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

$surveyList =& $survey->fetchQuestionList();

$validation = array();

$survey->processEditActions( $validation );
if ( $http->hasPostVariable( 'SurveyPublishButton' ) && $validation['error'] == false && $validation['warning'] == false )
{
    $survey->setAttribute( 'published', 1 );
    $survey->storeAll();
    eZContentObject::expireAllCache();
    $Module->redirectTo( '/survey/list' );
    return;
}
else
    $survey->sync();

if ( $http->hasPostVariable( 'SurveyPreviewButton' ) && $validation['error'] == false && $validation['warning'] == false )
    $Module->redirectTo( '/survey/preview/'.$surveyID );

foreach ( $surveyList as $question )
{
    if ( $http->hasPostVariable( 'SurveyQuestionCopy_' . $question->attribute( 'id' ) . '_x' ) )
    {
        $question->cloneQuestion( $surveyID );
        $survey->QuestionList = null;  // Clear the cached list  TODO: cleanup
        $surveyList =& $survey->fetchQuestionList();
        $survey->reOrder();
        break;
    }
}

$tpl = eZTemplate::factory();

$tpl->setVariable( 'survey', $survey );
$tpl->setVariable( 'survey_questions', $surveyList );
$tpl->setVariable( 'survey_validation', $validation );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:survey/edit.tpl' );
$Result['path'] = array( array( 'url' => '/survey/list',
                                'text' => ezpI18n::tr( 'survey', 'Survey' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'survey', 'Edit' ) ) );
?>
