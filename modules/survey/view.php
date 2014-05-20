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
