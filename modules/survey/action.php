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

$actionContinue = false;
if ( $http->hasPostVariable( 'NodeID' ) and
     $http->hasPostVariable( 'ContentObjectAttributeID' ) and
     $http->hasPostVariable( 'SurveyID' ) )
{
    $surveyID = $http->postVariable( 'SurveyID' );
    $contentObjectAttributeID = $http->postVariable( 'ContentObjectAttributeID' );
    $nodeID = $http->postVariable( 'NodeID' );
    $node = eZContentObjectTreeNode::fetch( $nodeID );
    if ( get_class( $node ) == 'eZContentObjectTreeNode' and
         $node->canRead() === true )
    {
        $dataMap = $node->dataMap();
        foreach ( $dataMap as $attribute )
        {
            $attributeObjectID = $attribute->attribute( 'id' );
            if ( $attributeObjectID == $contentObjectAttributeID )
            {
                $actionContinue = true;
                break;
            }
        }
    }
    else if ( get_class( $node ) == 'eZContentObjectTreeNode' )
    {
        eZDebug::writeWarning( "Not enough permissions to read node with ID: " . $nodeID . ".",
                               'action.php' );
        $Module->redirectTo( $node->attribute( 'url_alias' ) );
    }
    else
    {
        eZDebug::writeWarning( "node with ID: " . $nodeID . " does not exist.",
                               'action.php' );
        return;
    }
}
else
{
    eZDebug::writeWarning( "All the postvariables NodeID, ContentObjectAttributeID and SurveyID need to be supplied.",
                           'action.php' );
    return;
}

$nodeID = $http->postVariable( 'NodeID' );
$node = eZContentObjectTreeNode::fetch( $nodeID );
if ( get_class( $node ) == 'eZContentObjectTreeNode' )
{
    $Module->redirectTo( $node->attribute( 'url_alias' ) );
}

if ( $actionContinue === true )
{
    $survey = eZSurvey::fetch( $surveyID );
    $status = $survey->validateContentObjectAttributeID( $contentObjectAttributeID );

    if ( !$survey or !$survey->published() or !$survey->enabled() or !$survey->valid() )
    {
        return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
    }

    if ( $http->hasPostVariable( 'SurveyCancelButton' ) )
    {
        $Module->redirectTo( $survey->attribute( 'redirect_cancel' ) );
        return;
    }

    $params = array( 'prefix_attribute' => eZSurveyType::PREFIX_ATTRIBUTE,
                     'contentobjectattribute_id' => $contentObjectAttributeID );

    $validation = array();
    $survey->processViewActions( $validation, $params );

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

//         if ( $http->hasPostVariable( 'SurveyReceiverID' ) )
//         {
//             $surveyList = $survey->fetchQuestionList();
//             $mailTo = $surveyList[$http->postVariable( 'SurveyReceiverID' )]->answer();

//             $tpl_email = eZTemplate::factory();

//             $tpl_email->setVariable( 'survey', $survey );
//             $tpl_email->setVariable( 'survey_questions', $surveyList );

//             $templateResult = $tpl_email->fetch( 'design:survey/mail.tpl' );
//             $subject = $tpl_email->variable( 'subject' );
//             $mail = new eZMail();
//             $ini = eZINI::instance();
//             $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
//             if ( !$emailSender )
//                 $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
//             $mail->setSenderText( $emailSender );
//             $mail->setReceiver( $mailTo );
//             $mail->setSubject( $subject );
//             $mail->setBody( $templateResult );

//             $mailResult = eZMailTransport::send( $mail );
//         }
        $Module->redirectTo( $survey->attribute( 'redirect_submit' ) );
    }

//     $res = eZTemplateDesignResource::instance();
//     $res->setKeys( array( array( 'survey', $surveyID ) ) );

//     $tpl = eZTemplate::factory();

//     $tpl->setVariable( 'preview', false );
//     $tpl->setVariable( 'survey', $survey );
//     $tpl->setVariable( 'survey_validation', $validation );

//     $Result = array();
//     $Result['content'] = $tpl->fetch( 'design:survey/action.tpl' );
//     $Result['path'] = array( array( 'url' => false,
//                                     'text' => ezpI18n::tr( 'survey', 'Survey' ) ) );
}
if ( is_object( $Module ) )
$Module->redirectTo( $node->attribute( 'url_alias' ) );

?>
