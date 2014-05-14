<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

class eZSurveyQuestionResult extends eZPersistentObject
{
    function eZSurveyQuestionResult( $row )
    {
        $this->eZPersistentObject( $row );
    }

    static function definition()
    {
        return array( 'fields' => array ( 'id' => array( 'name' => 'ID',
                                                         'datatype' => 'integer',
                                                         'default' => 0,
                                                         'required' => true ),
                                          'result_id' => array( 'name' => 'ResultID',
                                                                'datatype' => 'integer',
                                                                'default' => 0,
                                                                'required' => true ),
                                          'question_id' => array( 'name' => 'QuestionID',
                                                                'datatype' => 'integer',
                                                                'default' => 0,
                                                                'required' => true ),
                                          'questionoriginal_id' => array( 'name' => 'QuestionOriginalID',
                                                                          'datatype' => 'integer',
                                                                          'default' => 0,
                                                                          'required' => true ),
                                          'text' => array( 'name' => 'Text',
                                                           'datatype' => 'string',
                                                           'default' => '',
                                                           'required' => false ) ),
                      'keys' => array( 'id' ),
                      'increment_key' => 'id',
                      'class_name' => 'eZSurveyQuestionResult',
                      'sort' => array( 'id' => 'asc' ),
                      'name' => 'ezsurveyquestionresult' );
    }

    /*!
      Store the question result. If the original questionid for the first version is not set,
      it will be copied from the survey question. This makes it possible to fetch all question results
      for several content object versions, which has different survey object id.
    */
    function store( $fieldFilters = null )
    {
        if ( $this->QuestionOriginalID == 0 )
        {
            $db = eZDB::instance();
            $id = $this->QuestionID;
            $question = eZSurveyQuestion::fetch( $id );
            if ( get_class( $question ) == 'eZSurveyQuestion' )
                $this->QuestionOriginalID = $question->attribute( 'original_id' );
        }
        parent::store( $fieldFilters );
    }

    static function instance( $resultID, $questionID, $count = 1 )
    {
        $questionResultArray = eZPersistentObject::fetchObjectList( eZSurveyQuestionResult::definition(),
                                                                    null,
                                                                    array( 'result_id' => $resultID,
                                                                           'question_id' => $questionID ) );

        if ( !$questionResultArray )
        {
            $questionResultArray = array();
        }

        $questionArrayCount = count( $questionResultArray );
        for ( $idx = $count; $idx < $questionArrayCount; ++$idx )
        {
            $questionResultArray[$idx]->remove();
            unset( $questionResultArray[$idx] );
        }

        $questionArrayCount = count( $questionResultArray );
        for ( $idx = $questionArrayCount; $idx < $count; ++$idx )
        {
            $questionResultArray[] = new eZSurveyQuestionResult( array ( 'result_id' => $resultID,
                                                                         'question_id' => $questionID ) );
        }

        return $questionResultArray;
    }

    function remove( $conditions = null, $extraConditions = null )
    {
        eZPersistentObject::removeObject( eZSurveyQuestionResult::definition(),
                                          array( 'id' => $this->ID ) );
    }

    var $ID;
    var $ResultID;
    var $QuestionID;
    var $Text;
}

?>
