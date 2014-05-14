<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
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
