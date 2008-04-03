<?php
//
// Definition of eZSurveyQuestionMetaData class
//
// Created on: <24-Feb-2008 23:18:05 br>
//
// Copyright (C) 1999-2008 eZ Systems as. All rights reserved.
//
// This source file is part of the eZ Publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE included in
// the packaging of this file.
//
// Licencees holding a valid "eZ Publish professional licence" version 2
// may use this file in accordance with the "eZ Publish professional licence"
// version 2 Agreement provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ Publish professional licence" version 2 is available at
// http://ez.no/ez_publish/licences/professional/ and in the file
// PROFESSIONAL_LICENCE included in the packaging of this file.
// For pricing of this licence please contact us via e-mail to licence@ez.no.
// Further contact information is available at http://ez.no/company/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*! \file ezsurveymultiplechoicemetadata.php
*/

/*!
  \class eZSurveyQuestionMetaData ezsurveymultiplechoicemetadata.php
  \brief The class eZSurveyQuestionMetaData does

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
                      'sort' => array( 'id', 'asc' ),
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
