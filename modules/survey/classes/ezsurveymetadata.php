<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
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
