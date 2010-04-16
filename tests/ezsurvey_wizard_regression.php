<?php

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