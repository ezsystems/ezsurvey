<?php
//
// Created on: <02-Apr-2004 00:00:00 Jan Kudlicka>
//
// Copyright (C) 1999-2008 eZ Systems as. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE.GPL included in
// the packaging of this file.
//
// Licencees holding valid "eZ publish professional licences" may use this
// file in accordance with the "eZ publish professional licence" Agreement
// provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" is available at
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
            $row['num2'] = 10;
        if ( !isset( $row['mandatory'] ) )
            $row['mandatory'] = 1;
        $row['type'] = 'TextEntry';
        $this->eZSurveyEntry( $row );
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
        else if ( $this->Text3 == 'user_name' )
        {
            $user = eZUser::currentUser();
            if ( get_class( $user ) == 'eZUser' and
                 $user->isLoggedIn() === true )
            {
                $contentObject = $user->attribute( 'contentobject' );
                if ( get_class( $contentObject ) == 'eZContentObject' )
                {
                    return $contentObject->attribute( 'name' );
                }
            }
        }

        return $this->Default;
    }
}



eZSurveyQuestion::registerQuestionType( 'Text Entry', 'TextEntry' );

?>
