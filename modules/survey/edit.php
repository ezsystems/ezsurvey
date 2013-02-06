<?php
//
// Created on: <02-Apr-2004 00:00:00 Jan Kudlicka>
//
// Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
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

/*! \file edit.php
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
