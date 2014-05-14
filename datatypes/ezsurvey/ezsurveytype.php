<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

/*!
  \class eZSurveyType ezsurveytype.php
  \brief The class eZSurveyType does

*/

class eZSurveyType extends eZDataType
{
    const DATA_TYPE_STRING = "ezsurvey";
    const CONTENT_VALUE = 'data_int';
    const PREFIX_ATTRIBUTE = 'ContentObjectAttribute';
    const CONTENT_CLASS_VALUE = 'data_int1';

    /*!
     Constructor
    */
    function eZSurveyType()
    {
        $this->eZDataType( self::DATA_TYPE_STRING, ezpI18n::tr( 'ezsurvey/datatypes', 'Survey', 'Datatype name' ) );
    }

    /*!
     \reimp
     Receive the custom actions for the datatype.
    */
    function customClassAttributeHTTPAction( $http, $action, $contentClassAttribute )
    {
    }

    /*!
     \reimp
     Update the content in the surveydatatype.
    */
    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
    }


    /*!
     Returns the content data for the given content class attribute.
    */
    function classAttributeContent( $classAttribute )
    {
    }


    /*!
     Initializes the class attribute with some data.
     \note Default implementation does nothing.
    */
    function initializeClassAttribute( $classAttribute )
    {
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return true;
    }

    /*!
     Initializes the object attribute with the survey data.
    */
    function initializeObjectAttribute( $objectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
    }

    /*!
      Initializes the the survey when a new contentobject and version is created.
    */
    function postInitializeObjectAttribute( $objectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( !$currentVersion )
        {
            $newSurvey = new eZSurvey();
            $newSurvey->setAttribute( 'contentobject_id', $objectAttribute->attribute( "contentobject_id" ) );
            $newSurvey->setAttribute( 'contentobjectattribute_id', $objectAttribute->attribute( 'id' ) );
            $newSurvey->setAttribute( 'contentobjectattribute_version', $objectAttribute->attribute( "version" ) );
            $newSurvey->setAttribute( 'contentclassattribute_id', $objectAttribute->attribute( "contentclassattribute_id" ) );
            $newSurvey->setAttribute( 'language_code', $objectAttribute->attribute( "language_code" ) );
            $newSurvey->store();

            $objectAttribute->setAttribute( self::CONTENT_VALUE, $newSurvey->attribute( 'id' ) );
            $objectAttribute->sync();
        }
        else
        {
            $resetOriginalQuestionID = false;

            $contentObjectID = $objectAttribute->attribute( 'contentobject_id' );
            $originalContentObjectID = $originalContentObjectAttribute->attribute( 'contentobject_id' );

            $languageCode = $objectAttribute->attribute( 'language_code' );
            $originalLanguageCode = $originalContentObjectAttribute->attribute( 'language_code' );

            if ( ( $contentObjectID != $originalContentObjectID ) or
                 ( $languageCode != $originalLanguageCode ) )
            {
                $resetOriginalQuestionID = true;
            }

            $surveyID = $objectAttribute->attribute( self::CONTENT_VALUE );
            $survey = $this->fetchSurveyByID( $surveyID, 'initializeObjectAttribute' );
            $clonedSurvey = $survey->cloneSurvey( $resetOriginalQuestionID );
            $clonedSurvey->setAttribute( 'contentobject_id', $objectAttribute->attribute( 'contentobject_id' ) );
            $clonedSurvey->setAttribute( 'contentobjectattribute_id', $objectAttribute->attribute( 'id' ) );
            $clonedSurvey->setAttribute( 'contentobjectattribute_version', $objectAttribute->attribute( "version" ) );
            $clonedSurvey->setAttribute( 'contentclassattribute_id', $objectAttribute->attribute( "contentclassattribute_id" ) );
            $clonedSurvey->setAttribute( 'language_code', $objectAttribute->attribute( "language_code" ) );
            $clonedSurvey->setAttribute( 'published', 1 );
            $clonedSurvey->store();

            $objectAttribute->setAttribute( self::CONTENT_VALUE, $clonedSurvey->attribute( 'id' ) );
            $objectAttribute->sync();
        }
    }


    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $isValid = eZInputValidator::STATE_ACCEPTED;
        $validation = array();
        $surveyID = $contentObjectAttribute->attribute( self::CONTENT_VALUE );
        $survey = $this->fetchSurveyByID( $surveyID );
        if ( is_object( $survey ) )
        {
            $params = array( 'prefix_attribute' => self::PREFIX_ATTRIBUTE,
                             'contentobjectattribute_id' => $contentObjectAttribute->attribute( 'id' ) );
            $status = $survey->validateEditActions( $validation, $params );
        }

        if ( isset( $validation['error'] ) and $validation['error'] === true )
        {
            $isValid = eZInputValidator::STATE_INVALID;
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'Missing survey input.' ) );
            $contentObjectAttribute->setHasValidationError();
        }
        return $isValid;
    }

    /*!
     Returns the content data for the given content object attribute.
    */
    function objectAttributeContent( $objectAttribute )
    {
        $surveyID = $objectAttribute->attribute( self::CONTENT_VALUE );
        $content = false;
        $survey = $this->fetchSurveyByID( $surveyID, 'objectAttributeContent' );
        if ( get_class( $survey ) == 'eZSurvey' )
        {
            $user = eZUser::currentUser();
            // Check if the view parameters are set.
            $http = eZHTTPTool::instance();
            $postViewAction = self::PREFIX_ATTRIBUTE . '_ezsurvey_id_view_mode_' . $objectAttribute->attribute( 'id' );
            if ( $http->hasPostVariable( $postViewAction ) )
            {
                $validation = array();
                $status = $this->processViewActions( $objectAttribute, $survey, $validation );
                $content = array( 'survey' => $survey );
                $content['survey_validation'] = $validation;
            }
            else
            {
                $content = array( 'survey' => $survey );
                if ( $objectAttribute->hasValidationError() )
                {
                    $validation = array();
                    $params = array( 'prefix_attribute' => self::PREFIX_ATTRIBUTE,
                                     'contentobjectattribute_id' => $objectAttribute->attribute( 'id' ) );
                    $status = $survey->validateEditActions( $validation, $params );
                    $content['survey_validation'] = $validation;
                    $content['preview'] = false;
                }
                else
                {
                    $content['survey_validation'] = array();
                }

                $content['last_new_question_type'] = $survey->lastNewQuestionType();
            }

            if ( $survey->attribute( 'one_answer' ) == 1 and $user->isLoggedIn() === false )
            {
                $content['survey_validation']['one_answer_need_login'] = true;
            }
            else if ( $survey->attribute( 'one_answer' ) == 1 )
            {
                $user = eZUser::currentUser();

                $contentObjectID = $objectAttribute->attribute( 'contentobject_id' );
                $contentClassAttributeID = $objectAttribute->attribute( 'contentclassattribute_id' );
                $languageCode = $objectAttribute->attribute( 'language_code' );

                $content['survey_validation']['one_answer_count'] = eZSurveyResult::exist( $surveyID,
                                                                                           $user->attribute( 'contentobject_id' ),
                                                                                           $contentObjectID,
                                                                                           $contentClassAttributeID,
                                                                                           $languageCode );
            }
        }
        $content = eZSurvey::setGlobalSurveyContent( $content );
        return $content;
    }

    /*!
      Process the view actions.
    */
    function processViewActions( $objectAttribute, &$survey, &$validation )
    {
        $http = eZHTTPTool::instance();
        $actionContinue = false;
        $postNodeID = self::PREFIX_ATTRIBUTE . '_ezsurvey_node_id_' . $objectAttribute->attribute( 'id' );
        $postContentObjectAttributeID = self::PREFIX_ATTRIBUTE . '_ezsurvey_contentobjectattribute_id_' . $objectAttribute->attribute( 'id' );
        $postSurveyID = self::PREFIX_ATTRIBUTE . '_ezsurvey_id_' . $objectAttribute->attribute( 'id' );
        $continueViewActions = true;
        if ( $survey->attribute( 'one_answer' ) == 1 )
        {
            $user = eZUser::currentUser();
            if ( $user->isLoggedIn() === true )
            {
                $contentObjectID = $objectAttribute->attribute( 'contentobject_id' );
                $contentClassAttributeID = $objectAttribute->attribute( 'contentclassattribute_id' );
                $languageCode = $objectAttribute->attribute( 'language_code' );
                $surveyID = $survey->attribute( 'id' );

                $exist = eZSurveyResult::exist( $surveyID, $user->attribute( 'contentobject_id' ), $contentObjectID, $contentClassAttributeID, $languageCode );
                if ( $exist === true )
                {
                    $continueViewActions = false;
                }
            }
            else
            {
                $continueViewActions = false;
            }
        }

        if ( $continueViewActions === true )
        {
            if ( $http->hasPostVariable( $postNodeID ) and
                 $http->hasPostVariable( $postContentObjectAttributeID ) and
                 $http->hasPostVariable( $postSurveyID ) )
            {
                $surveyID = $http->postVariable( $postSurveyID );
                $contentObjectAttributeID = $http->postVariable( $postContentObjectAttributeID );
                $nodeID = $http->postVariable( $postNodeID );
                $node = eZContentObjectTreeNode::fetch( $nodeID );
                if ( get_class( $node ) == 'eZContentObjectTreeNode' and
                     $node->canRead() === true )
                {
                    // verify that our attribute is included in this node.
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
                                           'eZSurveyType::processViewActions' );
                }
                else
                {
                    eZDebug::writeWarning( "node with ID: " . $nodeID . " does not exist.",
                                           'eZSurveyType::processViewActions' );
                    return false;
                }
            }
            else
            {
                eZDebug::writeWarning( "All the postvariables $postNodeID, $postContentObjectAttributeID and $postSurveyID need to be supplied.",
                                       'eZSurveyType::processViewActions' );
                return false;
            }

            $nodeID = $http->postVariable( $postNodeID );
            $node = eZContentObjectTreeNode::fetch( $nodeID );

            if ( $actionContinue === true )
            {
                $survey = eZSurvey::fetch( $surveyID );
                $status = $survey->validateContentObjectAttributeID( $contentObjectAttributeID );

                if ( !$survey or !$survey->published() or !$survey->enabled() or !$survey->valid() )
                {
                    eZDebug::writeWarning( 'Survey is not valid', 'eZSurveyType::processViewActions' );
                    return;
                }

                $params = array( 'prefix_attribute' => self::PREFIX_ATTRIBUTE,
                                 'contentobjectattribute_id' => $contentObjectAttributeID );

                $variableArray = $survey->processViewActions( $validation, $params );

                $postSurveyStoreButton = self::PREFIX_ATTRIBUTE . '_ezsurvey_store_button_' . $contentObjectAttributeID;

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
                if ( $http->hasPostVariable( $postSurveyStoreButton ) && $validation['error'] == false )
                {

                    $result->storeResult( $params );

                    $postReceiverID = self::PREFIX_ATTRIBUTE . '_ezsurvey_receiver_id_' . $contentObjectAttributeID;
                    if ( $http->hasPostVariable( $postReceiverID ) and
                         $questionList = $survey->fetchQuestionList() and
                         $postReceiverQuestionID = $http->postVariable( $postReceiverID ) and
                         isset( $questionList[$postReceiverQuestionID] ) )
                    {
                        $mailTo = $questionList[$postReceiverQuestionID]->answer();

                        $emailSenderList = explode( '_', $questionList[$postReceiverQuestionID]->attribute( 'text3' ) );
                        if ( isset( $emailSenderList[1] ) and
                             $emailSenderID = $emailSenderList[1] and
                             is_numeric( $emailSenderID ) and
                             $emailSenderID > 0 and
                             isset( $questionList[$emailSenderID] ) and
                             $senderQuestion = $questionList[$emailSenderID] and
                             $senderQuestion->attribute( 'type' ) == 'EmailEntry' and
                             eZMail::validate( $senderQuestion->attribute( 'answer' ) ) )
                        {
                            $emailSender = $senderQuestion->attribute( 'answer' );
                        }
                        else
                        {
                            $ini = eZINI::instance();
                            $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
                            if ( !$emailSender )
                            {
                                $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
                            }
                        }

                        $tpl_email = eZTemplate::factory();

                        $tpl_email->setVariable( 'survey', $survey );
                        $tpl_email->setVariable( 'survey_questions', $questionList );
                        $tpl_email->setVariable( 'survey_node', $node );

                        $templateResult = $tpl_email->fetch( 'design:survey/mail.tpl' );
                        $subject = $tpl_email->variable( 'subject' );

                        $mail = new eZMail();
                        $mail->setSenderText( $emailSender );
                        $mail->setReceiver( $mailTo );
                        $mail->setSubject( $subject );
                        $mail->setBody( $templateResult );

                        $mailResult = eZMailTransport::send( $mail );

                    }
                    $survey->executeBeforeLastRedirect( $node );

                    $href = trim( $survey->attribute( 'redirect_submit' ) );
                    $module = $GLOBALS['eZRequestedModule'];
                    if ( $module instanceof eZModule )
                    {
                        if ( trim( $href ) != "" )
                        {
                            if ( preg_match( "/^http:\/\/.+/", $href ) )
                            {
                                $module->redirectTo( $href );
                            }
                            else
                            {
                                $originalHref = $href;
                                $status = eZURI::transformURI( $href );
                                if ( $status === true )
                                {
                                    // Need to keep the original href, since it's
                                    // already changed here.
                                    $module->redirectTo( $originalHref );
                                }
                                else
                                {
                                    $http->redirect( $href );
                                }
                            }
                        }
                    }
                }
                else if ( $validation['error'] == true and $survey->attribute( 'persistent' ) == true )
                {
                    // Fix prevous results.
                    $validation['post_variables']['active'] = true;
                    $validation['post_variables']['variables'] = $variableArray;
                }
            }
        }
        else
        {
            eZDebug::writeWarning( 'Answer for survey with userid: ' . $user->id() . ' does already exist', 'eZSurveyType::processViewActions' );
            $validation['one_answer']['warning'] = true;
        }
    }

    /*!
     Returns a survey object by a given survey id.
     \return a survey object or false if the survey does not exist.
    */
    function fetchSurveyByID( $surveyID, $functionName = 'fetchSurveyByID' )
    {
        $survey = false;
        if ( is_numeric( $surveyID ) and $surveyID > 0 )
        {
            $survey = eZSurvey::fetch( $surveyID );
            if ( !is_object( $survey ) )
            {
                eZDebug::writeWarning(  "Survey is not valid: " . var_export( $survey, true ),
                                        'eZSurveyType::' . $functionName );
            }
        }
        else
        {
            eZDebug::writeWarning(  "Survey id is not numeric or above 0: " . var_export( $surveyID, true ),
                                    'eZSurveyType::' . $functionName );
        }
        return $survey;
    }

    /*!
     Fetches the HTTP input for the content object attribute.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $objectAttribute )
    {
        $isValid = true;
        $validation = array();
        $surveyID = $objectAttribute->attribute( self::CONTENT_VALUE );
        $survey = $this->fetchSurveyByID( $surveyID );
        if ( is_object( $survey ) )
        {
            $params = array( 'prefix_attribute' => self::PREFIX_ATTRIBUTE,
                             'contentobjectattribute_id' => $objectAttribute->attribute( 'id' ) );
            $isValid = $survey->processEditActions( $validation, $params );
            $survey = $survey->sync();
        }
        return $isValid;
    }


    /*!
     Executes a custom action for an object attribute which was defined on the web page.
    */
    function customObjectAttributeHTTPAction( $http, $action, $objectAttribute, $parameters )
    {
        $surveyID = $objectAttribute->attribute( self::CONTENT_VALUE );
        $survey = $this->fetchSurveyByID( $surveyID );
        if ( is_object( $survey ) )
        {
           $status = $survey->handleAttributeHTTPAction( $http, $action, $objectAttribute, $parameters );
           if ( $status == true )
           {
               $survey->sync();
           }
        }
    }

    /*!
     Clean up stored object attribute
     \note Default implementation does nothing.
    */
    function deleteStoredObjectAttribute( $objectAttribute, $version = null )
    {
        $surveyID = $objectAttribute->attribute( self::CONTENT_VALUE );
        $survey = $this->fetchSurveyByID( $surveyID );
        if ( is_object( $survey ) )
        {
            $survey->remove();
        }
    }

    /*!
      Set the survey itself to published.
     \return True if the value was stored correctly.
    */
    function onPublish( $contentObjectAttribute, $contentObject, $publishedNodes )
    {
        $retValue = false;
        $surveyID = $contentObjectAttribute->attribute( self::CONTENT_VALUE );
        $survey = $this->fetchSurveyByID( $surveyID );
        if ( is_object( $survey ) )
        {
            $survey->setAttribute( 'published', 1 );
            $survey->store();
            $retValue = true;
        }
        else
        {
            eZDebug::writeError( "Survey ID $surveyID did not exist.", 'eZSurveyType::onPublish' );
        }
        return $retValue;
    }

    /*!
     Returns the meta data used for storing search indices.
    */
    function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    /*!
     Returns the text.
    */
    function title( $contentObjectAttribute, $name = null )
    {
    }

    /*!
     \reimp
    */
    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $value = $classAttribute->attribute( self::CONTENT_CLASS_VALUE );

        $dom = $attributeParametersNode->ownerDocument;
        $defaultValueNode = $dom->createElement( 'value', $value );
        $attributeParametersNode->appendChild( $defaultValueNode );
    }

    /*!
     \reimp
    */
    function unserializeContentClassAttribute( $classAttribute, $attrjeppibuteNode, $attributeParametersNode )
    {
        $value = $attributeParametersNode->getElementsByTagName( 'value' )->item( 0 )->textContent;
        $classAttribute->setAttribute( self::CONTENT_CLASS_VALUE, $value );
    }

}

eZDataType::register( eZSurveyType::DATA_TYPE_STRING, "eZSurveyType" );

?>
