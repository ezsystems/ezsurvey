<?php
/**
 * File containing the eZOracleTestSuite class
 *
 * @copyright Copyright (C) 2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package tests
 *
 * @todo add a suite of tests for cluster mode
 */

class eZSurveySuite extends ezpDatabaseTestSuite
{
    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZ Survey Extension Test Suite" );
        $this->addTestSuite( 'eZSurveyWizardRegression' );
    }

    public static function suite()
    {
        return new self();
    }
}

?>
