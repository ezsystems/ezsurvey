<?php
//
// Definition of eZSurveyRelatedConfig class
//
// Created on: <20-Feb-2008 11:43:03 br>
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

/*! \file ezsurveyrelatedconfig.php
*/

/*!
  \class eZSurveyRelatedConfig ezsurveyrelatedconfig.php
  \brief The class eZSurveyRelatedConfig does

*/

class eZSurveyRelatedConfig extends eZPersistentObject
{
    /*!
     Constructor
    */
    function eZSurveyRelatedConfig( $row = array() )
    {
        $this->eZPersistentObject( $row );
    }

    /*!
      Definition of the related config.
    */
    static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'contentclass_id' => array( 'name' => 'ContentClassID',
                                                           'datatype' => 'integer',
                                                           'default' => 0,
                                                           'required' => true ),
                                         'node_id' => array( 'name' => 'NodeID',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ) ),
                      'keys' => array( 'id' ),
                      'function_attributes' => array(),
                      'increment_key' => 'id',
                      'class_name' => 'eZSurveyRelatedConfig',
                      'sort' => array( 'id' => 'asc' ),
                      'name' => 'ezsurveyrelatedconfig' );
    }

    /*!
      Fetch the configlist
    */
    static function fetchList()
    {
        $result = array();
        $conditions = array();
        $limitation = null;
        $asObject = true;
        return eZPersistentObject::fetchObjectList( eZSurveyRelatedConfig::definition(),
                                                    null,
                                                    $conditions, null, $limitation,
                                                    $asObject );
    }

    /*!
      \return a new surveyrelatedconfig.
    */
    static function create()
    {
        $row = array( 'id' => null,
                      'contentclass_id' => 0,
                      'node_id' => 0 );

        $surveyRelatedConfig = new eZSurveyRelatedConfig( $row );
        return $surveyRelatedConfig;
    }

}

?>
