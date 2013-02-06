<?php
//
// Created on: <02-Apr-2004 00:00:00 Jan Kudlicka>
//
// Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
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

/*! \file ezsurveymetadata.php
*/

class eZSurveyMetaData extends eZPersistentObject
{
    function eZSurveyMetaData( $row = false )
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
                                          'attr_name' => array( 'name' => 'AttrName',
                                                                'datatype' => 'string',
                                                                'default' => '',
                                                                'required' => false ),
                                          'attr_value' => array( 'name' => 'AttrValue',
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => false ) ),
                      // 'function_attributes' => array( 'template_name' => 'templateName' ),
                      'keys' => array( 'id' ),
                      'increment_key' => 'id',
                      'class_name' => 'eZSurveyMetaData',
                      'name' => 'ezsurveymetadata' );
    }

    var $ID;
    var $ResultID;
    var $AttrName;
    var $AttrValue;
}

?>
