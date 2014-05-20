<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

class eZSurveySectionHeader extends eZSurveyQuestion
{
    /*!
      Constructor of the survey.
    */
    function eZSurveySectionHeader( $row = false )
    {
        $row['type'] = 'SectionHeader';
        $this->eZSurveyQuestion( $row );
    }

    /*!
      This is a section and should not require an answer.
    */
    function canAnswer()
    {
        return false;
    }

    /*!
      Iterate the number of the question.
    */
    function questionNumberIterate( &$iterator )
    {
    }
}

eZSurveyQuestion::registerQuestionType( ezpI18n::tr( 'survey', 'Section Header' ), 'SectionHeader' );

?>
