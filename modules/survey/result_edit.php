<?php
//
// Created on: <28-Jun-2004 15:00:00 kk>
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

/*! \file result_list.php
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
