<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
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
