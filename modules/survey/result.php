<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

$Module = $Params['Module'];
// $surveyID = $Params['SurveyID'];
$contentObjectID = $Params['ContentObjectID'];
$contentClassAttributeID = $Params['ContentClassAttributeID'];
$languageCode = $Params['LanguageCode'];

$count = 0;
$survey = eZSurvey::fetchByObjectInfo( $contentObjectID, $contentClassAttributeID, $languageCode );
if ( !is_object( $survey ) )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

$surveyID = $survey->attribute( 'id' );


// $survey = eZSurvey::fetch( $surveyID );

if ( !$survey )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

$surveyList = $survey->fetchQuestionList();
$count = eZSurveyResult::fetchResult( $contentObjectID, $contentClassAttributeID, $languageCode );

$tpl = eZTemplate::factory();

$tpl->setVariable( 'contentobject_id', $contentObjectID );
$tpl->setVariable( 'contentclassattribute_id', $contentClassAttributeID );
$tpl->setVariable( 'language_code', $languageCode );

$tpl->setVariable( 'survey_questions', $surveyList );
$tpl->setVariable( 'survey_metadata', array() );
$tpl->setVariable( 'count', $count );

$Result = array();

$Result['left_menu'] = 'design:parts/survey/menu.tpl';
$Result['content'] = $tpl->fetch( 'design:survey/result.tpl' );
$Result['path'] = array( array( 'url' => '/survey/list',
                                'text' => ezpI18n::tr( 'survey', 'Survey' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'survey', 'Result overview' ) ) );

?>
