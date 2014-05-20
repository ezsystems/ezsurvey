<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

class eZSurveyWizardRegression extends ezpDatabaseTestCase
{
    /**
     * test importdatabase for different databases mysql, postgresql, oracle, etc
     * For oracle, the schema will be changed based on setting in dbschema.ini.append.php 
     * @return unknown_type
     */
    public function testImportDatabase()
    {
        $surveyWizard = eZSurveyWizard::instance();
        $surveyWizard->importDatabase();
        $this->assertTrue( $surveyWizard->databaseStatus() );
    }
}
?>