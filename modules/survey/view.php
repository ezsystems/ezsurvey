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

/*! \file view.php
*/

$http = eZHTTPTool::instance();

$Module = $Params['Module'];
$surveyID = $Params['SurveyID'];

$survey = eZSurvey::fetch( $surveyID );

if ( !$survey || !$survey->published() || !$survey->enabled() || !$survey->valid() )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

if ( $http->hasPostVariable( 'SurveyCancelButton' ) )
{
    $Module->redirectTo( $survey->attribute( 'redirect_cancel' ) );
    return;
}

$validation = array();
$survey->processViewActions( $validation );

if ( $http->hasPostVariable( 'SurveyStoreButton' ) && $validation['error'] == false )
{
    $user = eZUser::currentUser();
    if ( $survey->attribute( 'persistent' ) )
    {
        $result = eZSurveyResult::instance( $surveyID, $user->id() );
    }
    else
    {
        $result = eZSurveyResult::instance( $surveyID );
    }

    $result->setAttribute( 'user_id', $user->id() );

    $http = eZHTTPTool::instance();
    $sessionID = $http->sessionID();

    $result->setAttribute( 'user_session_id', $sessionID );

    $result->storeResult();

    if ( $http->hasPostVariable( 'SurveyReceiverID' ) )
    {
        $surveyList = $survey->fetchQuestionList();
        $mailTo = $surveyList[$http->postVariable( 'SurveyReceiverID' )]->answer();

        $tpl_email = eZTemplate::factory();

        $tpl_email->setVariable( 'survey', $survey );
        $tpl_email->setVariable( 'survey_questions', $surveyList );

        $templateResult = $tpl_email->fetch( 'design:survey/mail.tpl' );
        $subject = $tpl_email->variable( 'subject' );
        $mail = new eZMail();
        $ini = eZINI::instance();
        $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
        if ( !$emailSender )
            $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
        $mail->setSenderText( $emailSender );
        $mail->setReceiver( $mailTo );
        $mail->setSubject( $subject );
        $mail->setBody( $templateResult );

        $mailResult = eZMailTransport::send( $mail );
    }
    $Module->redirectTo( $survey->attribute( 'redirect_submit' ) );
}

$res = eZTemplateDesignResource::instance();
$res->setKeys( array( array( 'survey', $surveyID ) ) );

$tpl = eZTemplate::factory();

$tpl->setVariable( 'preview', false );
$tpl->setVariable( 'survey', $survey );
$tpl->setVariable( 'survey_validation', $validation );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:survey/view.tpl' );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'survey', 'Survey' ) ) );

?>
