<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

class eZSurveyEmailEntry extends eZSurveyEntry
{
    function eZSurveyEmailEntry( $row = false )
    {
        if ( !isset( $row['mandatory'] ) )
            $row['mandatory'] = 1;
        $row['type'] = 'EmailEntry';
        $this->eZSurveyEntry( $row );
    }

    function processViewActions( &$validation, $params )
    {
        $http = eZHTTPTool::instance();

        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params['contentobjectattribute_id'];


        $postAnswer = $prefix . '_ezsurvey_answer_' . $this->ID . '_' . $attributeID;
        $answer = trim ( $http->postVariable( $postAnswer ) );
        if ( $this->attribute( 'mandatory' ) == 1 and strlen( $answer ) == 0 )
        {
            $validation['error'] = true;
            $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Please answer the question %number as well!', null,
                                                                  array( '%number' => $this->questionNumber() ) ),
                                             'question_number' => $this->questionNumber(),
                                             'code' => 'email_answer_question',
                                             'question' => $this );
        }
        else if ( strlen( $answer ) != 0 && !eZMail::validate( $answer ) )
        {
            $validation['error'] = true;
            $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Entered text in the question %number is not a valid email address!', null,
                                                                  array( '%number' => $this->questionNumber() ) ),
                                             'question_number' => $this->questionNumber(),
                                             'code' => 'email_email_not_valid',
                                             'question' => $this );
        }

        $this->setAnswer( $answer );
    }

    function answer()
    {
        if ( $this->Answer !== false )
            return $this->Answer;

        $http = eZHTTPTool::instance();
        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $postSurveyAnswer = $prefix . '_ezsurvey_answer_' . $this->ID . '_' . $this->contentObjectAttributeID();
        if ( $http->hasPostVariable( $postSurveyAnswer ) )
        {
            $surveyAnswer = $http->postVariable( $postSurveyAnswer );
            return $surveyAnswer;
        }

        if ( $this->Text3 == 'user_email' )
        {
            $user = eZUser::currentUser();
            if ( get_class( $user ) == 'eZUser' and
                 $user->isLoggedIn() === true )
            {
                return $user->attribute( 'email' );
            }
        }
        return $this->Default;
    }
}

eZSurveyQuestion::registerQuestionType( ezpI18n::tr( 'survey', 'Email Entry' ), 'EmailEntry' );

?>
