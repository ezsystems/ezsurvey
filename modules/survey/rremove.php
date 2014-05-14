<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

$http = eZHTTPTool::instance();
$Module = $Params['Module'];
$resultID = $Params['ResultID'];

$surveyResult = eZSurveyResult::fetch( $resultID );
$surveyID = 0;
if ( $surveyResult )
{
    $surveyID = $surveyResult->attribute( 'survey_id' );
    $surveyResult->remove();
}
$Module->redirectTo( 'survey/result_list/' . $surveyID );


?>
