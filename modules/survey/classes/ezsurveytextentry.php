<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

class eZSurveyTextEntry extends eZSurveyEntry
{
    function eZSurveyTextEntry( $row = false )
    {
        if ( !isset( $row['num'] ) )
            $row['num'] = 70;
        if ( !isset( $row['num2'] ) )
            $row['num2'] = 1;
        if ( !isset( $row['mandatory'] ) )
            $row['mandatory'] = 1;
        $row['type'] = 'TextEntry';
        $this->eZSurveyEntry( $row );
    }

    static function definition()
    {
        $def = parent::definition();
        $def['function_attributes']['default_answers'] = 'textDefaultAnswers';
        return $def;
    }

    function textDefaultAnswers()
    {
        $value = $this->userAttributeList();
        return $value;
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
        $user = eZUser::instance();
        $value = $this->Default;
        if ( $user->isLoggedIn() === true )
        {
            switch ( $this->Text3 )
            {
                case "user_email":
                {
                    $value = $this->userEmail();
                } break;

                case "user_name":
                {
                    $value = $this->userName();
                } break;

                default:
                {
                    $value = $this->defaultUserValue();
                }
            }
        }

        return $value;
    }

    private function defaultUserValue()
    {
        $value = false;
        $valueArray = explode( '_', $this->Text3, 2 );
        if ( isset( $valueArray[0] ) and
             isset( $valueArray[1] ) and
             $valueArray[0] == 'userobject' )
        {
            $identifier = $valueArray[1];
            $user = eZUser::currentUser();
            $dataMap = $user->attribute( 'contentobject' )->attribute( 'data_map' );
            if ( isset( $dataMap[$identifier] ) )
            {
                $value = $dataMap[$identifier]->attribute( 'content' );
            }
        }
        return $value;
    }

    private function userName()
    {
        $value = false;
        $user = eZUser::currentUser();
        if ( get_class( $user ) == 'eZUser' and
             $user->isLoggedIn() === true )
        {
            $contentObject = $user->attribute( 'contentobject' );
            if ( get_class( $contentObject ) == 'eZContentObject' )
            {
                $value = $contentObject->attribute( 'name' );
            }
        }
        return $value;
    }

    private function userEmail()
    {
        $value = false;
        $user = eZUser::currentUser();
        if ( get_class( $user ) == 'eZUser' and
             $user->isLoggedIn() === true )
        {
            $value = $user->attribute( 'email' );
        }
        return $value;
    }
}



eZSurveyQuestion::registerQuestionType( ezpI18n::tr( 'survey', 'Text Entry' ), 'TextEntry' );

?>
