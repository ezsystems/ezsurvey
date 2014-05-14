<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
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
