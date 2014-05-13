<?php
/**
 * File containing the eZOracleTestSuite class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
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
