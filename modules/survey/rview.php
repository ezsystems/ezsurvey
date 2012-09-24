<?php
//
// Created on: <02-Apr-2004 00:00:00 Jan Kudlicka>
//
// Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
//
// This source file is part of the eZ Publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE.GPL included in
// the packaging of this file.
//
// Licencees holding valid "eZ Publish professional licences" may use this
// file in accordance with the "eZ Publish professional licence" Agreement
// provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ Publish professional licence" is available at
// http://ez.no/products/licences/professional/. For pricing of this licence
// please contact us via e-mail to licence@ez.no. Further contact
// information is available at http://ez.no/home/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*! \file rview.php
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
