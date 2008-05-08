<?php
//
// Created on: <02-Apr-2004 00:00:00 Jan Kudlicka>
//
// Copyright (C) 1999-2008 eZ Systems as. All rights reserved.
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

/*! \file ezsurveyresult.php
*/

class eZSurveyResult extends eZPersistentObject
{
    function eZSurveyResult( $row = array(), $metaData = null )
    {
        $this->eZPersistentObject( $row );
        if ( $metaData == false )
        {
            $this->MetaData = null;
        }
        else
        {
            $this->MetaData = $metaData;
        }
    }

    static function definition()
    {
        $def = array( 'fields' => array ( 'id' => array( 'name' => 'ID',
                                                         'datatype' => 'integer',
                                                         'default' => 0,
                                                         'required' => true ),
                                          'survey_id' => array( 'name' => 'SurveyID',
                                                                'datatype' => 'integer',
                                                                'default' => 0,
                                                                'required' => true ),
                                          'user_id' => array( 'name' => 'UserID',
                                                              'datatype' => 'integer',
                                                              'default' => eZUser::anonymousId(),
                                                              'required' => false ),
                                          'tstamp' => array( 'name' => 'TStamp',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => false ),
                                          'user_session_id' => array( 'name' => 'UserSessionID',
                                                                      'datatype' => 'string',
                                                                      'default' => '',
                                                                      'required' => false ) ),
                      'keys' => array( 'id' ),
                      'function_attributes' => array( 'question_results' => 'fetchQuestionResultList' ),
                      'increment_key' => 'id',
                      'class_name' => 'eZSurveyResult',
                      'sort' => array( 'id', 'asc' ),
                      'name' => 'ezsurveyresult' );

        return $def;
    }

    static function exist( $surveyID, $userID = false, $contentObjectID = false, $contentClassAttributeID = false, $languageCode = false )
    {
        $db = eZDB::instance();
        $userSelectString = '';
        if( is_numeric( $userID ) )
        {
            $userID = $db->escapeString( $userID );
            $userSelectString = " AND user_id='$userID'";
        }

        if ( is_numeric( $contentObjectID ) and is_numeric( $contentClassAttributeID ) and $languageCode !== false )
        {
            $languageCode = $db->escapeString( $languageCode );
            $query = "SELECT ezsurveyresult.id FROM ezsurveyresult, ezsurvey WHERE
                             ezsurvey.id=ezsurveyresult.survey_id AND
                             contentobject_id='$contentObjectID' and
                             contentclassattribute_id='$contentClassAttributeID' AND
                             language_code='$languageCode'" . $userSelectString;

            $result = $db->arrayQuery( $query );
            if ( count( $result ) > 0 )
            {
                return true;
            }
        }
        else if ( is_numeric( $surveyID ) )
        {
            $query = "SELECT ezsurveyresult.id FROM ezsurveyresult WHERE survey_id='$surveyID'" . $userSelectString;

            $result = $db->arrayQuery( $query );
            if ( count( $result ) > 0 )
            {
                return true;
            }
        }

        return false;
    }


    static function instance( $surveyID, $userID = false )
    {
        if ( $userID )
        {
            $surveyResult = eZPersistentObject::fetchObject( eZSurveyResult::definition(),
                                                             null,
                                                             array( 'survey_id' => $surveyID,
                                                                    'user_id' => $userID ) );
            if ( $surveyResult )
            {
                return $surveyResult;
            }
        }

        return new eZSurveyResult( array( 'survey_id' => $surveyID ) );
    }

    function storeResult( $params )
    {
        $this->setAttribute( 'tstamp', time() );
        $this->store();
        $object = new eZSurveyMetaData( array( 'result_id' => $this->ID ) );
        if ( $this->MetaData !== null )
        {
            foreach( array_keys( $this->MetaData ) as $key )
            {
                $object->setAttribute( 'attr_name', $key );
                $object->setAttribute( 'attr_value', $this->MetaData[$key] );
                $object->store();
                $object->setAttribute( 'id', null );
            }
        }
        else
        {
            // to have stored result_id in ezsuveymetadata table too
            $object->setAttribute( 'attr_name', '' );
            $object->setAttribute( 'attr_value', '' );
            $object->store();
        }

        $survey = eZSurvey::fetch( $this->attribute( 'survey_id' ) );
        $survey->storeResult( $this->ID, $params );
    }

    /*!
     \static
     Fetch Survey Result object

     \param survey result id

     \return survey result object
    */
    static function fetch( $resultID )
    {
        $surveyResultObject = eZPersistentObject::fetchObject( eZSurveyResult::definition(),
                                                               null,
                                                               array( 'id' => $resultID ) );
        return $surveyResultObject;
    }



    /*!
     \static
      Fetch count of a survey result.
      \return integer count
    */
    static function fetchSurveyResultCount( $contentObjectID, $contentClassAttributeID, $languageCode )
    {
        $count = self::fetchResult( $contentObjectID, $contentClassAttributeID, $languageCode );
        return array( 'result' => $count );
    }


    /*!
     \static
      Fetch surveyresult object.
      \param SurveyResult id
      \return SurveyResult object
    */
    static function fetchSurveyResult( $id )
    {
        $surveyResultObject = self::fetch( $id );
        return array( 'result' => $surveyResultObject );
    }


    // returns true if user with $user_id id (or current if false) has posted
    // survey with $survey_id id, false otherwise.
    function &fetchAlreadyPosted( $survey_id, $user_id = false )
    {
        if ( $user_id === false )
        {
            $user_id = eZUser::currentUserID();
        }
        return array( 'result' => ( eZPersistentObject::fetchObject( eZSurveyResult::definition(),
                                                                     null,
                                                                     array( 'survey_id' => $survey_id,
                                                                            'user_id' => $user_id ) ) )? true: false );
    }

    /*!
     Get previous results for current survey.

     \return array of question results. false if persistent is set to 0, no previous results exists or anonymous user.
     */
    function &fetchQuestionResultList( $asObject = false, $persistent = false )
    {
        $rows = eZPersistentObject::fetchObjectList( eZSurveyQuestionResult::definition(),
                                                     null,
                                                     array( 'result_id' => $this->attribute( 'id' ) ),
                                                     false,
                                                     null,
                                                     $asObject );

        if ( $asObject )
        {
            return $rows;
        }
        $extraResultArray = array();
        if ( $persistent === true )
        {
            $extraResults = eZPersistentObject::fetchObjectList( eZSurveyQuestionMetaData::definition(),
                                                                 null,
                                                                 array( 'result_id' => $this->attribute( 'id' ) ),
                                                                 false,
                                                                 null,
                                                                 $asObject );
            if ( count( $extraResults ) > 0 )
            {
                foreach ( $extraResults as $extraResult )
                {
                    $extraResultArray[$extraResult['question_id']] = $extraResult;
                }
            }
        }

        $resultRows = array();
        foreach( $rows as $row )
        {
            if ( !isset ( $resultRows[(string)$row['question_id']] ) )
            {
                $resultRows[(string)$row['question_id']] = array();
            }
            $resultRows[(string)$row['question_id']]['content'][$row['text']] = $row['text'];
            $resultRows[(string)$row['question_id']]['text'] = $row['text'];

            if ( isset( $extraResultArray[(string)$row['question_id']] ) )
            {
                $resultRows[(string)$row['question_id']]['extra_info'] = $extraResultArray[(string)$row['question_id']];
            }
        }

        return $resultRows;
    }

    /*!
     \static
     Fetch Results for a specified query

     \param ContentClassAttributeID
     \param ContentObjectID
     \param LanguageCode
     \param Offset, default 0
     \param limit, default 15

     \return Array containing result objects.
    */
    static function &fetchResultArray( $contentObjectID, $contentClassAttributeID, $languageCode, $offset = 0, $limit = 15, &$count )
    {
        /*
        $surveyResult = eZPersistentObject::fetchObjectList( eZSurveyResult::definition(),
                                                    null,
                                                    array( 'survey_id' => $surveyID ),
                                                    array( 'tstamp' => true ),
                                                    array( 'length' => $limit,
                                                           'offset' => $offset ) );
        */

        $db = eZDB::instance();
        $contentObjectID = $db->escapeString( $contentObjectID );
        $contentClassAttributeID = $db->escapeString( $contentClassAttributeID );
        $languageCode = $db->escapeString( $languageCode );

        $limitArray = array( 'offset' => $offset,
                             'limit' => $limit );

        $query = "SELECT ezsurveyresult.* FROM
                         ezsurveyresult, ezsurvey WHERE
                         ezsurveyresult.survey_id=ezsurvey.id AND
                         contentclassattribute_id='" . $contentClassAttributeID . "' AND
                         contentobject_id='" . $contentObjectID . "' AND
                         language_code='" . $languageCode . "' ORDER BY ezsurveyresult.tstamp DESC";
        $resultList = $db->arrayQuery( $query, $limitArray );

        $queryCount = "SELECT count( ezsurveyresult.id ) as count FROM
                         ezsurveyresult, ezsurvey WHERE
                         ezsurveyresult.survey_id=ezsurvey.id AND
                         contentclassattribute_id='" . $contentClassAttributeID . "' AND
                         contentobject_id='" . $contentObjectID . "' AND
                         language_code='" . $languageCode . "' ORDER BY ezsurveyresult.tstamp DESC";
        $countArray = $db->arrayQuery( $queryCount );
        $count = $countArray[0]['count'];
        if ( is_array(  $resultList ) and count( $resultList ) > 0 )
        {
            foreach ( $resultList as $resultArray )
            {
                $result = new eZSurveyResult( $resultArray );
                $surveyResult[] = $result;
            }
        }
        return $surveyResult;
    }

    // static
    // if $offset is false, will return number of items
    // otherwise array, see close the function end.
    static function &fetchResult( $contentObjectID, $contentClassAttributeID, $languageCode, $offset = false, $metadata = false )
    {
        $db = eZDB::instance();
        if ( $offset !== false )
            $offset = (int) $offset;
        $contentObjectID = $db->escapeString( $contentObjectID );
        $contentClassAttributeID = $db->escapeString( $contentClassAttributeID );
        $languageCode = $db->escapeString( $languageCode );

        if ( $metadata == false )
        {
            $query = ' FROM ezsurveyresult, ezsurvey WHERE
                            ezsurveyresult.survey_id=ezsurvey.id AND
                            ezsurvey.contentobject_id=\'' . $contentObjectID . '\' AND
                            ezsurvey.contentclassattribute_id=\'' . $contentClassAttributeID . '\' AND
                            ezsurvey.language_code=\'' . $languageCode . '\'';
        }
        else
        {
            $query = ' FROM ezsurveyresult, ezsurveymetadata as m1, ezsurvey';
            for( $index=2; $index <= count( $metadata ); $index++ )
            {
                $query .= ', ezsurveymetadata as m';
                $query .= $index;
            }
            $query .= ' ezsurveyresult.survey_id=ezsurvey.id AND
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

                $query .= ' and m' . $index . '.attr_name=\'' . $key .
                          '\' and m' . $index . '.attr_value=\'' . $metadata[$key] . '\'';
            }
        }
        $countRows = $db->arrayQuery( 'SELECT count(distinct ezsurveyresult.id) as count' . $query );
        $count= $countRows[0]['count'];

        if ( $offset === false )
            return $count;

        $resultIDRows = $db->arrayQuery( 'SELECT distinct ezsurveyresult.id as result_id, ezsurveyresult.user_id as user_id' . $query, array ( 'limit' => 1,
                                                                                      'offset' => $offset ) );
        $resultID = false;
        $userID = false;
        // the key for $resultIDRows is not known:
        foreach ( array_keys( $resultIDRows ) as $key )
        {
            $resultID = $resultIDRows[$key]['result_id'];
            $userID = $resultIDRows[$key]['user_id'];
        }
        return array( 'count' => $count, 'result_id' => $resultID, 'user_id' => $userID );
    }

    // static
    // exports surveys
    // TODO: export metadata as well...
    static function exportCSV( $contentObjectID, $contentClassAttributeID, $languageCode )
    {
        $surveyString = '';
        function printLine( $list, $array )
        {
            $surveyString = '';
            $found = false;
            foreach( $list as $key )
            {
                if ( isset( $array[$key] ) )
                {
                    $surveyString .=  '"'.str_replace( '"', "'", $array[$key] ).'";';
                    $found = true;
                }
                else
                {
                    $surveyString .=  '"";';

                }
            }
            if ( $found === true )
                $surveyString .= "\n";
            return $surveyString;
        }
        $survey = eZSurvey::fetchByObjectInfo( $contentObjectID, $contentClassAttributeID, $languageCode );

        if ( !$survey || !$survey->published() || !$survey->enabled() || !$survey->valid() )
            return false;

        $questionList = $survey->fetchQuestionList();
        $questions = array();
        $indexList = array();
        foreach( array_keys( $questionList ) as $key )
        {
            if ( $questionList[$key]->canAnswer() )
            {
                $oldKey = $questionList[$key]->attribute( 'original_id' );
                $indexList[] = $oldKey;
                $questions[$oldKey] = $questionList[$key]->attribute( 'text' );
            }
        }
        $surveyString = printLine( $indexList, $questions );

        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT ezsurveyquestionresult.result_id as id, question_id, questionoriginal_id, text
                                  FROM ezsurveyquestionresult, ezsurveyresult, ezsurvey
                                  WHERE ezsurveyresult.id=ezsurveyquestionresult.result_id AND
                                        ezsurveyresult.survey_id=ezsurvey.id AND
                                        contentclassattribute_id='" . $contentClassAttributeID . "' AND
                                        contentobject_id='" . $contentObjectID . "' AND
                                        language_code='" . $languageCode . "'
                                  ORDER BY tstamp ASC, ezsurveyquestionresult.result_id ASC" );

        $extraQuery = "SELECT ezsurveyquestionmetadata.result_id as id, question_id, value
                                  FROM ezsurveyquestionmetadata, ezsurveyresult, ezsurvey
                                  WHERE ezsurveyresult.id=ezsurveyquestionmetadata.result_id AND
                                        ezsurveyresult.survey_id=ezsurvey.id AND
                                        contentclassattribute_id='" . $contentClassAttributeID . "' AND
                                        contentobject_id='" . $contentObjectID . "' AND
                                        language_code='" . $languageCode . "'
                                  ORDER BY tstamp ASC, ezsurveyquestionmetadata.result_id ASC";

        $extraResultArray = $db->arrayQuery( $extraQuery );
        $extraResultHash = array();
        foreach ( $extraResultArray as $extraResultItem )
        {
            $extraResultHash[$extraResultItem['id']][$extraResultItem['question_id']] = $extraResultItem['value'];
        }
        $oldID = false;
        $answers = array();
        foreach( array_keys( $rows ) as $key )
        {
            $row =& $rows[$key];
            if ( $oldID != $row['id'] )
            {
                if ( $oldID !== false )
                {
                    $surveyString .= printLine( $indexList, $answers );
                    unset( $answers );
                    $answers = array();
                }
                $oldID = $row['id'];
            }
            if ( isset( $answers[$row['question_id']] ) )
                $answers[$row['questionoriginal_id']] .= "; ".$row['text'];  // esp. for multiple check boxes
            else
                $answers[$row['questionoriginal_id']] = $row['text'];

            if ( isset( $extraResultHash[$row['id']][$row['questionoriginal_id']] ) )
            {
                $answers[$row['questionoriginal_id']] .= "; " . $extraResultHash[$row['id']][$row['questionoriginal_id']];
            }
        }
        if ( $oldID !== false )
        {
           $surveyString .= printLine( $indexList, $answers );
        }
        return $surveyString;
    }

    /*!
     \reimp
    */
    function remove( $conditions = null, $extraConditions = null )
    {
        foreach( $this->fetchQuestionResultList( true ) as $questionResult )
        {
            $questionResult->remove();
        }
        eZPersistentObject::remove();
    }

    var $ID;
    var $SurveyID;
    var $UserID;
    var $TStamp;
    var $Survey = false;
    var $MetaData;
}

?>
