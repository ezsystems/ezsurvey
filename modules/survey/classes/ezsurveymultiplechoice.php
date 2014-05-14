<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

class eZSurveyMultipleChoice extends eZSurveyQuestion
{
    public static $multipleChoiceAnswers;

    static function definition()
    {
        $def = parent::definition();
        $def['function_attributes']['multiple_choice_answers'] = 'multipleChoiceAnswers';
        return $def;
    }

    public function multipleChoiceAnswers()
    {
        $value = array();
        if ( isset( self::$multipleChoiceAnswers[$this->ID] ) )
        {
            $value = self::$multipleChoiceAnswers[$this->ID];
        }
        return $value;
    }

    function eZSurveyMultipleChoice( $row = false )
    {
        $row['type'] = 'MultipleChoice';
        $this->eZSurveyQuestion( $row );
        $this->decodeXMLOptions();
    }

    function afterAdding()
    {
        $this->addOption( '', false, 0 );
        $this->addExtraInfo( '', false, false, 30, 1, 1, 0, 0 );
        $this->encodeXMLOptions();
    }

    function addExtraInfo( $label, $value, $defaultValue, $column, $row, $cssStyle, $valueChecked, $enabled )
    {
        ++$this->ExtraInfoID;
        $this->ExtraInfo['extra_info'] = array( 'id' => $this->ExtraInfoID,
                                                'label' => $label,
                                                'value' => $value,
                                                'default_value' => $defaultValue,
                                                'column' => $column,
                                                'row' => $row,
                                                'enable_css_style' => $cssStyle,
                                                'value_checked' => $valueChecked,
                                                'enabled' => $enabled );
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

        $postMCTabOrder1 = eZSurveyType::PREFIX_ATTRIBUTE . '_ezsurvey_mc_' . $this->ID . '_' . $oldOrder1 . '_tab_order_' . $attributeID;
        $newOrder1 = $http->postVariable( $postMCTabOrder1 );

        $postMCTabOrder2 = eZSurveyType::PREFIX_ATTRIBUTE . '_ezsurvey_mc_' . $this->ID . '_' . $oldOrder2 . '_tab_order_' . $attributeID;
        $newOrder2 = $http->postVariable( $postMCTabOrder2 );

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
                    $optionValue = $option->getElementsByTagName( "value" );
                    $optionValue = $optionValue->item( 0 )->textContent;
                    $optionChecked = $option->getElementsByTagName( "checked" );
                    $optionChecked = $optionChecked->item( 0 )->textContent;
                    $this->addOption( $optionLabel, $optionValue, $optionChecked );
                }
            }
        }

        $this->ExtraInfo = array();
        if ( $this->Text3 != '' )
        {
            $dom = new DOMDocument( '1.0', 'utf-8' );
            $dom->loadXML( $this->Text3 );
            $optionArray = $dom->getElementsByTagName( "option" );
            if ( $optionArray )
            {
                $optionList = array( 'label', 'value', 'default_value', 'column', 'row', 'enable_css_style', 'enabled', 'value_checked' );
                foreach ( $optionArray as $option )
                {
                    $optionValue = array();
                    foreach (  $optionList as $optionListName )
                    {
                        $optionValue[$optionListName] = $option->getElementsByTagName( $optionListName );
                        if ( isset( $optionValue[$optionListName] ) )
                             $optionValue[$optionListName] = $optionValue[$optionListName]->item( 0 )->textContent;
                        else
                             $optionValue[$optionListName] = '';
                    }

                    $this->addExtraInfo( $optionValue['label'], $optionValue['value'], $optionValue['default_value'],
                                         $optionValue['column'], $optionValue['row'], $optionValue['enable_css_style'],
                                         $optionValue['value_checked'], $optionValue['enabled'] );
                }
            }
        }
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

            $optionValue[$i] = $doc->createElement( "value" );
            $optionValue[$i]->appendChild( $doc->createTextNode( $optionArray['value'] ) );
            $options[$i]->appendChild( $optionValue[$i] );

            $optionChecked[$i] = $doc->createElement( "checked" );
            $optionChecked[$i]->appendChild( $doc->createTextNode( $optionArray['checked'] ) );
            $options[$i]->appendChild( $optionChecked[$i] );

            $rootElement->appendChild( $options[$i] );
        }

        $this->Text2 = $doc->saveXml();

        $doc2 = new DOMDocument( '1.0', 'utf-8' );
        $root2 = $doc2->appendChild( new DomElement( "extra_option" ) );

        $options = array();
        $optionLabel = array();
        $optionValue = array();
        $optionDefaultValue = array();
        $optionColumn = array();
        $optionRow = array();
        $optionCssStyle = array();
        $optionEnabled = array();
        $optionValueChecked = array();
        foreach ( $this->ExtraInfo as $i => $optionArray )
        {
            $options[$i] = $doc2->createElement( "option" );
            $optionLabel[$i] = $doc2->createElement( "label" );
            $optionLabel[$i]->appendChild( $doc2->createTextNode( $optionArray['label'] ) );
            $options[$i]->appendChild( $optionLabel[$i] );

            $optionValue[$i] = $doc2->createElement( "value" );
            $optionValue[$i]->appendChild( $doc2->createTextNode( $optionArray['value'] ) );
            $options[$i]->appendChild( $optionValue[$i] );

            $optionDefaultValue[$i] = $doc2->createElement( "default_value" );
            $optionDefaultValue[$i]->appendChild( $doc2->createTextNode( $optionArray['default_value'] ) );
            $options[$i]->appendChild( $optionDefaultValue[$i] );

            $optionColumn[$i] = $doc2->createElement( "column" );
            $optionColumn[$i]->appendChild( $doc2->createTextNode( $optionArray['column'] ) );
            $options[$i]->appendChild( $optionColumn[$i] );

            $optionRow[$i] = $doc2->createElement( "row" );
            $optionRow[$i]->appendChild( $doc2->createTextNode( $optionArray['row'] ) );
            $options[$i]->appendChild( $optionRow[$i] );

            $optionCssStyle[$i] = $doc2->createElement( "enable_css_style" );
            $optionCssStyle[$i]->appendChild( $doc2->createTextNode( $optionArray['enable_css_style'] ) );
            $options[$i]->appendChild( $optionCssStyle[$i] );

            $optionValueChecked[$i] = $doc2->createElement( "value_checked" );
            $optionValueChecked[$i]->appendChild( $doc2->createTextNode( $optionArray['value_checked'] ) );
            $options[$i]->appendChild( $optionValueChecked[$i] );

            $optionEnabled[$i] = $doc2->createElement( "enabled" );
            $optionEnabled[$i]->appendChild( $doc2->createTextNode( $optionArray['enabled'] ) );
            $options[$i]->appendChild( $optionEnabled[$i] );

            $root2->appendChild( $options[$i] );
        }
        $this->Text3 = $doc2->saveXml();
    }

    function hasAttribute( $attr_name )
    {
        if ( $attr_name == 'options' or
             $attr_name == 'extra_info' )
            return true;
        $hasAttribute = eZSurveyQuestion::hasAttribute( $attr_name );
        return $hasAttribute;
    }

    function attribute( $attr_name, $noFunction = false )
    {
        if ( $attr_name == 'options' )
            return $this->Options;
        else if ( $attr_name == 'extra_info' )
        {
            if ( isset( $this->ExtraInfo['extra_info'] ) )
                return $this->ExtraInfo['extra_info'];
            else
                return array();
        }
        return eZSurveyQuestion::attribute( $attr_name );
    }

    function processViewActions( &$validation, $params )
    {
        $http = eZHTTPTool::instance();
        $variableArray = array();
        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params['contentobjectattribute_id'];

        $postSurveyAnswer = $prefix . '_ezsurvey_answer_' . $this->ID .  '_' . $attributeID;
        if ( !$http->hasPostVariable( $postSurveyAnswer ) and
             $this->attribute( 'num' ) != 3 and         // 3 - checkboxes in a row
             $this->attribute( 'num' ) != 4 )           // 4 - checkboxes in a column
        {
            $validation['error'] = true;
            $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Please answer the question %number as well!', null,
                                                                  array( '%number' => $this->questionNumber() ) ),
                                             'question_number' => $this->questionNumber(),
                                             'code' => 'mc_answer_question',
                                             'question' => $this );
        }
        else
        {
            $postAnswer = $prefix . '_ezsurvey_answer_' . $this->ID . '_' . $attributeID;
            $answer = $http->postVariable( $postAnswer );
            if ( is_array( $answer ) )
            {
                foreach ( $answer as $value )
                {
                    $variableArray['answer'][$value] = $value;
                }
            }
            else
            {
                $variableArray['answer'] = $answer;
            }

            $postSurveyExtraAnswer = $prefix . '_ezsurvey_answer_' . $this->ID .  '_extra_info_' . $attributeID;

            if ( $http->hasPostVariable( $postSurveyExtraAnswer ) )
            {
                // if the extra choice is choosen, check the extra_info field.
                if ( is_array( $answer ) )
                {
                    if ( in_array( $this->ExtraInfo['extra_info']['value'], $answer ) )
                    {
                        $surveyExtraInfo = trim( $http->postVariable( $postSurveyExtraAnswer ) );
                        $this->Answer .= $surveyExtraInfo;
                        $variableArray['extra_answer'] = $surveyExtraInfo;
                    }
                }
                else
                {
                    if ( $answer == $this->ExtraInfo['extra_info']['value'] )
                    {
                        $surveyExtraInfo = trim( $http->postVariable( $postSurveyExtraAnswer ) );
                        $this->Answer .= $surveyExtraInfo;
                        $variableArray['extra_answer'] = $surveyExtraInfo;
                    }
                }
            }
            else
            {
                $variableArray['extra_answer'] = '';
                $this->Answer .= '';
            }

            foreach ( array_keys( $this->Options ) as $key )
            {
                $option =& $this->Options[$key];
                if ( is_array( $answer ) )
                {
                    if ( in_array( $option['value'], $answer ) )
                        $option['toggled'] = 1;
                    else
                        $option['toggled'] = 0;
                }
                else
                {
                    if ( $option['value'] == $answer )
                        $option['toggled'] = 1;
                    else
                        $option['toggled'] = 0;
                }
            }

            foreach ( array_keys( $this->ExtraInfo ) as $key )
            {
                // if the option is checked.
                $option =& $this->ExtraInfo[$key];
                if ( is_array( $answer ) )
                {
                    if ( in_array( $option['value'], $answer ) )
                        $option['value_checked'] = 1;
                    else
                        $option['value_checked'] = 0;
                }
                else
                {
                    if ( $option['value'] == $answer )
                        $option['value_checked'] = 1;
                    else
                        $option['value_checked'] = 0;
                }

                if ( isset( $variableArray['extra_answer'] ) )
                {
                    $option['extra_answer'] = $variableArray['extra_answer'];
                }
            }

        }

        self::$multipleChoiceAnswers[$this->ID] = array( 'options' => $this->Options,
                                              'extra_info' => $this->ExtraInfo );
        return $variableArray;
    }


    /*!
        Validate the post actions from the questions.
    */
    function validateEditActions( &$validation, $params )
    {
        $http = eZHTTPTool::instance();

        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params['contentobjectattribute_id'];

        eZSurveyQuestion::validateEditActions( $validation, $params );

        foreach ( array_keys( $this->Options ) as $key )
        {
            $option =& $this->Options[$key];
            $optionID = $option['id'];
            if ( $http->hasPostVariable( 'SurveyMC_' . $this->ID . '_' . $optionID . '_Value' ) )
            {
                $option['value'] = trim( $http->postVariable( 'SurveyMC_' . $this->ID . '_' . $optionID . '_Value' ) );
                if ( strlen( $option['value'] ) == 0 )
                {
                    $validation['error'] = true;
                    $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'You must enter the value for an option in the question with id %question!', null,
                                                                          array( '%question' => $this->ID ) ),
                                                     'question_id' => $this->ID,
                                                     'code' => 'mc_value_for_option',
                                                     'question' => $this );
                }
            }
        }

        // Need to check the POST varilables.
        $optionValues = array();
        $optionCount = 0;
        $checkedCount = 0;

        // Check the extra option value to be not empty
        $postMCExtraValue = $prefix . '_ezsurvey_mc_' . $this->ID . '_extra_value_' . $attributeID;
        if ( $http->hasVariable( $postMCExtraValue ) )
        {
	     	$optionValue = $http->postVariable( $postMCExtraValue );
	     	if ( strlen( $optionValue ) === 0 )
	     	{
                $validation['error'] = true;
                $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Options in the question with id %question must have unique values!', null,
                                                                      array( '%question' => $this->ID ) ),
                                                 'question_id' => $this->ID,
                                                 'code' => 'mc_option_unique_value',
                                                 'question' => $this );
	     	}
        }

        foreach ( array_keys( $this->Options ) as $key )
        {
            $option =& $this->Options[$key];
            $optionID = $option['id'];
            $postMCValue = $prefix . '_ezsurvey_mc_' . $this->ID . '_' . $optionID . '_value_' . $attributeID;
            if ( $http->hasVariable( $postMCValue ) )
            {
                $optionValue = $http->postVariable( $postMCValue );
                $optionCount++;
                if ( in_array( $optionValue, $optionValues ) or strlen( $optionValue ) === 0 )
                {
                    $validation['error'] = true;
                    $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Options in the question with id %question must have unique values!', null,
                                                                          array( '%question' => $this->ID ) ),
                                                     'question_id' => $this->ID,
                                             'code' => 'mc_option_unique_value',
                                             'question' => $this );
                    break;
                }
                $optionValues[] = $optionValue;
            }

            $postMCChecked = $prefix . '_ezsurvey_mc_' . $this->ID . '_' . $optionID . '_checked_' . $attributeID;
            $checkedCount += $http->hasPostVariable( $postMCChecked ) ? 1 : 0;
        }

        $postExtraMCChecked = $prefix . '_ezsurvey_mc_' . $this->ID . '_extra_value_checked_' . $attributeID;
        $checkedCount += $http->hasPostVariable( $postExtraMCChecked ) ? 1 : 0;
    }

    function processEditActions( &$validation, $params )
    {
        $http = eZHTTPTool::instance();

        eZSurveyQuestion::processEditActions( $validation, $params );

        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params['contentobjectattribute_id'];

        $postRenderingStyle = $prefix . '_ezsurvey_question_' . $this->ID . '_num_' . $attributeID;
        $renderingStyle = $http->postVariable( $postRenderingStyle );

        foreach ( array_keys( $this->Options ) as $key )
        {
            $option =& $this->Options[$key];
            $optionID = $option['id'];
            $postMCLabel = $prefix . '_ezsurvey_mc_' . $this->ID . '_' . $optionID . '_label_' . $attributeID;

            if ( $http->hasPostVariable( $postMCLabel ) and
                 $http->postVariable( $postMCLabel ) != $option['label'] )
            {
                $option['label'] = $http->postVariable( $postMCLabel );
                $this->setHasDirtyData( true );
            }

            $postMCValue = $prefix . '_ezsurvey_mc_' . $this->ID . '_' . $optionID . '_value_' . $attributeID;
            if ( $http->hasPostVariable( $postMCValue ) )
            {
                if ( $http->postVariable( $postMCValue ) != $option['value'] )
                {
                    $option['value'] = trim( $http->postVariable( $postMCValue ) );
                    $this->setHasDirtyData( true );
                }
            }

            if ( $renderingStyle == 1 or $renderingStyle == 2 or $renderingStyle == 5 )
            {
                $postMCChecked = $prefix . '_ezsurvey_mc_' . $this->ID . '_checked_' . $attributeID;
                if ( $http->hasPostVariable( $postMCChecked ) and $http->postVariable( $postMCChecked ) == $option['value'] )
                {
                    if ( $option['checked'] != 1 )
                    {
                        $option['checked'] = 1;
                        $this->setHasDirtyData( true );
                    }
                }
                else
                {
                    if ( $option['checked'] != 0 )
                    {
                        $option['checked'] = 0;
                        $this->setHasDirtyData( true );
                    }
                }
            }
            else
            {
                $postMCChecked = $prefix . '_ezsurvey_mc_' . $this->ID . '_' . $optionID .  '_checked_' . $attributeID;
                $checked = ( $http->hasPostVariable( $postMCChecked ) ) ? 1 : 0;
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

        if ( isset( $this->ExtraInfo['extra_info'] ) )
        {
            $extraOption =& $this->ExtraInfo['extra_info'];
            $extraOptionID = $extraOption['id'];

            $extraOptionArrayInput = array( 'extra_label' => 'label',
                                            'extra_value' => 'value',
                                            'extra_default_value' => 'default_value',
                                            'extra_column' => 'column',
                                            'extra_row' => 'row' );

            foreach ( $extraOptionArrayInput as $extraOptionValue => $xmlKey )
            {

                $postMCExtraVariable = $prefix . '_ezsurvey_mc_' . $this->ID . '_' . $extraOptionValue . '_' . $attributeID;
                if ( $http->hasPostVariable( $postMCExtraVariable ) and
                     $http->postVariable( $postMCExtraVariable ) != $extraOption[$xmlKey] )
                {
                    $extraOption[$xmlKey] = $http->postVariable( $postMCExtraVariable );
                    $this->setHasDirtyData( true );
                }
            }

            $extraOptionArrayChecked = array( 'extra_enable_css_style' => 'enable_css_style' );

            $postMCExtraLabel = $prefix . '_ezsurvey_mc_' . $this->ID . '_extra_label_' . $attributeID;

            // check that this is not the first initial request where we don't have any postvariables.
            if ( $http->hasPostVariable( $postMCExtraLabel ) )
            {
                foreach ( $extraOptionArrayChecked as $extraOptionValue => $xmlKey )
                {
                    $postMCExtraVariable = $prefix . '_ezsurvey_mc_' . $this->ID . '_' . $extraOptionValue . '_' . $attributeID;
                    $checked = $http->hasPostVariable( $postMCExtraVariable ) ? 1 : 0;

                    if ( $checked != $extraOption[$xmlKey] )
                    {
                        $extraOption[$xmlKey] = $checked;
                        $this->setHasDirtyData( true );
                    }
                }
            }

            if ( $renderingStyle == 1 or $renderingStyle == 2 or $renderingStyle == 5 )
            {
                $postMCChecked = $prefix . '_ezsurvey_mc_' . $this->ID . '_checked_' . $attributeID;
                if ( $http->hasPostVariable( $postMCChecked ) and $http->postVariable( $postMCChecked ) == $extraOption['value'] )
                {
                    if ( $extraOption['value_checked'] != 1 )
                    {
                        $extraOption['value_checked'] = 1;
                        $this->setHasDirtyData( true );
                    }
                }
                else
                {
                    if ( $extraOption['value_checked'] != 0 )
                    {
                        $extraOption['value_checked'] = 0;
                        $this->setHasDirtyData( true );
                    }
                }
            }
            else
            {
                $extraOptionValue = 'extra_value_checked';
                $xmlKey = 'value_checked';
                $postMCExtraVariable = $prefix . '_ezsurvey_mc_' . $this->ID . '_' . $extraOptionValue . '_' . $attributeID;
                $checked = $http->hasPostVariable( $postMCExtraVariable ) ? 1 : 0;

                if ( $checked != $extraOption[$xmlKey] )
                {
                    $extraOption[$xmlKey] = $checked;
                    $this->setHasDirtyData( true );
                }
            }

        }
        $this->encodeXMLOptions();
    }

    function handleAttributeHTTPAction( $http, $action, $objectAttribute, $parameters )
    {
        $returnValue = false;
        $attributeID = $objectAttribute->attribute( 'id' );
        $postSurveyMCNewAction = 'ezsurvey_mc_' . $this->ID . '_new_option';

        if ( $postSurveyMCNewAction == $action )
        {
            $this->addOption( '', false, 0 );
            $this->setHasDirtyData( true );
            $returnValue = true;
        }

        $postSurveyMCRemoveSelectedAction = 'ezsurvey_mc_' . $this->ID . '_remove_selected';
        if ( $postSurveyMCRemoveSelectedAction == $action )
        {
            $tagsFound = false;
            foreach ( array_keys( $this->Options ) as $key )
            {
                $option =& $this->Options[$key];
                $optionID = $option['id'];
                $postMCSelected = eZSurveyType::PREFIX_ATTRIBUTE . '_ezsurvey_mc_' . $this->ID .
                    '_' . $optionID . '_selected_' . $objectAttribute->attribute( 'id' );
                $tagged = ( $http->hasPostVariable( $postMCSelected ) ) ? 1 : 0;
                if ( $tagged )
                {
                    $option['tagged'] = $tagged;
                    $tagsFound = true;
                }
            }
            if ( $tagsFound === true )
            {
                $this->removeTaggedOptions();
                $returnValue = true;
            }

            $postExtraSelected = eZSurveyType::PREFIX_ATTRIBUTE . '_ezsurvey_mc_' . $this->ID .
                '_extra_selected_' . $objectAttribute->attribute( 'id' );
            if ( $http->hasPostVariable( $postExtraSelected ) )
            {
                $returnValue = $this->setExtraInfoEnabled( 0 );
            }
        }

        $postUncheckOptionsAction = 'ezsurvey_mc_' . $this->ID . '_uncheck_options';
        if ( $postUncheckOptionsAction == $action )
        {
            foreach ( array_keys( $this->Options ) as $key )
            {
                $option =& $this->Options[$key];
                if ( $option['checked'] == 1 )
                {
                    $option['checked'] = 0;
                    $this->setHasDirtyData( true );
                    $returnValue = true;
                }
            }

            foreach ( array_keys( $this->ExtraInfo ) as $key )
            {
                $option =& $this->ExtraInfo[$key];
                if ( $option['value_checked'] == 1 )
                {
                    $option['value_checked'] = 0;
                    $this->setHasDirtyData( true );
                    $returnValue = true;
                }
            }
        }

        $postEnableExtraInfoAction = 'ezsurvey_mc_' . $this->ID . '_enable_extra_info';
        if ( $postEnableExtraInfoAction == $action )
        {
            $returnValue = $this->setExtraInfoEnabled( 1 );
        }

        $this->encodeXMLOptions();
        return $returnValue;
    }

    function setExtraInfoEnabled( $value )
    {
        $returnValue = false;
        foreach ( array_keys( $this->ExtraInfo ) as $key )
        {
            $option =& $this->ExtraInfo[$key];
            if ( $option['enabled'] != $value )
            {
                $option['enabled'] = $value;
                $this->setHasDirtyData( true );
                $returnValue = true;
            }
        }
        return $returnValue;
    }

    function result()
    {
        $surveyID = $this->attribute( 'survey_id' );
        $survey = eZSurvey::fetch( $surveyID );
        $contentObjectID = $survey->attribute( 'contentobject_id' );
        $contentClassAttributeID = $survey->attribute( 'contentclassattribute_id' );
        $languageCode = $survey->attribute( 'language_code' );
        $result = eZSurveyMultipleChoice::fetchResult( $this, $contentObjectID, $contentClassAttributeID, $languageCode );
        return $result['result'];
    }

    // from fetching from template
    function fetchResult( $question, $contentObjectID, $contentClassAttributeID, $languageCode, $metadata = false )
    {
        $db = eZDB::instance();
        $originalQuestionID = $question->attribute( 'original_id' );
        $resultArray = array();
        foreach ( $question->Options as $option )
        {
            $resultArray[$option['value']] = array( 'label' => $option['label'],
                                                    'value' => $option['value'],
                                                    'count' => 0,
                                                    'percentage' => 0 );
        }

        foreach ( $question->ExtraInfo as $option )
        {
            if ( $option['value'] != '' )
            {
                $resultArray[$option['value']] = array( 'label' => $option['label'],
                                                        'value' => $option['value'],
                                                        'count' => 0,
                                                        'percentage' => 0 );
            }
        }


        if ( $metadata == false )
        {
            $query = 'SELECT count(distinct ezsurveyresult.id) as count from ezsurveyresult, ezsurvey where
                             ezsurveyresult.survey_id=ezsurvey.id AND
                             ezsurvey.contentobject_id=\'' . $contentObjectID . '\' AND
                             ezsurvey.contentclassattribute_id=\'' . $contentClassAttributeID . '\' AND
                             ezsurvey.language_code=\'' . $languageCode . '\'';
        }
        else
        {
            $query = 'SELECT count(distinct m1.result_id) as count from ezsurveyresult, ezsurveymetadata as m1, ezsurvey';
            for( $index=2; $index <= count( $metadata ); $index++ )
            {
                $query .= ', ezsurveymetadata as m';
                $query .= $index;
            }
            $query .= ' where ezsurveyresult.survey_id=ezsurvey.id AND
                              ezsurvey.contentobject_id=\'' . $contentObjectID . '\' AND
                              ezsurvey.contentclassattribute_id=\'' . $contentClassAttributeID . '\' AND
                              ezsurvey.language_code=\'' . $languageCode . '\'';

            $index = 0;
            foreach ( array_keys( $metadata ) as $key )
            {
                $index++;
                if ( $index == 1 )
                    $query .= ' and ezsurveyresult.id=m1.result_id';
                else
                {
                    $query .= ' and m' . ( $index - 1 ) . '.result_id=m' . $index . '.result_id';
                }
                $query .= ' and m' . $index . '.attr_name=\'' . $key . '\' and m' . $index . '.attr_value=\'' . $metadata[$key] . '\'';
            }
        }
        $rows = $db->arrayQuery( $query );
        $count= $rows[0]['count'];
        if ( $count == 0 )
        {
            $result = array( 'result' => $resultArray );
            return $result;
        }

        $query = 'SELECT text,count(text) as count from ezsurveyquestionresult';
        if ( $metadata != false )
        {
            for( $index=1; $index <= count( $metadata ); $index++ )
            {
                $query .= ', ezsurveymetadata as m';
                $query .= $index;
            }
        }
        $query .= ' where questionoriginal_id=\'' . $question->attribute( 'original_id' ) . '\'';
        $index = 0;
        if ( $metadata != false )
        {
            foreach ( array_keys( $metadata ) as $key )
            {
                $index++;
                if ( $index == 1 )
                    $query .= ' and ezsurveyquestionresult.result_id=m1.result_id';
                else
                {
                    $query .= ' and m' . ( $index - 1 ) . '.result_id=m' . $index . '.result_id';
                }
                $query .= ' and m' . $index . '.attr_name=\'' . $key . '\' and m' . $index . '.attr_value=\'' . $metadata[$key] . '\'';
            }
        }
        $query .= ' group by text';
        $rows = $db->arrayQuery( $query );

        foreach ( $rows as $row )
        {
            $percentage = (int) round( ( 100 * $row['count'] ) / $count );
            if ( $percentage > 100 )
                $percentage = 100;
            if ( isset( $resultArray[$row['text']]['label'] ) )
            {
                $resultArray[$row['text']] = array( 'label' => $resultArray[$row['text']]['label'],
                                                    'value' => $row['text'],
                                                    'count' => $row['count'],
                                                    'percentage' => $percentage );
            }
        }

        $result = array( 'result' => $resultArray );
        return $result;
    }

    function &fetchResultItem( $question, $result_id, $metadata = false )
    {
        $labelArray = array();
        foreach ( $question->Options as $option )
        {
            $labelArray[$option['value']] = $option['label'];
        }

        $result = eZPersistentObject::fetchObjectList(
            eZSurveyQuestionResult::definition(),
            'text',
            array( 'question_id' => $question->attribute( 'id' ),
                   'result_id' => $result_id ),
            array(),
            null,
            false );

        $extraResult = eZPersistentObject::fetchObjectList(
            eZSurveyQuestionMetaData::definition(),
            'text',
            array( 'question_id' => $question->attribute( 'id' ),
                   'result_id' => $result_id ),
            array(),
            null,
            false );

        $resultArray = array();
        foreach ( array_keys( $result ) as $key )
        {
            $label = '';
            $extraValue = '';
            if ( isset( $result[$key]['text'] ) and
                 isset( $labelArray[$result[$key]['text']] ) )
            {
                 $label = $labelArray[$result[$key]['text']];
            }

            if ( isset( $extraResult[0]['name'] ) and
                 $extraResult[0]['name'] == $result[$key]['text'] )
            {
                $extraValue = $extraResult[0]['value'];
                $label = $question->ExtraInfo['extra_info']['label'];
            }

            $resultArray[] = array( 'value' => $result[$key]['text'],
                                    'label' => $label,
                                    'extra_value' => $extraValue );
        }
        $result = array( 'result' => $resultArray );
        return $result;
    }

    function storeResult( $resultID, $params )
    {
        parent::storeResult( $resultID, $params );
        $answerArray = $this->extraInfoAnswer( $params );
        if ( count( $answerArray ) > 0 )
        {
            foreach ( $answerArray as $value => $content )
            {
                $questionMetaData = eZSurveyQuestionMetaData::instance( $resultID, $this->ID, $this->OriginalID, $value, $content );
            }
        }
        else
        {
            $questionMetaData = eZSurveyQuestionMetaData::instance( $resultID, $this->ID, $this->OriginalID, '', '' );
        }
        $questionMetaData->store();
    }

    function extraInfoAnswer( $params )
    {
        $surveyAnswer = array();
        $contentObjectAttributeID = $params['contentobjectattribute_id'];
        $http = eZHTTPTool::instance();

        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $postSurveyAnswer = $prefix . '_ezsurvey_answer_' . $this->ID . '_' . $contentObjectAttributeID;

        $postSurveyExtraInfoAnswer = $prefix . '_ezsurvey_answer_' . $this->ID . '_extra_info_' . $contentObjectAttributeID;
        if ( $http->hasPostVariable( $postSurveyAnswer ) and
             $http->hasPostVariable( $postSurveyExtraInfoAnswer ) )
        {
            $answer = $http->postvariable( $postSurveyAnswer );
            if ( is_array( $answer ) and in_array( $this->ExtraInfo['extra_info']['value'], $answer ) )
            {
                $surveyAnswer[$this->ExtraInfo['extra_info']['value']] = $http->postVariable( $postSurveyExtraInfoAnswer );
                return $surveyAnswer;
            }
            if ( $this->ExtraInfo['extra_info']['value'] == $answer )
            {
                $surveyAnswer[$answer] = $http->postVariable( $postSurveyExtraInfoAnswer );
                return $surveyAnswer;
            }
            else
            {
                eZDebug::writeWarning( 'Answer: ' . $answer . ' and internal key: ' . $this->ExtraInfo['extra_info']['value'] . ' is not equal',
                                       'eZSurveyMultipleChoice::extraInfoAnswer' );
            }
        }
        return $surveyAnswer;
    }

    function isSingleQuestion()
    {
        $type = $this->attribute( 'num' );
        return ( $type != 3 && $type != 4 ) ? true : false;
    }

    var $Options;
    var $OptionID = 0;

    var $ExtraInfo;
    var $ExtraInfoID = 0;
}

eZSurveyQuestion::registerQuestionType( ezpI18n::tr( 'survey', 'Single/Multiple Choice' ),
                                        'MultipleChoice' );

?>
