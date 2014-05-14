<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

class eZSurveyFeedbackField extends eZSurveyEntry
{
    private static $hasFeedbackQuestionList;
    private static $feedbackQuestionList;

    /*!
     Constructor
    */
    function __construct( $row = false )
    {
        $row['type'] = 'FeedbackField';
        if ( !isset( $row['num'] ) )
            $row['num'] = 1; // default value for bcc

        $this->eZSurveyEntry( $row );
    }

    static function definition()
    {
        $def = parent::definition();
        $def['function_attributes']['feedback_email_questions'] = 'emailQuestions';
        return $def;
    }

    function emailQuestions()
    {
        $survey = eZSurvey::fetch( $this->SurveyID );
        $questions = $this->feedbackQuestionList();

        $emailQuestionID = $this->attribute( 'num2' );
        $returnValue = array();
        foreach ( $questions as $question )
        {
            if ( $question->attribute( 'type' ) == 'EmailEntry' )
            {
                $originalID = $question->attribute( 'original_id' );
                $id = $question->attribute( 'id' );

                $value = $question->attribute( 'text' );
                $returnValue[$originalID] = $value . " (" . $id . ")";
            }
            else if ( $question->attribute( 'type' ) == 'gwNetaxept' )
            {
                $originalID = $question->attribute( 'original_id' );
                $id = $question->attribute( 'id' );

                $netaxeptContent = $question->attribute( 'netaxept_content' );
                $value = $netaxeptContent['label'];
                $returnValue[$originalID] = $value . " (" . $id . ")";
            }
        }

        return $returnValue;
    }

    private function validateEmail( $email )
    {
        $value = true;
        if ( !eZMail::validate( $email ) and
             $this->attribute( 'num2' ) == 0 )
        {
            $value = false;
        }

        if ( $this->attribute( 'mandatory' ) == 0 and trim( $email ) == '' )
        {
            $value = true;
        }

        return $value;
    }

    function processViewActions( &$validation, $params )
    {
        $variableArray = parent::processViewActions( $validation, $params );
        if ( $this->validateEmail( $variableArray['answer'] ) === false )
        {
            $validation['error'] = true;
            $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered text in the question %number is not a valid email address!', null,
                                              array( '%number' => $this->questionNumber() ) ),
                                                     'question_number' => $this->questionNumber(),
                                             'code' => 'feedbackfield_email_not_valid',
                                             'question' => $this );

        }

        $this->setAnswer( $this->fetchMailTo() );
        return $variableArray;
    }

    function answer()
    {
        return parent::answer();
    }

    private function fetchFeedbackSurvey()
    {
        if ( ( $survey = $this->survey( $this->SurveyID ) ) === false )
        {
            $survey = eZSurvey::fetch( $this->SurveyID );
        }
        return $survey;
    }

    /*!
      process the post actions from questions.
    */
    function processEditActions( &$validation, $params )
    {
        parent::processEditActions( $validation, $params );

        $http = eZHTTPTool::instance();
        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params['contentobjectattribute_id'];

        $postQuestionNum = $prefix . '_ezsurvey_question_' . $this->ID . '_num_' . $attributeID;
        if ( !$http->hasPostVariable( $postQuestionNum ) )
        {
            $this->setAttribute( 'num', 0 );
        }

        if ( $this->attribute( 'num2' ) != 0 )
        {
            $this->setAttribute( 'mandatory', 0 );
        }
    }

    /*!
      Return a postvariable based on question.
    */
    private function answerByQuestion( $question )
    {
        $http = eZHTTPTool::instance();
        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $question->contentObjectAttributeID();

        $postVariable = '';
        switch ( $question->Type )
        {
            case "EmailEntry":
            {
                $postVariable = $prefix . '_ezsurvey_answer_' . $question->ID . '_' . $attributeID;
            } break;

            case "gwNetaxept":
            {
                $postVariable = $prefix . '_ezsurvey_answer_' . $question->ID . '_email_' . $attributeID;
            } break;
        }

        $value = '';
        if ( $http->hasPostVariable( $postVariable ) )
        {
            $value = $http->postVariable( $postVariable );
        }
        return $value;

    }

    private function feedbackQuestionList()
    {
        if ( !isset( self::$feedbackQuestionList ) and
             $survey = $this->fetchFeedbackSurvey() and
             $survey instanceof eZSurvey )
        {
            self::$feedbackQuestionList = $survey->fetchQuestionList();
        }
        return self::$feedbackQuestionList;
    }

    private function questionByOriginalID( $id, $surveyList = false )
    {
        $value = false;

        if ( $id > 0 and is_array( $surveyList ) )
        {
            foreach ( $surveyList as $item )
            {
                if ( $item->attribute( 'original_id' ) == $id )
                {
                    $value = $item;
                    break;
                }
            }
        }
        return $value;
    }

    function questionNumberIterate( &$iterator )
    {
        $emailQuestionID = $this->attribute( 'num2' );
        if ( $emailQuestionID == 0 )
        {
            $this->QuestionNumber = $iterator++;
        }
    }

    private function fetchMailTo( $surveyList = false )
    {
        $emailQuestionID = $this->attribute( 'num2' );
        $surveyList = $this->feedbackQuestionList();

        if ( $emailQuestionID > 0 and is_array( $surveyList ) )
        {
            $found = false;
            foreach ( $surveyList as $item )
            {
                if ( $item->attribute( 'original_id' ) == $emailQuestionID )
                {
                    $mailTo = $this->answerByQuestion( $item );
                    if ( !eZMail::validate( $mailTo ) )
                    {
                        $mailTo = $this->answer();
                    }
                    $found = true;
                    break;
                }
            }

            if ( $found === false )
            {
                $mailTo = $this->answer();
            }
        }
        else
        {
            $mailTo = $this->answer();
        }
        return $mailTo;
    }

    public function executeBeforeLastRedirect( $node )
    {
        $survey = $this->fetchFeedbackSurvey();
        $surveyQuestions = $this->feedbackQuestionList();
        $mailTo = $this->fetchMailTo( $surveyQuestions );

        if ( $survey = $this->fetchFeedbackSurvey() and
             $survey instanceof eZSurvey and
             $surveyQuestions = $this->feedbackQuestionList() and
             $mailTo = $this->fetchMailTo( $surveyQuestions ) and
             eZMail::validate( $mailTo ) )
        {
            $tpl_email = eZTemplate::factory();

            $tpl_email->setVariable( 'intro', $this->Text2 );

            $tpl_email->setVariable( 'survey', $survey );
            $tpl_email->setVariable( 'survey_questions', $surveyQuestions );
            $tpl_email->setVariable( 'survey_node', $node );

            $templateResult = $tpl_email->fetch( 'design:survey/feedbackfield_mail.tpl' );
            if ( trim( $this->Text3 ) != '' )
            {
                $subject = $this->Text3;
            }
            else
            {
                $subject = $tpl_email->variable( 'subject' );
            }
            $mail = new eZMail();

            $ini = eZINI::instance();
            $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
            if ( !$emailSender )
                $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );

            $mail->setSenderText( $emailSender );
            $mail->setReceiver( $mailTo );
            $mail->setSubject( $subject );
            $mail->setBody( $templateResult );

            if ( $this->Num == 1 )
            {
                $adminReceiver = $ini->variable( 'MailSettings', 'AdminEmail' );
                $mail->addBcc( $adminReceiver );
            }

            $mailResult = eZMailTransport::send( $mail );
        }
    }

}

eZSurveyQuestion::registerQuestionType( ezpI18n::tr( 'survey', 'Feedback field' ), 'FeedbackField' );

?>
