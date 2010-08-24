<?php
//
// Created on: <02-Apr-2004 00:00:00 Jan Kudlicka>
//
// Copyright (C) 1999-2010 eZ Systems AS. All rights reserved.
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

/*! \file ezsurveytextentry.php
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



eZSurveyQuestion::registerQuestionType( ezi18n( 'survey', 'Text Entry' ), 'TextEntry' );

?>
