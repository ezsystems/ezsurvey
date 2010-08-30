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

/*! \file ezsurveyquestionresult.php
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
