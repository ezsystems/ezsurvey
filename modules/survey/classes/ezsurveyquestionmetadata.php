<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

class eZSurveyQuestionMetaData extends eZPersistentObject
{
    /*!
     Constructor
    */
    function eZSurveyQuestionMetaData( $row = array() )
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
                                          'question_original_id' => array( 'name' => 'QuestionOriginalID',
                                                                           'datatype' => 'integer',
                                                                           'default' => 0,
                                                                           'required' => true ),
                                          'name' => array( 'name' => 'Name',
                                                           'datatype' => 'string',
                                                           'default' => '',
                                                           'required' => true ),
                                          'value' => array( 'name' => 'Value',
                                                            'datatype' => 'string',
                                                            'default' => '',
                                                            'required' => true ) ),
                      'keys' => array( 'id' ),
                      'increment_key' => 'id',
                      'class_name' => 'eZSurveyQuestionMetaData',
                      'sort' => array( 'id' => 'asc' ),
                      'name' => 'ezsurveyquestionmetadata' );
    }

    /*!
     \static
     Fetch Survey Question Meta Data object

     \param survey result id

     \return survey question metadata object
    */
    static function fetch( $id )
    {
        $object = eZPersistentObject::fetchObject( eZSurveyQuestionMetaData::definition(),
                                                   null,
                                                   array( 'id' => $id ) );
        return $object;
    }

/*!
     \static
     Fetch Survey Question Meta Data object

     \param survey result id

     \return survey question metadata object
    */
    static function fetchByQuestionID( $id )
    {
        $object = eZPersistentObject::fetchObject( eZSurveyQuestionMetaData::definition(),
                                                   null,
                                                   array( 'id' => $id ) );
        return $object;
    }

    static function instance( $resultID, $questionID, $questionOriginalID, $name = false, $value = false )
    {
        $rows = array( 'result_id' => $resultID,
                       'question_id' => $questionID,
                       'question_original_id' => $questionOriginalID );
        $surveyResult = eZPersistentObject::fetchObject( eZSurveyQuestionMetaData::definition(),
                                                             null,
                                                             array( 'result_id' => $resultID,
                                                                    'question_id' => $questionID,
                                                                    'question_original_id' => $questionOriginalID ) );
        if ( $surveyResult )
        {
            if ( $name !== false )
                $surveyResult->setAttribute( 'name', $name );

            if ( $value !== false )
                $surveyResult->setAttribute( 'value', $value );

            return $surveyResult;
        }

        if ( $name !== false )
            $rows['name'] = $name;

        if ( $value !== false )
            $rows['value'] = $value;

        return new eZSurveyQuestionMetaData( $rows );
    }



}

?>
