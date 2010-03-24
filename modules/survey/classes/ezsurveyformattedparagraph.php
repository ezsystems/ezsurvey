<?php
//
// Created on: <10-Jun-2004 10:06:45 gl>
//
// Copyright (C) 1999-2008 eZ Systems AS. All rights reserved.
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

/*! \file ezsurveyformattedparagraph.php
*/

class eZSurveyFormattedParagraph extends eZSurveyQuestion
{
    function eZSurveyFormattedParagraph( $row = false )
    {
        $row['type'] = 'FormattedParagraph';
        $this->eZSurveyQuestion( $row );
    }

    /*!
     \reimp
    */
    static function definition()
    {
        return array_merge_recursive( array( 'function_attributes' => array( 'content' => 'xmlOutput' ) ),
                                      eZSurveyQuestion::definition() );
    }

    /*!
     Use the ezxhtml output parser to parse the xml input.

     \return xhtml
    */
    function xmlOutput()
    {
        $xmlObject = new eZXMLText( $this->attribute( 'text' ), null );
        $outputHandler = $xmlObject->attribute( 'output' );
        return $outputHandler->attribute( 'output_text' );
    }

    function canAnswer()
    {
        return false;
    }

    function questionNumberIterate( &$iterator )
    {
    }

    function processEditActions( &$validation, $params )
    {
        $http = eZHTTPTool::instance();

        if ( $http->hasPostVariable( 'SurveyQuestion_'.$this->ID.'_Text' ) &&
             $http->postVariable( 'SurveyQuestion_'.$this->ID.'_Text' ) != $this->Text )
        {
            $inputXML = $http->postVariable( 'SurveyQuestion_'.$this->ID.'_Text' );

            $xmlData = "<section xmlns:image='http://ez.no/namespaces/ezpublish3/image/' xmlns:xhtml='http://ez.no/namespaces/ezpublish3/xhtml/' xmlns:custom='http://ez.no/namespaces/ezpublish3/custom/' >";
            $xmlData .= "<paragraph>";
            $xmlData .= $inputXML;
            $xmlData .= "</paragraph>";
            $xmlData .= "</section>";

            $xmlObject = new eZXMLText( $inputXML, null );
            $inputHandler = $xmlObject->attribute( 'input' );
            $data =& $inputHandler->convertInput( $xmlData );
            $domString =& eZXMLTextType::domString( $data[0] );

            $domString = preg_replace( "#<paragraph> </paragraph>#", "<paragraph>&nbsp;</paragraph>", $domString );
            $domString = str_replace ( "<paragraph />" , "", $domString );
            $domString = str_replace ( "<line />" , "", $domString );
            $domString = str_replace ( "<paragraph></paragraph>" , "", $domString );
            $domString = preg_replace( "#<paragraph>&nbsp;</paragraph>#", "<paragraph />", $domString );
            $domString = preg_replace( "#<paragraph></paragraph>#", "", $domString );

            $domString = preg_replace( "#[\n]+#", "", $domString );
            $domString = preg_replace( "#&lt;/line&gt;#", "\n", $domString );
            $domString = preg_replace( "#&lt;paragraph&gt;#", "\n\n", $domString );

            $xml = new eZXML();
            $tmpDom = $xml->domTree( $domString, array( 'CharsetConversion' => false ) );
            $domString = eZXMLTextType::domString( $tmpDom );

            $this->setAttribute( 'text', $domString );
        }

        if ( $http->hasPostVariable( 'SurveyQuestion_' . $this->ID . '_Text2' ) &&
             $http->postVariable( 'SurveyQuestion_' . $this->ID . '_Text2' ) != $this->Text2 )
        {
            $this->setAttribute( 'text2', $http->postVariable( 'SurveyQuestion_' . $this->ID . '_Text2' ) );
        }

        if ( $http->hasPostVariable( 'SurveyQuestion_' . $this->ID . '_Text3' ) &&
             $http->postVariable( 'SurveyQuestion_' . $this->ID . '_Text3' ) != $this->Text3 )
        {
            $this->setAttribute( 'text3', $http->postVariable( 'SurveyQuestion_'.$this->ID.'_Text3' ) );
        }

        if ( $http->hasPostVariable( 'SurveyQuestion_'.$this->ID.'_Num' ) &&
             $http->postVariable( 'SurveyQuestion_'.$this->ID.'_Num' ) != $this->Num )
        {
            $this->setAttribute( 'num', $http->postVariable( 'SurveyQuestion_'.$this->ID.'_Num' ) );
        }

        if ( $http->hasPostVariable( 'SurveyQuestion_'.$this->ID.'_Num2' ) &&
             $http->postVariable( 'SurveyQuestion_'.$this->ID.'_Num2' ) != $this->Num2 )
        {
            $this->setAttribute( 'num2', $http->postVariable( 'SurveyQuestion_'.$this->ID.'_Num2' ) );
        }

        if ( $http->hasPostVariable( 'SurveyQuestion_'.$this->ID.'_Mandatory_Hidden' ) )
        {
            if ( $http->hasPostVariable( 'SurveyQuestion_'.$this->ID.'_Mandatory' ) )
                $newMandatory = 1;
            else
                $newMandatory = 0;

            if ( $newMandatory != $this->Mandatory )
                $this->setAttribute( 'mandatory', $newMandatory );
        }

        if ( $http->hasPostVariable( 'SurveyQuestion_'.$this->ID.'_Default' ) &&
             $http->postVariable( 'SurveyQuestion_'.$this->ID.'_Default' ) != $this->Default )
        {
            $this->setAttribute( 'default_value', $http->postVariable( 'SurveyQuestion_'.$this->ID.'_Default' ) );
        }
    }
}

eZSurveyQuestion::registerQuestionType( 'Formatted Paragraph', 'FormattedParagraph' );

?>
