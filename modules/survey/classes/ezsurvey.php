<?php
//
// Created on: <02-Apr-2004 00:00:00 Jan Kudlicka>
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

/*! \file ezsurvey.php
 */

require_once( eZExtension::baseDirectory() . '/ezsurvey/modules/survey/classes/ezsurveyquestions.php' );

class eZSurvey extends eZPersistentObject
{
    function eZSurvey( $row = array() )
    {
        if ( !isset( $row['valid_from' ] ) )
            $row['valid_from'] = -time();
        if ( !isset( $row['valid_to' ] ) )
            $row['valid_to'] = -time();
        $this->eZPersistentObject( $row );
        $this->QuestionList = null;
    }

    static function definition()
    {
        $def = array( 'fields' => array( 'id' => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'title' => array( 'name' => 'Title',
                                                           'datatype' => 'string',
                                                           'default' => '',
                                                           'required' => true ),
                                         'enabled' => array( 'name' => 'Enabled',
                                                             'datatype' => 'integer',
                                                             'default' => '1',
                                                             'required' => true ),
                                         'published' => array( 'name' => 'Published',
                                                               'datatype' => 'integer',
                                                               'default' => '0',
                                                               'required' => true ),
                                         'persistent' => array( 'name' => 'Persistent',
                                                                'datatype' => 'integer',
                                                                'default' => '0',
                                                                'required' => true ),
                                         'one_answer' => array( 'name' => 'OneAnswer',
                                                                'datatype' => 'integer',
                                                                'default' => '0',
                                                                'required' => false ),
                                         'valid_from' => array( 'name' => 'ValidFrom',
                                                                'datatype' => 'integer',
                                                                'default' => '0',
                                                                'required' => true ),
                                         'valid_to' => array( 'name' => 'ValidTo',
                                                              'datatype' => 'integer',
                                                              'default' => '0',
                                                              'required' => true ),
                                         'redirect_cancel' => array( 'name' => 'RedirectCancel',
                                                                     'datatype' => 'string',
                                                                     'default' => '/content/view/full/2',
                                                                     'required' => true ),
                                         'redirect_submit' => array( 'name' => 'RedirectSubmit',
                                                                     'datatype' => 'string',
                                                                     'default' => '/content/view/full/2',
                                                                     'required' => true ),
                                         'contentobject_id' => array( 'name' => 'ContentObjectID',
                                                                      'datatype' => 'integer',
                                                                      'default' => '0',
                                                                      'required' => true ),
                                         'contentobjectattribute_id' => array( 'name' => 'ContentObjectAttributeID',
                                                                               'datatype' => 'integer',
                                                                               'default' => '0',
                                                                               'required' => true ),
                                         'contentclassattribute_id' => array( 'name' => 'ContentClassAttributeID',
                                                                               'datatype' => 'integer',
                                                                               'default' => '0',
                                                                               'required' => true ),
                                         'language_code' => array( 'name' => 'LanguageCode',
                                                                   'datatype' => 'string',
                                                                   'default' => '',
                                                                   'required' => true ),
                                         'contentobjectattribute_version' => array( 'name' => 'ContentObjectAttributeVersion',
                                                                                    'datatype' => 'integer',
                                                                                    'default' => '0',
                                                                                    'required' => true ) ),
                      'keys' => array( 'id' ),
                      'function_attributes' => array( 'question_results' => 'fetchQuestionResultList',
                                                      'result_count' => 'resultCount',
                                                      'questions' => 'fetchQuestionList',
                                                      'question_types' => 'questionTypes',
                                                      'valid_from_array' => 'validFromArray',
                                                      'valid_to_array' => 'validToArray',
                                                      'valid' => 'valid',
                                                      'can_edit_results' => 'canEditResults',
                                                      'activity_status' => 'activityStatus' ),
                      'increment_key' => 'id',
                      'class_name' => 'eZSurvey',
                      'sort' => array( 'id' => 'asc' ),
                      'name' => 'ezsurvey' );
        return $def;
    }

    function cloneSurvey( $resetOriginalQuestionID = false )
    {
        $row = array( 'id' => null,
                      'title' => $this->Title,
                      'valid_from' => $this->ValidFrom,
                      'valid_to' => $this->ValidTo,
                      'enabled' => $this->Enabled,
                      'published' => $this->Published,
                      'one_answer' => $this->OneAnswer,
                      'persistent' => $this->Persistent,
                      'redirect_cancel' => $this->RedirectCancel,
                      'redirect_submit' => $this->RedirectSubmit );
        $cloned = new eZSurvey( $row );
        $cloned->store();
        if ( $this->QuestionList === null )
        {
            $this->fetchQuestionList();
        }
        foreach( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $clonedID = $cloned->attribute( 'id' );
            $question->cloneQuestion( $clonedID, $resetOriginalQuestionID );
        }
        return $cloned;
    }

    /*!
     Check current user can edit survey results.

     \return true if user is allowed to edit survey results.
    */
    function canEditResults()
    {
        $user = eZUser::instance();
        $accessList = 1;

        $accessResult = $user->hasAccessTo( 'survey', 'administration' );
        $result = ( $accessResult['accessWord'] == 'yes' and $this->Persistent == 1 );

        return $result;
    }

    /*!
     Check the status of the survey.

     \return 0 - Not started.
             1 - Open.
             2 - Closed.
    */
    function activityStatus()
    {
        $result = 0;
        $time = time();
        if ( ( ( (int)$this->ValidFrom ) <= 0 or ( (int)$this->ValidFrom ) <= $time ) and
             ( ( (int)$this->ValidTo ) <= 0   or ( (int)$this->ValidTo ) >= $time ) )
        {
            $result = 1;
        }
        else if ( ( (int)$this->ValidTo ) > 0 and ( (int)$this->ValidTo ) < $time )
        {
            $result = 2;
        }

        return $result;
    }

    /*!
     Get number of results for current survey
    */
    function resultCount( $user = false )
    {
        $db = eZDB::instance();
        $contentobjectID = $this->attribute( 'contentobject_id' );
        $contentClassAttributeID = $this->attribute( 'contentclassattribute_id' );
        $languageCode = $this->attribute( 'language_code' );

        $userSelect = '';
        if ( get_class( $user ) == 'eZUser' )
        {
            $userSelect = ' AND ezsurveyresult.user_id=\'' . $user->attribute( 'contentobject_id' ) . '\'';
        }

        $sql = 'SELECT count(ezsurveyresult.id) as count FROM ' .
                       'ezsurveyresult, ezsurvey WHERE ' .
                       'survey_id=ezsurvey.id AND ' .
                       'ezsurvey.contentobject_id=\'' . $contentobjectID . '\' AND ' .
                       'ezsurvey.contentclassattribute_id=\'' . $contentClassAttributeID . '\' AND ' .
                       'ezsurvey.language_code=\'' . $languageCode . '\'' . $userSelect;

        $rows = $db->arrayQuery( $sql );
        $count = $rows[0]['count'];
        return $count;
    }

    function id()
    {
        return $this->ID;
    }

    /*!
      Fetch eZSurvey object

      \param eZSurvey ID
      \param as Object ( optional, default true )

      \return eZSurvey
    */
    static function fetch( $id , $asObject = true )
    {

        $surveyObject = eZPersistentObject::fetchObject( eZSurvey::definition(),
                                                null,
                                                array( 'id' => $id ),
                                                $asObject );
        return $surveyObject;
    }

    /*!
     \static

      Fetch survey object. Return false if survey is not published, not enabled or not valid.

      \param Survey id

      \return Survey object
    */
    function fetchSurvey( $id )
    {
        $survey = eZSurvey::fetch( $id );
        if ( !$survey || !$survey->published() || !$survey->enabled() || !$survey->valid() )
            $survey = false;
        return array( 'result' => $survey );
    }

    /*!
     Get previous results for current survey.

     \param user object.

     \return array of question results. false if persistent is set to 0, no previous results exists or anonymous user.
     */
    function &fetchQuestionResultList( $user = false )
    {
        if ( !$this->attribute( 'persistent' ) )
        {
            $value = 0;
            return $value;
        }

        if ( !$user )
        {
            $user = eZUser::instance();
        }

        if ( !$user->attribute( 'is_logged_in' ) )
        {
            $value = 0;
            return $value;
        }

        $userID = $user->attribute( 'contentobject_id' );
        $surveyResultDefinition = eZSurveyResult::definition();
        $result = eZPersistentObject::fetchObject( $surveyResultDefinition,
                                                    null,
                                                    array( 'survey_id' => $this->ID,
                                                           'user_id' => $userID ) );

        if ( !$result )
        {
            $value = 0;
            return $value;
        }
        $isPersistent = $this->Persistent == 1 ? true : false;
        return $result->fetchQuestionResultList( false, $isPersistent );
    }

    /*!
     Fetch list of questions objects for the current survey.

     \return Array of Question objects.
    */
    function &fetchQuestionList()
    {
        if ( $this->QuestionList === null )
        {
            $rows = eZPersistentObject::fetchObjectList( eZSurveyQuestion::definition(),
                                                         null,
                                                         array( 'survey_id' => $this->ID ),
                                                         array( 'tab_order' => 'asc' ),
                                                         null,
                                                         false );
            $objects = array();
            $this->QuestionList = array();
            $questionIterator = 1;
            if ( count( $rows ) > 0 )
            {
                foreach ( $rows as $row )
                {
                    $classname = implode( '', array( 'eZSurvey', $row['type'] ) );
                    $newObject = new $classname( $row );
                    $newObject->questionNumberIterate( $questionIterator );
                    $newObjectID = $newObject->attribute( 'id' );
                    $this->QuestionList[$newObjectID] = $newObject;
                }
            }
        }
        return $this->QuestionList;
    }

    static function fetchSurveyList( $params = array(), &$count )
    {
        $limitArray = array( 'offset' => 0,
                             'limit' => 50 );
        if ( isset( $params['offset'] ) )
            $limitArray['offset'] = $params['offset'];

        if ( isset( $params['limit'] ) )
            $limitArray['limit'] = $params['limit'];

        $selectQueries['var'] = "ezcontentobject.id AS contentobject_id,
                         ezcontentobject_attribute.language_code as contentobjattr_language_code,
                         ezcontentobject.name,
                         ezsurvey.*";
        $selectQueries['count'] = "count(ezcontentobject.id) AS count";
        $db = eZDB::instance();
        foreach ( $selectQueries as $key => $var  )
        {
            $query = "SELECT " . $var . "
                  FROM ezsurvey, ezcontentobject, ezcontentobject_attribute
                  WHERE ezcontentobject_attribute.data_type_string='ezsurvey' AND
                        ezsurvey.id=ezcontentobject_attribute.data_int AND
                        ezcontentobject_attribute.contentobject_id=ezcontentobject.id AND
                        ezcontentobject.current_version=ezcontentobject_attribute.version AND
                        ezcontentobject.status=" . eZContentObject::STATUS_PUBLISHED . "
                  ORDER BY ezcontentobject.id DESC, ezcontentobject.current_version DESC";

            if ( $key == 'var' )
            {
                $rows = $db->arrayQuery( $query, $limitArray );
                $objectArray = array();
                if ( is_array( $rows ) and count( $rows ) > 0 )
                {
                    foreach ( $rows as $row )
                    {
                        $object = new eZSurvey( $row );
                        $objectArray[] = array( 'survey' => $object,
                                                'info' => array( 'contentobject_id' => $row['contentobject_id'],
                                                                 'contentobjectattribute_language_code' => $row['contentobjattr_language_code'],
                                                                 'name' => $row['name'] ) );
                    }
                }
            }
            else
            {
                $rows = $db->arrayQuery( $query );
                $count = $rows[0]['count'];
            }
        }
        return $objectArray;
    }

    function enabled()
    {
        return ( $this->Enabled == '1' ) ? true : false;
    }

    function published()
    {
        return ( $this->Published == '1' ) ? true : false;
    }

    /*!
     Check if survey is in the valid timeframe

     \return true if the survey is in the valid time interval
    */
    function valid()
    {
        $returnValue = ( ( (int)$this->ValidFrom <= 0 or (int)$this->ValidFrom <= time() ) and
                       ( (int)$this->ValidTo <= 0 or (int)$this->ValidTo >= time() ) );
        return $returnValue;
    }

    function processViewActions( &$validation, $params )
    {
        $variableArray = array();
        $validation['error'] = false;
        $validation['warning'] = false;
        $validation['errors'] = array();
        $validation['warnings'] = array();

        $prefix = $params['prefix_attribute'];
        $attributeID = $params['contentobjectattribute_id'];

        if ( $this->QuestionList === null )
        {
            $this->fetchQuestionList();
        }

        $http = eZHTTPTool::instance();

        $postSurveyID = $prefix . '_ezsurvey_id_' . $attributeID;
        if ( !$http->hasPostVariable( $postSurveyID ) )
        {
            return false;
        }

        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $variableArray[$question->attribute( 'id' )] = $question->processViewActions( $validation, $params );
        }

        // post process questions, so each question type can do actions based on the total result.
        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $question->postProcessViewActions( $validation, $params );
        }

        return $variableArray;
    }

    function reOrder()
    {
        $iterator = 1;
        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            if ( $question->attribute( 'tab_order' ) != $iterator )
                $question->setAttribute( 'tab_order', $iterator );
            $iterator++;
        }
    }

    /*!
     Validate the editing input of the survey, called by the module view.

     Return the result in the input parameter
    */
    function validateEditActions( &$validation, $params )
    {
        $http = eZHTTPTool::instance();

        $prefix = $params['prefix_attribute'];
        $attributeID = $params['contentobjectattribute_id'];

        $validation['error'] = false;
        $validation['warning'] = false;
        $validation['errors'] = array();
        $validation['warnings'] = array();

        if ( $this->QuestionList === null )
        {
            $this->fetchQuestionList();
        }

        $postSurveyID = $prefix . '_ezsurvey_id_' . $attributeID;
        if ( !$http->hasPostVariable( $postSurveyID ) )
        {
            $validation['error'] = true;
            $validation['errors'][] = array( 'message' => ezi18n( 'survey', 'The survey is not valid. Survey ID is missing' ) );
            return false;
        }

        $validArray = array( 'hour', 'minute', 'year', 'month', 'day' );
        foreach ( $validArray as $validVariable )
        {
            $postValidFrom = $prefix . '_ezsurvey_valid_from_' . $validVariable . '_' . $attributeID;
            $postValidFromHidden = $prefix . '_ezsurvey_valid_from_' . $validVariable . '_hidden_' . $attributeID;
            if ( ( $http->hasPostVariable( $postValidFrom ) and !is_numeric( $http->postVariable( $postValidFrom ) ) ) or
                 ( $http->hasPostVariable( $postValidFromHidden ) and !is_numeric( $http->postVariable( $postValidFromHidden ) ) ) )
            {
                $validation['error'] = true;
                $validation['errors'][] = array( 'message' => ezi18n( 'survey', 'All values in Valid from need to be numeric.' ) );
            }
        }

        foreach ( $validArray as $validVariable )
        {
            $postValidTo = $prefix . '_ezsurvey_valid_to_' . $validVariable . '_' . $attributeID;
            $postValidToHidden = $prefix . '_ezsurvey_valid_to_' . $validVariable . '_' . $attributeID;
            if ( ( $http->hasPostVariable( $postValidTo ) and !is_numeric( $http->postVariable( $postValidTo ) ) ) or
                 ( $http->hasPostVariable( $postValidToHidden ) and !is_numeric( $http->postVariable( $postValidToHidden ) ) ) )
            {
                $validation['error'] = true;
                $validation['errors'][] = array( 'message' => ezi18n( 'survey', 'All values in Valid to need to be numeric.' ) );
            }
        }

        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $question->validateEditActions( $validation, $params );
        }
    }

    function lastNewQuestionType()
    {
        $http = eZHTTPTool::instance();
        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $this->ContentObjectAttributeID;

        $postNewQuestionType = $prefix . '_ezsurvey_new_question_type_' . $attributeID;
        if ( $http->hasPostVariable( $postNewQuestionType ) )
        {
            $questionType = $http->postVariable( $postNewQuestionType );
            return $questionType;
        }
        return false;
    }


    /*!
      handle the http actions from the datatype.
    */
    function handleAttributeHTTPAction( $http, $action, $objectAttribute, $parameters )
    {
        $status = false;
        switch ( $action )
        {
            case 'new_question':
            {
                $status = $this->handleNewQuestionAction( $http, $objectAttribute, $parameters );
            }break;

            case 'remove_selected':
            {
                $status = $this->handleRemoveSelectedSurveyAction( $http, $objectAttribute, $parameters );
            }break;
        }

        if ( $status == false )
        {
            $status = $this->handleQuestionAction( $http, $action, $objectAttribute, $parameters );
        }

        if ( $status == false )
        {
            $status = $this->handleCloneQuestionAction( $http, $action, $objectAttribute, $parameters );
        }
        return $status;
    }

    function handleQuestionAction( $http, $action, $objectAttribute, $parameters )
    {
        $status = false;
        if ( $this->QuestionList === null )
        {
            $this->fetchQuestionList();
        }

        if ( is_array( $this->QuestionList ) )
        {
            foreach ( array_keys( $this->QuestionList ) as $key )
            {
                $question =& $this->QuestionList[$key];
                $questionStatus = $question->handleAttributeHTTPAction( $http, $action, $objectAttribute, $parameters );
                if ( $questionStatus === true )
                {
                    $status = true;
                }
            }
        }
        return $status;
    }

    function handleCloneQuestionAction( $http, $action, $objectAttribute, $parameters )
    {
        $status = false;

        if ( $this->QuestionList === null )
        {
            $this->fetchQuestionList();
        }

        $attributeID = $objectAttribute->attribute( 'id' );
        foreach ( $this->QuestionList as $question )
        {
            $postQuestionCopy = 'ezsurvey_question_copy_' . $question->attribute( 'id' );
            if ( $action == $postQuestionCopy )
            {
                $question->cloneQuestion( $this->attribute( 'id' ), true );
                $this->QuestionList = null;  // Clear the cached list  TODO: cleanup
                $surveyList =& $this->fetchQuestionList();
                $this->reOrder();
                $status = true;
                break;
            }
        }
        return $status;
    }

    function handleRemoveSelectedSurveyAction( $http, $objectAttribute, $parameters )
    {
        $returnValue = false;

        $attributeID = $objectAttribute->attribute( 'id' );
        $postRemoveSelected = eZSurveyType::PREFIX_ATTRIBUTE . '_ezsurvey_new_question_type_' . $attributeID;

        if ( $this->QuestionList === null )
        {
            $this->fetchQuestionList();
        }

        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $questionID = $question->attribute( 'id' );
            $postQuestionSelected = eZSurveyType::PREFIX_ATTRIBUTE . '_ezsurvey_question_' . $questionID . '_selected_' . $attributeID;
            if ( $http->hasPostVariable( $postQuestionSelected ) )
            {
                $question->remove();
                unset( $this->QuestionList[$key] );
                $returnValue = true;
            }
        }

        if ( $returnValue === true )
        {
            $this->reOrder();
        }

        return $returnValue;
    }


    function handleNewQuestionAction( $http, $objectAttribute, $parameters )
    {
        $returnValue = false;
        $list = eZSurveyQuestion::listQuestionTypes();
        $attributeID = $objectAttribute->attribute( 'id' );
        $postNewQuestionType = eZSurveyType::PREFIX_ATTRIBUTE . '_ezsurvey_new_question_type_' . $attributeID;
        $type = $http->postVariable( $postNewQuestionType );
        if ( isset( $list[$type] ) and
             ( $list[$type]['max_one_instance'] == false or $list[$type]['count'] == 0 ) )
        {
            $classname = implode( '', array( 'eZSurvey', $type ) );

            if ( $this->QuestionList === null )
            {
                $this->fetchQuestionList();
            }
            $newObject = new $classname( array( 'survey_id' => $this->ID,
                                                'tab_order' => count( $this->QuestionList ) + 1 ) );
            $newObject->afterAdding();
            $newObject->store();

            $newObjectID = $newObject->attribute( 'id' );


            $this->QuestionList[$newObjectID] =& $newObject;
            $this->reOrder();
            $returnValue = true;
        }
        return $returnValue;
    }


    /*!
     Processes the editing input of the survey, called by the module view.

     Return the result in the input parameter
    */
    function processEditActions( &$validation, $params )
    {
        $validation['error'] = false;
        $validation['warning'] = false;
        $validation['errors'] = array();
        $validation['warnings'] = array();

        if ( $this->QuestionList === null )
        {
            $this->fetchQuestionList();
        }

        $http = eZHTTPTool::instance();
        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params['contentobjectattribute_id'];

        $postSurveyID = $prefix . '_ezsurvey_id_' . $attributeID;
        if ( !$http->hasPostVariable( $postSurveyID ) )
        {
            $validation['error'] = true;
            $validation['errors'][] = array( 'message' => ezi18n( 'survey', 'The survey is not valid. Survey ID is missing' ) );
            return false;
        }

        $postTitle = $prefix . '_ezsurvey_title_' . $attributeID;
        if ( $http->postVariable( $postTitle ) != $this->Title )
        {
            $surveyTitle = $http->postVariable( $postTitle );
            $this->setAttribute( 'title', $surveyTitle );
        }

        $postEnabled = $prefix . '_ezsurvey_enabled_' . $attributeID;
        $enabled = ( $http->hasPostVariable( $postEnabled ) ) ? 1 : 0;

        if ( $enabled != $this->Enabled )
            $this->setAttribute( 'enabled', $enabled );


        $validArray = array( 'hour', 'minute', 'year', 'month', 'day' );
        $validFromArray = array();
        foreach ( $validArray as $validVariable )
        {
            $postValidFrom = $prefix . '_ezsurvey_valid_from_' . $validVariable . '_' . $attributeID;
            $postValidFromHidden = $prefix . '_ezsurvey_valid_from_' . $validVariable . '_hidden_' . $attributeID;
            if ( ( $http->hasPostVariable( $postValidFrom ) ) )
            {
                $validFromArray[$validVariable] = $http->postVariable( $postValidFrom );
            }
            else if ( $http->hasPostVariable( $postValidFromHidden ) )
            {
                $validFromArray[$validVariable] = $http->postVariable( $postValidFromHidden );
            }
            else
            {
                $validFromArray[$validVariable] = 0;
            }
        }

        $validFrom = mktime(
            $validFromArray['hour'],
            $validFromArray['minute'],
            0,
            $validFromArray['month'],
            $validFromArray['day'],
            $validFromArray['year'] );

        $validToArray = array();
        foreach ( $validArray as $validVariable )
        {
            $postValidTo = $prefix . '_ezsurvey_valid_to_' . $validVariable . '_' . $attributeID;
            $postValidToHidden = $prefix . '_ezsurvey_valid_to_' . $validVariable . '_hidden_' . $attributeID;
            if ( ( $http->hasPostVariable( $postValidTo ) ) )
            {
                $validToArray[$validVariable] = $http->postVariable( $postValidTo );
            }
            else if ( $http->hasPostVariable( $postValidToHidden ) )
            {
                $validToArray[$validVariable] = $http->postVariable( $postValidToHidden );
            }
            else
            {
                $validToArray[$validVariable] = 0;
            }
        }

        $validTo = mktime(
            $validToArray['hour'],
            $validToArray['minute'],
            0,
            $validToArray['month'],
            $validToArray['day'],
            $validToArray['year'] );

        $postValidFromNoLimit = $prefix . '_ezsurvey_valid_from_no_limit_' . $attributeID;
        if ( $http->hasPostVariable( $postValidFromNoLimit ) )
            $validFrom = -$validFrom;
        if ( $this->ValidFrom != $validFrom )
            $this->setAttribute( 'valid_from', $validFrom );

        $postSurveyPersistent = $prefix . '_ezsurvey_persistent_' . $attributeID;
        if ( $http->hasPostVariable( $postSurveyPersistent ) )
            $this->setAttribute( 'persistent', 1 );
        else
            $this->setAttribute( 'persistent', 0 );

        $postSurveyOneAnswer = $prefix . '_ezsurvey_one_answer_' . $attributeID;
        if ( $http->hasPostVariable( $postSurveyOneAnswer ) )
            $this->setAttribute( 'one_answer', 1 );
        else
            $this->setAttribute( 'one_answer', 0 );

        $postValidToNoLimit = $prefix . '_ezsurvey_valid_to_no_limit_' . $attributeID;
        if ( $http->hasPostVariable( $postValidToNoLimit ) )
            $validTo = -$validTo;
        if ( $this->ValidTo != $validTo )
            $this->setAttribute( 'valid_to', $validTo );

        $postRedirectCancel = $prefix . '_ezsurvey_redirect_cancel_' . $attributeID;
        if ( $this->RedirectCancel != $http->postVariable( $postRedirectCancel ) )
            $this->setAttribute( 'redirect_cancel', $http->postVariable( $postRedirectCancel ) );

        $postRedirectSubmit = $prefix . '_ezsurvey_redirect_submit_' . $attributeID;
        if ( $this->RedirectSubmit != $http->postVariable( $postRedirectSubmit ) )
            $this->setAttribute( 'redirect_submit', $http->postVariable( $postRedirectSubmit ) );

        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $questionID = $question->attribute( 'id' );

            $postQuestionVisible = $prefix . '_ezsurvey_question_visible_'. $questionID . '_' . $attributeID;
            if ( $http->hasPostVariable( $postQuestionVisible ) )
            {
                $question->setAttribute( 'visible', 1 );
            }
            else
            {
                $question->setAttribute( 'visible', 0 );
            }

            // Set the attributeID in the question to be able to fetch the correct post variable
            // in the sort function staticTabOrderCompare
            $question->setAttribute( 'contentobjectattribute_id', $attributeID );
        }

        usort( $this->QuestionList, array( 'eZSurveyQuestion', 'staticTabOrderCompare' ) );

        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $question->processEditActions( $validation, $params );
        }

        $this->reOrder();
    }

    function sync( $fieldFilters = null )
    {
        if ( $this->QuestionList === false )
        {
            $this->fetchQuestionList();
        }
        eZPersistentObject::sync( $fieldFilters );
        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $question->sync();
        }
    }

    function storeAll()
    {
        if ( $this->QuestionList === false )
        {
            $this->fetchQuestionList();
        }
        $this->store();
        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $question->store();
        }
    }

    function storeResult( $resultID, $params )
    {
        if ( !$this->QuestionList )
        {
            $this->fetchQuestionList();
        }

        foreach ( array_keys( $this->QuestionList ) as $key )
        {
            $question =& $this->QuestionList[$key];
            $question->storeResult( $resultID, $params );
        }
    }

    // Removes a survey, it's question and all collected data
    function remove( $conditions = null, $extraConditions = null )
    {
        $db = eZDB::instance();
        $db->begin();

        $rows = $db->arrayQuery( "select id from ezsurveyresult where survey_id=" . $this->ID );
        $results = false;
        foreach( $rows as $row )
        {
            if ( $results == false )
            {
                $resultIDString = '('.$row['id'];
                $results = true;
            }
            else
                $resultIDString .= ', '.$row['id'];
        }
        if ( $results )
            $resultIDString .= ')';

        $db->query( "delete from ezsurvey where id=".$this->ID );
        $db->query( "delete from ezsurveyquestion where survey_id=".$this->ID );
        if ( $results )
        {
            $db->query( "delete from ezsurveyresult where survey_id=".$this->ID );
            $db->query( "delete from ezsurveyquestionresult where result_id in ".$resultIDString );
            $db->query( "delete from ezsurveymetadata where result_id in ".$resultIDString );
        }

        $db->commit();
    }

    function &questionTypes()
    {
        $list = eZSurveyQuestion::listQuestionTypes();
        foreach( array_keys( $list ) as $index )
        {
            if ( $list[$index]['max_one_instance'] == true )
            {
                if ( $list[$index]['count'] > 0 )
                    unset( $list[$index] );
            }
        }
        return $list;
    }

    // private
    function &dateTimeArray( $tstamp )
    {
        $noLimit = false;
        if ( $tstamp <= 0 )
        {
            $noLimit = true;
            $tstamp = -$tstamp;
        }

        $returnArray = array(
          "no_limit" => $noLimit,
          "year" => date( "Y", $tstamp ),
          "month" => date( "m", $tstamp ),
          "day" => date( "d", $tstamp ),
          "hour" => date( "H", $tstamp ),
          "minute" => date( "i", $tstamp )
        );
        return $returnArray;
    }

    function &validFromArray()
    {
        $dateTimeArray =& $this->dateTimeArray( $this->ValidFrom );
        return $dateTimeArray;
    }

    function &validToArray()
    {
        $dateTimeArray =& $this->dateTimeArray( $this->ValidTo );
        return $dateTimeArray;
    }

    function validateContentObjectAttributeID( $contentObjectAttributeID )
    {
        $db = eZDB::instance();
        $attributeID = $db->escapeString( $contentObjectAttributeID );
        $query = "SELECT ezsurvey.id FROM ezsurvey, ezcontentobject_attribute WHERE ezcontentobject_attribute.data_int=ezsurvey.id and ezcontentobject_attribute.id='$attributeID' and ezsurvey.id='" . $this->ID . "'";
        $result = $db->arrayQuery( $query );
        $status = count( $result ) > 0;
        return $status;

    }


    static function fetchByObjectInfo( $contentObjectID, $contentClassAttributeID, $languageCode )
    {
        $db = eZDB::instance();
        $contentObjectID = $db->escapeString( $contentObjectID );
        $contentObjectAttributeID = $db->escapeString( $contentClassAttributeID );
        $languageCode = $db->escapeString( $languageCode );
        $query = "SELECT ezsurvey.id as id FROM
                         ezsurvey, ezcontentobject, ezcontentobject_attribute WHERE
                         ezsurvey.id=ezcontentobject_attribute.data_int AND
                         ezcontentobject_attribute.data_type_string='ezsurvey' AND
                         ezcontentobject_attribute.version=ezsurvey.contentobjectattribute_version AND
                         ezcontentobject.id=ezcontentobject_attribute.contentobject_id AND
                         ezcontentobject.current_version=ezcontentobject_attribute.version AND
                         ezsurvey.contentobject_id='" . $contentObjectID . "' AND
                         ezsurvey.contentclassattribute_id='" . $contentClassAttributeID . "' AND
                         ezsurvey.language_code='" . $languageCode . "'";
        $rows = $db->arrayQuery( $query );
        $survey = false;
        if ( count( $rows ) > 0 )
        {
            $survey = eZSurvey::fetch( $rows[0]['id'] );
        }
        return $survey;
    }

    var $ID;
    var $Title;
    var $Enabled;
    var $Published;
    var $QuestionList;
    var $ValidFrom;
    var $ValidTo;
    var $RedirectCancel;
    var $RedirectSubmit;
}

?>
