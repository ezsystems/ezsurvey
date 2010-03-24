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

/*! \file ezsurveyreceiver.php
*/

class eZSurveyReceiver extends eZSurveyQuestion
{
    function eZSurveyReceiver( $row = false )
    {
        $row['type'] = 'Receiver';
        $this->eZSurveyQuestion( $row );
        $this->decodeXMLOptions();
    }

    function addOption( $label, $value, $checked )
    {
        ++$this->OptionID;
        $this->Options[] = array( 'id' => $this->OptionID,
                                  'label' => $label,
                                  'value' => $value,
                                  'checked' => $checked,
                                  'toggled' => $checked,
                                  'tagged' => 0 );
    }

    function removeTaggedOptions()
    {
        $iterator = 1;
        foreach ( array_keys( $this->Options ) as $key )
        {
            $option =& $this->Options[$key];
            if ( $option['tagged'] == 1 )
            {
                unset( $this->Options[$key] );
                $this->setHasDirtyData( true );
            }
            else
                $option['id'] = $iterator++;
        }
    }

    function reorderOptions()
    {
        $iterator = 1;
        foreach ( array_keys ( $this->Options ) as $key )
        {
            $option =& $this->Options[$key];
            $option['id'] = $iterator++;
        }
    }

    function tabOrderCompare( &$option1, &$option2 )
    {
        $http = eZHTTPTool::instance();
        $attributeID = $option1['contentobjectattribute_id'];


        $oldOrder1 =& $option1['id'];
        $oldOrder2 =& $option2['id'];

        $postReceiverTabOrder1 = eZSurveyType::PREFIX_ATTRIBUTE . '_ezsurvey_receiver_' . $this->ID . '_' . $oldOrder1 . '_tab_order_' . $attributeID;
        $newOrder1 = $http->postVariable( $postReceiverTabOrder1 );

        $postReceiverTabOrder2 = eZSurveyType::PREFIX_ATTRIBUTE . '_ezsurvey_receiver_' . $this->ID . '_' . $oldOrder2 . '_tab_order_' . $attributeID;
        $newOrder2 = $http->postVariable( $postReceiverTabOrder2 );

        if ( $newOrder1 < $newOrder2 )
            return -1;
        else if ( $newOrder1 > $newOrder2 )
            return 1;
        else
        {
            if ( $oldOrder1 > $oldOrder2 )
                return -1;
            else if ( $oldOrder1 < $oldOrder2 )
                return 1;
            else
                return 0;
        }
    }

    function decodeXMLOptions()
    {
        $this->Options = array();
        if ( $this->Text2 != '' )
        {
            $dom = new DOMDocument( '1.0', 'utf-8' );
            $dom->loadXML( $this->Text2 );
            $optionArray = $dom->getElementsByTagName( "option" );
            if ( $optionArray )
            {
                foreach ( $optionArray as $option )
                {
                    $optionLabel = $option->getElementsByTagName( "label" );
                    $optionLabel = $optionLabel->item( 0 )->textContent;
                    $optionValue = $option->getElementsByTagName( "email" );
                    $optionValue = $optionValue->item( 0 )->textContent;
                    $optionChecked = $option->getElementsByTagName( "checked" );
                    $optionChecked = $optionChecked->item( 0 )->textContent;
                    $this->addOption( $optionLabel, $optionValue, $optionChecked );
                }
            }
            else
                $this->addOption( '', '', 0 );
        }
        else
            $this->addOption( '', '', 0 );
    }

    function encodeXMLOptions()
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $rootElement = $doc->appendChild( new DomElement( "options" ) );

        $options = array();
        $optionLabel = array();
        $optionValue = array();
        $optionChecked = array();

        foreach ( $this->Options as $i => $optionArray )
        {
            $options[$i] = $doc->createElement( "option" );
            $optionLabel[$i] = $doc->createElement( "label" );
            $optionLabel[$i]->appendChild( $doc->createTextNode( $optionArray['label'] ) );
            $options[$i]->appendChild( $optionLabel[$i] );

            $optionValue[$i] = $doc->createElement( "email" );
            $optionValue[$i]->appendChild( $doc->createTextNode( $optionArray['value'] ) );
            $options[$i]->appendChild( $optionValue[$i] );

            $optionChecked[$i] = $doc->createElement( "checked" );
            $optionChecked[$i]->appendChild( $doc->createTextNode( $optionArray['checked'] ) );
            $options[$i]->appendChild( $optionChecked[$i] );

            $rootElement->appendChild( $options[$i] );
        }
        $this->Text2 = $doc->saveXml();
    }

    function hasAttribute( $attr_name )
    {
        if ( $attr_name == 'options' )
            return true;
        return eZSurveyQuestion::hasAttribute( $attr_name );
    }

    function attribute( $attr_name, $noFunction = false )
    {
        if ( $attr_name == 'options' )
            return $this->Options;
        return eZSurveyQuestion::attribute( $attr_name );
    }

    function processViewActions( &$validation, $params )
    {
        $http = eZHTTPTool::instance();
        $variableArray = array();

        $prefix = $params['prefix_attribute'];
        $attributeID = $params['contentobjectattribute_id'];

        $postSurveyAnswer = $prefix . '_ezsurvey_answer_' . $this->ID . '_' . $attributeID;
        if ( $http->hasPostVariable( $postSurveyAnswer ) )
        {
            $answer = $http->postVariable( $postSurveyAnswer );
            $variableArray['answer'] = $answer;
            foreach ( array_keys( $this->Options ) as $key )
            {
                $option =& $this->Options[$key];
                if ( $option['id'] == $answer )
                {
                    $option['toggled'] = 1;
                    $this->setAnswer( $option['value'] );
                }
                else
                    $option['toggled'] = 0;
            }
        }
    }


    function handleAttributeHTTPAction( $http, $action, $objectAttribute, $parameters )
    {
        $returnValue = false;
        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $objectAttribute->attribute( 'id' );

        $actionNewOption = 'ezsurvey_receiver_' . $this->ID . '_new_option';
        if ( $action == $actionNewOption )
        {
            $this->addOption( '', '', 0 );
            $this->setHasDirtyData( true );
            $this->encodeXMLOptions();
            $returnValue = true;
        }

        $actionRemoveSelected = 'ezsurvey_receiver_' . $this->ID . '_remove_selected';
        if ( $action == $actionRemoveSelected )
        {
            foreach ( array_keys( $this->Options ) as $key )
            {
                $option =& $this->Options[$key];
                $optionID = $option['id'];
                $postTagged = $prefix . '_ezsurvey_receiver_' . $this->ID . '_' . $optionID . '_selected_' . $attributeID;
                $tagged = ( $http->hasPostVariable( $postTagged ) ) ? 1 : 0;
                if ( $tagged )
                {
                    $option['tagged'] = $tagged;
                }
            }
            $this->removeTaggedOptions();
            $this->encodeXMLOptions();
            $returnValue = true;
        }

        $actionUncheckOptions = 'ezsurvey_receiver_' . $this->ID . '_uncheck_options';
        if ( $action == $actionUncheckOptions )
        {
            foreach ( array_keys( $this->Options ) as $key )
            {
                $option =& $this->Options[$key];
                if ( $option['checked'] == 1 )
                {
                    $option['checked'] = 0;
                    $option['toggled'] = 0;
                    $this->setHasDirtyData( true );
                    $this->encodeXMLOptions();
                    $returnValue = true;
                }
            }
        }

        return $returnValue;
    }

    function validateEditActions( &$validation, $params )
    {
        $http = eZHTTPTool::instance();

        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params['contentobjectattribute_id'];

        eZSurveyQuestion::validateEditActions( $validation, $params );

        $optionValues = array();
        $optionCount = 0;
        foreach ( array_keys( $this->Options ) as $key )
        {
            $option =& $this->Options[$key];
            $optionID = $option['id'];

            $postValue = $prefix . '_ezsurvey_receiver_' . $this->ID . '_' . $optionID . '_value_' . $attributeID;
            if ( !$http->hasPostVariable( $postValue ) or
                 ( $http->hasPostVariable( $postValue ) and
                   !eZMail::validate( $http->postVariable( $postValue ) ) ) )
            {
                $validation['error'] = true;
                $validation['errors'][$this->ID] = array( 'message' => ezi18n( 'survey', "Entered text '%text' in the question with id %number is not an email address!", null,
                                                                               array( '%number' => $this->ID,
                                                                                      '%text' => $http->postVariable( $postValue ) ) ),
                                                          'question_id' => $this->ID,
                                                          'code' => 'receiver_email_not_valid',
                                                          'question' => $this );
                break;
            }

            if ( !$http->hasPostVariable( $postValue ) or
                 ( $http->hasPostVariable( $postValue ) and
                   in_array( $http->postVariable( $postValue ), $optionValues ) ) )
            {
                $validation['error'] = true;
                $validation['errors'][$this->ID] = array( 'message' => ezi18n( 'survey', 'Email addresses in the question with id %number must have unique values!', null,
                                                                               array( '%number' => $this->ID ) ),
                                                          'question_id' => $this->ID,
                                                          'code' => 'receiver_email_not_unique',
                                                          'question' => $this );
                break;
            }
            $optionValues[] = $option['value'];
        }

    }


    function processEditActions( &$validation, $params )
    {
        $http = eZHTTPTool::instance();

        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params['contentobjectattribute_id'];

        eZSurveyQuestion::processEditActions( $validation, $params );
        $postCheckedRadio = $prefix . '_ezsurvey_receiver_' . $this->ID . '_checked_' . $attributeID;
        foreach ( array_keys( $this->Options ) as $key )
        {
            $option =& $this->Options[$key];
            $optionID = $option['id'];
            $postReceiver = $prefix . '_ezsurvey_receiver_' . $this->ID . '_' . $optionID . '_label_' . $attributeID;
            if ( $http->hasPostVariable( $postReceiver ) and
                 $http->postVariable( $postReceiver ) != $option['label'] )
            {
                $option['label'] = $http->postVariable( $postReceiver );
                $this->setHasDirtyData( true );
            }

            $postValue = $prefix . '_ezsurvey_receiver_' . $this->ID . '_' . $optionID . '_value_' . $attributeID;
            if ( $http->hasPostVariable( $postValue ) and
                 $http->postVariable( $postValue ) != $option['value'] )
            {
                $option['value'] = $http->postVariable( $postValue );
                $this->setHasDirtyData( true );
            }

            if ( $http->hasPostVariable( $postCheckedRadio ) and
                 !is_array( $http->postVariable( $postCheckedRadio ) ) ) // if radiobutton
            {
                $checkedID = $http->postVariable( $postCheckedRadio );
                if ( $checkedID == $optionID and
                     $option['checked'] != 1 )
                {
                    $option['checked'] = 1;
                    $this->setHasDirtyData( true );
                }
                else if ( $checkedID != $optionID and
                          $option['checked'] != 0 )
                {
                    $option['checked'] = 0;
                    $this->setHasDirtyData( true );
                }
            }
            else // if checkbox
            {
                $postChecked = $prefix . '_ezsurvey_receiver_' . $this->ID . '_' . $optionID . '_checked_' . $attributeID;
                $checked = ( $http->hasPostVariable( $postChecked ) ) ? 1 : 0;
                if ( $checked != $option['checked'] )
                {
                    $option['checked'] = $checked;
                    $this->setHasDirtyData( true );
                }
            }

            // Need to store the attribute id for sorting in the callback function tabOrderCompare.
            $option['contentobjectattribute_id'] = $attributeID;
        }

        usort( $this->Options, array( $this, 'tabOrderCompare' ) );

        $this->reorderOptions();
        $this->encodeXMLOptions();
    }

    function questionNumberIterate( &$iterator )
    {
        if ( count( $this->Options ) > 1 )
            $this->QuestionNumber=$iterator++;
    }

    function answer()
    {
        if ( count( $this->Options ) > 1 )
            return parent::answer();

        $arrayKeys = array_keys( $this->Options );
        return $this->Options[$arrayKeys[0]]['value'];
    }

    var $Options;
    var $OptionID=0;
}

eZSurveyQuestion::registerQuestionType( 'Form Receiver', 'Receiver', true );

?>
