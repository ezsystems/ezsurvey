<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

$Module = $Params['Module'];

$surveyID = $Params['SurveyID'];
$survey = eZSurvey::fetch( $surveyID );
$newSurvey =& $survey->cloneSurvey();

$Module->redirectTo( '/survey/edit/'.$newSurvey->attribute( 'id' ) );

?>