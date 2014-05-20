<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

$Module = $Params['Module'];
$contentObjectID = $Params['ContentObjectID'];
$contentClassAttributeID = $Params['ContentClassAttributeID'];
$languageCode = $Params['LanguageCode'];
$surveyResultID = $Params['SurveyResultID'];
$offset = $Params['Offset'];
if ( !$offset )
    $offset = 0;
$limit = 1;
$viewParameters['offset'] = $offset;

$http = eZHTTPTool::instance();
if ( $http->hasPostVariable( 'ExportCSVButton' ) )
{
    $href = 'survey/export/' . $contentObjectID . '/' . $contentClassAttributeID . '/' . $languageCode;
    $status = eZURI::transformURI( $href );
    if ( $status === true )
    {
        $http->redirect( $href );
    }
}

if ( is_numeric( $surveyResultID ) )
{
    $surveyResult = eZSurveyResult::fetch( $surveyResultID );
    if ( get_class( $surveyResult ) != 'eZSurveyResult' )
    {
        return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
    }
    $countListArray = eZSurveyResult::fetchSurveyResultCount( $contentObjectID, $contentClassAttributeID, $languageCode );
    $countList = $countListArray['result'];
}
else
{
    $resultList = eZSurveyResult::fetchResultArray( $contentObjectID, $contentClassAttributeID, $languageCode, $offset, $limit, $countList );
    if ( count( $resultList ) < 1 )
    {
        return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
    }
    $surveyResult = $resultList[0];
}
$surveyID = $surveyResult->attribute( 'survey_id' );

$survey = eZSurvey::fetch( $surveyID );
if ( !$survey )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$surveyList = $survey->fetchQuestionList();
// $countList = $survey->resultCount();

$tpl = eZTemplate::factory();

$tpl->setVariable( 'contentobject_id', $contentObjectID );
$tpl->setVariable( 'contentclassattribute_id', $contentClassAttributeID );
$tpl->setVariable( 'language_code', $languageCode );

$tpl->setVariable( 'survey', $survey );
$tpl->setVariable( 'survey_questions', $surveyList );
$tpl->setVariable( 'survey_metadata', array() );
$tpl->setVariable( 'result_id', $surveyResult->attribute( 'id' ) );
$tpl->setVariable( 'survey_user_id', $surveyResult->attribute( 'user_id' ) );
$tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'count', $countList );

$resultURL = false;

$Result = array();
$Result['left_menu'] = 'design:parts/survey/menu.tpl';
$Result['content'] = $tpl->fetch( 'design:survey/rview.tpl' );
$Result['path'] = array( array( 'url' => '/survey/list',
                                'text' => ezpI18n::tr( 'survey', 'Survey' ) ),
                         array( 'url' => 'survey/result/' . $contentObjectID . '/' . $contentClassAttributeID . '/' . $languageCode,
                                'text' => ezpI18n::tr( 'survey', 'Result overview' ) ),
                         array( 'url' => 'survey/result_list/' . $contentObjectID . '/' . $contentClassAttributeID . '/' . $languageCode,
                                'text' => ezpI18n::tr( 'survey', 'All' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'survey', 'Survey result' ) ) );
?>
