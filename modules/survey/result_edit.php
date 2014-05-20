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
$resultID = $Params['ResultID'];

$surveyResult = eZSurveyResult::fetch( $resultID );
if ( !$surveyResult )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$survey = eZSurvey::fetch( $surveyResult->attribute( 'survey_id' ) );
if ( !$survey )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$validation = array();

$contentObjectID = $survey->attribute( 'contentobject_id' );
$contentObjectAttributeID = $survey->attribute( 'contentobjectattribute_id' );
$contentClassAttributeID = $survey->attribute( 'contentclassattribute_id' );
$languageCode = $survey->attribute( 'language_code' );

$params = array( 'prefix_attribute' => eZSurveyType::PREFIX_ATTRIBUTE,
                             'contentobjectattribute_id' => $contentObjectAttributeID );

$survey->processViewActions( $validation, $params );

if ( $http->hasPostVariable( 'SurveyStoreButton' ) && $validation['error'] == false )
{
    $result = eZSurveyResult::instance( $surveyResult->attribute( 'survey_id' ),
                                        $surveyResult->attribute( 'user_id' ) );
    $result->storeResult( $params );
    $Module->redirectTo( '/survey/result_list/' . $contentObjectID . '/' . $contentClassAttributeID . '/' . $languageCode );
}
else if ( $http->hasPostVariable( 'SurveyCancelButton' ) )
{
    $Module->redirectTo( '/survey/result_list/' . $contentObjectID . '/' . $contentClassAttributeID . '/' . $languageCode );
}

$tpl = eZTemplate::factory();

$tpl->setVariable( 'prefix_attribute', eZSurveyType::PREFIX_ATTRIBUTE );

$tpl->setVariable( 'contentobject_id', $contentObjectID );
$tpl->setVariable( 'contentobjectattribute_id', $contentObjectAttributeID );
$tpl->setVariable( 'contentclassattribute_id', $contentClassAttributeID );
$tpl->setVariable( 'language_code', $languageCode );

$tpl->setVariable( 'preview', false );
$tpl->setVariable( 'survey', $survey );
$tpl->setVariable( 'survey_result', $surveyResult );
$tpl->setVariable( 'survey_validation', $validation );

$Result = array();
$Result['left_menu'] = 'design:parts/survey/menu.tpl';
$Result['content'] = $tpl->fetch( 'design:survey/result_edit.tpl' );
$Result['path'] = array( array( 'url' => '/survey/list',
                                'text' => ezpI18n::tr( 'survey', 'Survey' ) ),
                         array( 'url' => 'survey/result/' . $contentObjectID . '/' . $contentClassAttributeID . '/' . $languageCode,
                                'text' => ezpI18n::tr( 'survey', 'Result overview' ) ),
                         array( 'url' => 'survey/result_list/' . $contentObjectID . '/' . $contentClassAttributeID . '/' . $languageCode,
                                'text' => ezpI18n::tr( 'survey', 'All' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'survey', 'Edit' ) ) );


?>
