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

/*! \file ezsurveyquestion.php
*/


// TODO: remove() will check for can_be_selected();


// Uses $GLOBALS['eZSurveyQuestionTypes'] as global variable

// Abstract class! Do not create instances of this class!
class eZSurveyQuestion extends eZPersistentObject
{
    private static $contentObject;
    private static $dataMap;
    private static $currentUserObject;
    private static $survey;

    function eZSurveyQuestion( $row = array() )
    {
        $type = $row['type'];
        if ( $type )
            $GLOBALS['eZSurveyQuestionTypes'][$type]['count']++;
        $this->Answer = false;
        $this->eZPersistentObject( $row );
    }

    function remove( $conditions = null, $extraConditions = null )
    {
        $GLOBALS['eZSurveyQuestionTypes'][$this->Type]['count']--;
        parent::remove();
    }

    /*!
      Fetch eZSurveyQuestion object

      \param eZSurveyQuestion ID
      \param as Object ( optional, default true )

      \return eZSurveyQuestion
    */
    static function fetch( $id , $asObject = true )
    {

        $surveyQuestionObject = eZPersistentObject::fetchObject( eZSurveyQuestion::definition(),
                                                                 null,
                                                                 array( 'id' => $id ),
                                                                 $asObject );
        return $surveyQuestionObject;
    }

    function store( $fieldFilters = null )
    {
        parent::store( $fieldFilters );
        if ( $this->OriginalID == 0 )
        {
            $this->OriginalID = $this->ID;
            parent::store( $fieldFilters );
        }
    }

    static function definition()
    {

        return array( 'fields' => array ( 'id' => array( 'name' => 'ID',
                                                          'datatype' => 'integer',
                                                          'default' => 0,
                                                          'required' => true ),
                                           'original_id' => array( 'name' => 'OriginalID',
                                                                   'datatype' => 'integer',
                                                                   'default' => 0,
                                                                   'required' => true ),
                                           'survey_id' => array( 'name' => 'SurveyID',
                                                                 'datatype' => 'integer',
                                                                 'default' => 0,
                                                                 'required' => true ),
                                           'visible' => array( 'name' => 'Visible',
                                                               'datatype' => 'integer',
                                                               'default' => 1,
                                                               'required' => true ),
                                           'tab_order' => array( 'name' => 'TabOrder',
                                                                 'datatype' => 'integer',
                                                                 'default' => 0,
                                                                 'required' => true ),
                                           'type' => array( 'name' => 'Type',
                                                            'datatype' => 'string',
                                                            'default' => '',
                                                            'required' => true ),
                                           'mandatory' => array( 'name' => 'Mandatory',
                                                                 'datatype' => 'integer',
                                                                 'default' => 1,
                                                                 'required' => true ),
                                           'default_value' => array( 'name' => 'Default',
                                                                     'datatype' => 'string',
                                                                     'default' => '',
                                                                     'required' => false ),
                                           'text' => array( 'name' => 'Text',
                                                            'datatype' => 'string',
                                                            'default' => '',
                                                            'required' => false ),
                                           'text2' => array( 'name' => 'Text2',
                                                             'datatype' => 'string',
                                                             'default' => '',
                                                             'required' => false ),
                                           'text3' => array( 'name' => 'Text3',
                                                             'datatype' => 'string',
                                                             'default' => '',
                                                             'required' => false ),
                                           'num' => array( 'name' => 'Num',
                                                           'datatype' => 'integer',
                                                           'default' => 0,
                                                           'required' => false ),
                                           'num2' => array( 'name' => 'Num2',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => false ) ),
                       'function_attributes' => array( 'template_name' => 'templateName',
                                                       'answer' => 'answer',
                                                       'question_number' => 'questionNumber',
                                                       'result' => 'result',
                                                       'can_be_selected' => 'canBeSelected' ),
                       'keys' => array( 'id' ),
                       'increment_key' => 'id',
                       'class_name' => 'eZSurveyQuestion',
                       'sort' => array( 'tab_order' => 'asc' ),
                       'name' => 'ezsurveyquestion' );
    }

    function hasAttribute( $attribute )
    {
        $val = parent::hasAttribute( $attribute );
        switch ( $attribute )
        {
            case 'contentobjectattribute_id':
            {
                $val = true;
            }break;
        }
        return $val;
    }

    function attribute( $attribute, $noFunction = false )
    {
        $found = false;
        switch ( $attribute )
        {
            case 'contentobjectattribute_id':
            {
                $val = $this->ContentObjectAttributeID;
                $found = true;
            }break;
        }
        if ( $found === false )
            $val = parent::attribute( $attribute, $noFunction );
        return $val;
    }

    function setAttribute( $attribute, $value )
    {
        $found = false;
        switch ( $attribute )
        {
            case 'contentobjectattribute_id':
            {
                $this->ContentObjectAttributeID = $this->contentobjectattributeID();
                $found = true;
            }break;
        }
        if ( $found === false )
            parent::setAttribute( $attribute, $value );
    }


    function attributes()
    {
        $attributes = parent::attributes();
        $attributes[] = 'contentobjectattribute_id';
        return $attributes;
    }

    function &cloneQuestion( $surveyID, $resetOriginalID = false )
    {
        $row = array( 'id' => null,
                      'survey_id' => $surveyID,
                      'original_id' => $this->OriginalID,
                      'tab_order' => $this->TabOrder,
                      'type' => $this->Type,
                      'mandatory' => $this->Mandatory,
                      'visible' => $this->Visible,
                      'default_value' => $this->Default,
                      'text' => $this->Text,
                      'text2' => $this->Text2,
                      'text3' => $this->Text3,
                      'num' => $this->Num,
                      'num2' => $this->Num2 );
        $classname = implode( '', array( 'eZSurvey', $this->Type ) );
        $cloned = new $classname( $row );
        $cloned->store();
        if ( $resetOriginalID === true )
        {
            $cloned->setAttribute( 'original_id', $cloned->attribute( 'id' ) );
            $cloned->store();
        }
        return $cloned;
    }

    function &templateName()
    {
        $type = strtolower( $this->Type );
        return $type;
    }

    function setAnswer( $answer )
    {
        $this->Answer = $answer;
    }

    function answer()
    {
        $http = eZHTTPTool::instance();

        if ( $this->Answer !== false )
            return $this->Answer;

        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;

        $postSurveyAnswer = $prefix . '_ezsurvey_answer_' . $this->ID . '_' . $this->contentObjectAttributeID();
        if ( $http->hasPostVariable( $postSurveyAnswer ) )
        {
            $surveyAnswer = $http->postVariable( $postSurveyAnswer );
            return $surveyAnswer;
        }
        return $this->Default;
    }

    function canAnswer()
    {
        return true;
    }

    function &canBeSelected()
    {
        $value = true;
        return $value;
    }

    static function registerQuestionType( $name, $type, $maxOneInstance=false )
    {
        $GLOBALS['eZSurveyQuestionTypes'][$type] = array( 'name' => $name,
                                                          'type' => $type,
                                                          'count' => 0,
                                                          'max_one_instance' => $maxOneInstance );
    }

    static function listQuestionTypes()
    {
        return $GLOBALS['eZSurveyQuestionTypes'];
    }

    static function staticTabOrderCompare( &$question1, &$question2 )
    {
        $http = eZHTTPTool::instance();
        $attributeID = $question1->attribute( 'contentobjectattribute_id' );

        $oldOrder1 = $question1->attribute( 'tab_order' );
        $oldOrder2 = $question2->attribute( 'tab_order' );

        $postTabOrder1 = eZSurveyType::PREFIX_ATTRIBUTE . '_ezsurvey_question_tab_order_' . $question1->attribute( 'id' ) . '_' . $attributeID;
        $newOrder1 = $http->postVariable( $postTabOrder1 );

        $postTabOrder2 = eZSurveyType::PREFIX_ATTRIBUTE . '_ezsurvey_question_tab_order_' . $question2->attribute( 'id' ) . '_' . $attributeID;
        $newOrder2 = $http->postVariable( $postTabOrder2 );

        if ( $newOrder1 < $newOrder2 )
            return -1;
        else if ( $newOrder1 > $newOrder2 )
            return 1;
        {
            if ( $oldOrder1 > $oldOrder2 )
                return -1;
            else if ( $oldOrder1 < $oldOrder2 )
                return 1;
            else
                return 0;
        }
    }

    function questionNumberIterate( &$iterator )
    {
        $this->QuestionNumber = $iterator++;
    }

    function &questionNumber()
    {
        return $this->QuestionNumber;
    }

    function processViewActions( &$validation, $params )
    {
        $variableArray = array();
        return $variableArray;
    }

    /*!
      Default do nothing.
    */
    function postProcessViewActions( &$validation, $params )
    {
    }

    /*!
      Validate the post actions from the questions.
    */
    function validateEditActions( &$validation, $params )
    {
    }

    /*!
      process the post actions from questions.
    */
    function processEditActions( &$validation, $params )
    {
        $http = eZHTTPTool::instance();
        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params['contentobjectattribute_id'];

        $postQuestionText = $prefix . '_ezsurvey_question_' . $this->ID . '_text_' . $attributeID;
        if ( $http->hasPostVariable( $postQuestionText ) and
             $http->postVariable( $postQuestionText ) != $this->Text )
            $this->setAttribute( 'text', $http->postVariable( $postQuestionText ) );

        $postQuestionText2 = $prefix . '_ezsurvey_question_' . $this->ID . '_text2_' . $attributeID;
        if ( $http->hasPostVariable( $postQuestionText2 ) and
             $http->postVariable( $postQuestionText2 ) != $this->Text2 )
            $this->setAttribute( 'text2', $http->postVariable( $postQuestionText2 ) );

        $postQuestionText3 = $prefix . '_ezsurvey_question_' . $this->ID . '_text3_' . $attributeID;
        if ( $http->hasPostVariable( $postQuestionText3 ) and
             $http->postVariable( $postQuestionText3 ) != $this->Text3 )
            $this->setAttribute( 'text3', $http->postVariable( $postQuestionText3 ) );

        $postQuestionNum = $prefix . '_ezsurvey_question_' . $this->ID . '_num_' . $attributeID;
        if ( $http->hasPostVariable( $postQuestionNum ) and
             $http->postVariable( $postQuestionNum ) != $this->Num )
            $this->setAttribute( 'num', $http->postVariable( $postQuestionNum ) );

        $postQuestionNum2 = $prefix . '_ezsurvey_question_' . $this->ID . '_num2_' . $attributeID;
        if ( $http->hasPostVariable( $postQuestionNum2 ) and
             $http->postVariable( $postQuestionNum2 ) != $this->Num2 )
            $this->setAttribute( 'num2', $http->postVariable( $postQuestionNum2 ) );

        $postQuestionMandatoryHidden = $prefix . '_ezsurvey_question_' . $this->ID . '_mandatory_hidden_' . $attributeID;
        if ( $http->hasPostVariable( $postQuestionMandatoryHidden ) )
        {
            $postQuestionMandatory = $prefix . '_ezsurvey_question_' . $this->ID . '_mandatory_' . $attributeID;
            if ( $http->hasPostVariable( $postQuestionMandatory ) )
                $newMandatory = 1;
            else
                $newMandatory = 0;

            if ( $newMandatory != $this->Mandatory )
                $this->setAttribute( 'mandatory', $newMandatory );
        }

        $postQuestionDefault = $prefix . '_ezsurvey_question_' . $this->ID . '_default_' . $attributeID;
        if ( $http->hasPostVariable( $postQuestionDefault ) and
             $http->postVariable( $postQuestionDefault ) != $this->Default )
            $this->setAttribute( 'default_value', $http->postVariable( $postQuestionDefault ) );
    }

    function handleAttributeHTTPAction( $http, $action, $objectAttribute, $parameters )
    {
    }

    function storeResult( $resultID, $params )
    {
        $http = eZHTTPTool::instance();
        $httpAnswer = $this->answer( $params );
        if ( !is_array( $httpAnswer ) )
        {
            if ( $httpAnswer == '' )
            {
                $httpAnswer = array();
            }
            else
            {
                $httpAnswer = array( $httpAnswer );
            }
        }

        $questionResultArray =& eZSurveyQuestionResult::instance( $resultID, $this->ID, count( $httpAnswer ) );
        $questionCount = 0;
        foreach ( $httpAnswer as $answer )
        {
            $questionResultArray[$questionCount]->setAttribute( 'text', $answer );
            $questionResultArray[$questionCount]->store();
            ++$questionCount;
        }
    }

    function result()
    {
        return false;
    }

    function afterAdding()
    {
    }

    function contentobjectattributeID()
    {
        if ( !is_numeric( $this->ContentObjectAttributeID ) )
        {
            if ( !$this->Survey )
            {
                $survey = eZSurvey::fetch( $this->SurveyID );
                $this->Survey = $survey;
            }
            else
            {
                $survey = $this->Survey;
            }
            $this->ContentObjectAttributeID = $survey->attribute( 'contentobjectattribute_id' );
        }
        return $this->ContentObjectAttributeID;
    }

    function contentobjectattributeVersion()
    {
        if ( !is_numeric( $this->ContentObjectAttributeVersion ) )
        {
            if ( !$this->Survey )
            {
                $survey = eZSurvey::fetch( $this->SurveyID );
                $this->Survey = $survey;
            }
            else
            {
                $survey = $this->Survey;
            }

            $this->ContentObjectAttributeID = $survey->attribute( 'contentobjectattribute_version' );
        }
        return $this->ContentObjectAttributeVersion;
    }

    /*!
      Default function for questions which should do something after the survey is confirmed.
    */
    public function executeBeforeLastRedirect( $node )
    {
    }

    protected function survey( $surveyID = false )
    {
        if ( !is_numeric( $surveyID ) )
        {
            $surveyID = $this->SurveyID;
        }

        $survey = false;
        if ( is_numeric( $surveyID ) and
             !isset( self::$survey[$surveyID] ) )
        {
            self::$survey[$surveyID] = $survey = eZSurvey::fetch( $surveyID );
        }
        else if ( isset( self::$survey[$surveyID] ) )
        {
            $survey = self::$survey[$surveyID];
        }

        return $survey;
    }

    protected function surveyContentObject( $surveyID )
    {
        if ( !isset( self::$contentObject[$surveyID] ) )
        {
            $survey = eZSurvey::fetch( $this->SurveyID );
            $survey = $this->survey();

            if ( $survey instanceof eZSurvey and
                 $object = eZContentObject::fetch( $survey->attribute( 'contentobject_id' ) ) and
                 $object instanceof eZContentObject )
            {
                self::$contentObject[$surveyID] = $object;
            }
            else
            {
                return false;
            }
        }
        return self::$contentObject[$surveyID];
    }

    /*!
      Return dataMap for a given object (eZContentObject).
    */
    protected function dataMap( $object = false )
    {
        $dataMap = array();

        $id = 'survey';
        if ( $object instanceof eZContentObject )
        {
            $id = $object->attribute( 'id' );
        }
        if ( isset( self::$dataMap[$id] )  )
        {
            $dataMap = self::$dataMap[$id];
        }
        else
        {
            if ( is_numeric( $id ) )
            {
                self::$dataMap[$id] = $dataMap = $object->attribute( 'data_map' );
            }
            else if ( $id == 'survey' and
                      $survey = $this->survey() and
                      $object = $this->surveyContentObject( $this->SurveyID ) and
                      $object instanceof eZContentObject and
                      $survey instanceof eZSurvey )
            {
                self::$dataMap['survey'] = $dataMap = $object->version( $survey->attribute( 'contentobjectattribute_version' ) )->attribute( 'data_map' );
            }
        }
        return $dataMap;
    }

    /*!
      Return object of the current user.
    */
    protected function currentUserObject()
    {
        if ( !isset( self::$currentUserObject ) )
        {
            self::$currentUserObject = false;
            $user = eZUser::instance();
            if ( $user instanceof eZUser )
            {
                $object = $user->attribute( 'contentobject' );
                if ( $object instanceof eZContentObject )
                {
                    self::$currentUserObject = $object;
                }
            }
        }

        return self::$currentUserObject;
    }

    function userAttributeList()
    {
        $object = $this->currentUserObject();
        $dataMap = $this->dataMap( $object );

        $value = array( 'user_email' => ezi18n( 'survey', 'User email' ),
                        'user_name' => ezi18n( 'survey', 'User name' ) );

        $validAttributes = array( 'eztext', 'ezstring', 'ezmail' );

        foreach ( $dataMap as $identifier => $attribute )
        {
            $dataTypeString = $attribute->attribute( 'data_type_string' );
            if ( in_array( $dataTypeString, $validAttributes ) )
            {
                $key = 'userobject_' . $identifier;
                $value[$key] = $attribute->attribute( 'contentclass_attribute' )->attribute( 'name' );
            }
        }

        return $value;
    }



    var $ContentObjectAttributeID;
    var $Survey;

    var $ID;
    var $OriginalID;
    var $SurveyID;
    var $TabOrder;
    var $Type;
    var $Mandatory;
    var $Default;
    var $Text;
    var $Text2;
    var $Text3;
    var $Num;
    var $Num2;
    var $QuestionNumber='';
    var $QuestionTypes;
    var $Answer;
}

if ( !isset( $GLOBALS['eZSurveyQuestionTypes'] ) )
    $GLOBALS['eZSurveyQuestionTypes'] = array();

?>
