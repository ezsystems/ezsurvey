<?php
//
// Definition of eZSurveyWizard class
//
// Created on: <11-Jun-2008 14:58:01 br>
//
// Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE included in
// the packaging of this file.
//
// Licencees holding a valid "eZ publish professional licence" version 2
// may use this file in accordance with the "eZ publish professional licence"
// version 2 Agreement provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" version 2 is available at
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

/*! \file ezsurveywizard.php
*/

/*!
  \class eZSurveyWizard ezsurveywizard.php
  \brief The class eZSurveyWizard does

*/

class eZSurveyWizard
{
    /*!
     Constructor
    */
    private function __construct()
    {
        $this->DBStatus = null;
        $this->setDatabaseStatus();
    }


    function attribute( $value )
    {
        $ret = false;
        switch ( $value )
        {
            case "content_class_list":
            {
                $ret = $this->contentClassList();
            }break;
        }
        return $ret;
    }

    private function contentClassList()
    {
        $db = eZDB::instance();
        $query = "SELECT ezcontentclass.id
                  FROM   ezcontentclass,
                         ezcontentclass_attribute ezcontentclass_attribute1,
                         ezcontentclass_attribute ezcontentclass_attribute2
                  WHERE  ezcontentclass.version='0' AND
                         ezcontentclass.id=ezcontentclass_attribute1.contentclass_id AND
                         ezcontentclass_attribute1.is_information_collector='0' AND
                         ezcontentclass.id=ezcontentclass_attribute2.contentclass_id AND
                         ezcontentclass_attribute2.is_information_collector='0' AND
                         ezcontentclass_attribute1.contentclass_id=ezcontentclass_attribute2.contentclass_id AND
                         ezcontentclass_attribute1.data_type_string='ezxmltext' AND
                         ezcontentclass_attribute2.data_type_string='eztext'";
        $contentClassArray = $db->arrayQuery( $query );
        $retArray = array();

        foreach ( $contentClassArray as $contentClass )
        {
            $retArray[] = eZContentClass::fetch( $contentClass['id'] );
        }
        return $retArray;
    }

    static function instance()
    {
        if ( !empty( $GLOBALS["eZSurveyGlobalWizardInstance"] ) )
        {
            return $GLOBALS["eZSurveyGlobalWizardInstance"];
        }

        $surveyWizard = new eZSurveyWizard();
        $GLOBALS["eZSurveyGlobalWizardInstance"] = $surveyWizard;

        return $surveyWizard;
    }

    function setDatabaseStatus()
    {
        $db = eZDB::instance();
        $status = true;
        $eZTableList = $db->eZTableList();
        $this->DBStatus = true;

        foreach ( $this->tableList() as $table )
        {
            if ( !isset( $eZTableList[$table] ) )
            {
                $status = false;
                $this->DBStatus = $status;
                break;
            }
        }
    }

    function databaseStatus()
    {
        return $this->DBStatus;
    }

    function importDatabase()
    {
        $dbaFilePath = eZExtension::baseDirectory() . '/ezsurvey/share/db_schema.dba';
        if ( file_exists( $dbaFilePath ) )
        {
            $dbaArray = eZDbSchema::read( $dbaFilePath, true );
            if ( is_array( $dbaArray ) and count( $dbaArray ) > 0 )
            {
                $db = eZDB::instance();
                $dbaArray['type'] = strtolower( $db->databaseName() );
                $dbaArray['instance'] =& $db;

                $dbSchema = eZDbSchema::instance( $dbaArray );

                $result = false;
                if ( $dbSchema )
                {
                    // Before adding the schema, make sure that the tables are removed.
                    if ( $this->cleanDBBeforeImport() )
                    {
                        // This will insert the data and
                        // run any sequence value correction SQL if required
                        $result = $dbSchema->insertSchema( array( 'schema' => true,
                                                                  'data' => true ) );
                        // only in mysql the InnoDB update is needed
                        if ( $db->databaseName() === 'mysql' )
                        {
                            $this->UpdateInnoDB();
                        }
                        $this->setDatabaseStatus();
                    }
                }
            }
        }
    }


    function state()
    {
        $state = 'none';
        if ( $this->databaseStatus() === false )
        {
            $state = 'database';
        }
        else if ( $this->hasSurveyClassStatus() === false )
        {
            $state = 'survey_class';
        }
        else if ( count( $this->contentClassList() ) == 0 )
        {
            $state = 'survey_classattribute';
        }
        else if ( $this->hasSurveyClassAttributeStatus( 'content_class' ) === false )
        {
            $state = 'conf_survey_classattribute';
        }
        else if ( $this->hasSurveyClassAttributeStatus( 'content_node' ) === false )
        {
            $state = 'conf_survey_classattribute_parent';
        }
        return $state;
    }

    private function updateInnoDB()
    {
        $db = eZDB::instance();
        $db->begin();
        foreach ( $this->tableList() as $table )
        {
            $query = "ALTER TABLE $table ENGINE = innodb";
            $db->query( $query );
        }
        $db->commit();
    }

    private function hasSurveyClassAttributeStatus( $configStatus )
    {
        if ( isset( $this->SurveyAttribute ) )
        {
            $surveyAttribute = $this->SurveyAttribute;
        }
        else
        {
            $db = eZDB::instance();
            $query = "SELECT * FROM ezsurveyrelatedconfig";
            $surveyAttribute = $db->arrayQuery( $query );
            $this->SurveyAttribute = $surveyAttribute;
        }

        $status = false;
        if ( count( $surveyAttribute ) > 0 )
        {
            if ( $configStatus == 'content_class' and
                 $surveyAttribute[0]['contentclass_id'] > 0 )
            {
                $status = true;
            }
            else if ( $configStatus == 'content_node' and
                      $surveyAttribute[0]['node_id'] > 0 )
            {
                $status = true;
            }
        }
        return $status;
    }


    private function hasSurveyClassStatus()
    {
        $db = eZDB::instance();
        $query = "SELECT id FROM ezcontentclass_attribute where data_type_string='ezsurvey'";
        $classAttributes = $db->arrayQuery( $query );

        $status = false;
        if ( count( $classAttributes ) > 0  )
        {
            $status = true;
        }
        return $status;
    }


    private function cleanDBBeforeImport()
    {
        $res = array();
        $db = eZDB::instance();
        $eZTableList = $db->eZTableList();
        $db->begin();
        foreach ( $this->tableList() as $table )
        {
            if ( isset( $eZTableList[$table] ) )
            {
                $query = "DROP TABLE " . $table;
                $res[] = $db->query( $query );
            }
        }
        $db->commit();
        $status = !in_array( false, $res );
        return $status;
    }


    private function tableList()
    {
        return array( 'ezsurvey', 'ezsurveyquestion', 'ezsurveyresult', 'ezsurveyquestionresult', 'ezsurveymetadata',
                      'ezsurveyrelatedconfig', 'ezsurveyquestionmetadata' );
    }


    function importPackage()
    {
        $surveyINI = eZINI::instance( 'ezsurvey.ini' );
        $packageName = $surveyINI->variable( 'PackageSettings', 'PackageName' );
        $packagePath = $surveyINI->variable( 'PackageSettings', 'PackagePath' );
        $fileName = $surveyINI->variable( 'PackageSettings', 'PackageFileName' );

        $path = eZExtension::baseDirectory() . '/' . $packagePath . '/';
        $file = $path . $fileName;

        if ( file_exists( $file ) )
        {
            $package = eZPackage::import( $file, $packageName );

            if ( is_object( $package ) )
            {
                $status = $this->installPackage( $package );
            }
            else if ( $package == eZPackage::STATUS_ALREADY_EXISTS )
            {
                $package = eZPackage::fetch( $packageName );
                if ( is_object( $package ) )
                {
                    $status = $this->installPackage( $package );
                }
                else
                {
                    eZDebug::writeError( "Could not fetch package: $packageName", 'eZSurveyWizard::importPackage' );
                }
            }
            else
            {
                eZDebug::writeError( "Uploaded file is not an eZ Publish package", 'eZSurveyWizard::importPackage' );
            }
        }
        else
        {
            eZDebug::writeWarning( 'File "' . $file . '" does not exist', 'eZSurveyWizard::importPackage' );
        }
    }


    private function installPackage( $package )
    {
        $persistentData = array();
        $persistentData['package_name'] = $package->attribute( 'name' );
        $persistentData['currentItem'] = 0;
        $persistentData['doItemInstall'] = true;
        $persistentData['error'] = array();
        $persistentData['error_default_actions'] = array();

        $installItemArray = $package->installItemsList( false, eZSys::osType() );
        foreach ( $installItemArray as $installItem )
        {
            $installer = eZPackageInstallationHandler::instance( $package, $installItem['type'], $installItem );

            if ( !$installer || isset( $persistentData['error']['choosen_action'] ) )
            {
                $result = $package->installItem( $installItem, $persistentData );

                if ( !$result )
                {
                    $templateName = "design:package/install_error.tpl";
                    break;
                }
                else
                {
                    $persistentData['error'] = array();
                }
            }
            else
            {
                $persistentData['doItemInstall'] = false;
                $installer->generateStepMap( $package, $persistentData );
                $displayStep = true;
                break;
            }
        }
        $package->setInstalled();
    }
}

?>
