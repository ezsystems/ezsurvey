<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

$Module = $Params['Module'];
$contentObjectID = $Params['ContentObjectID'];
$contentClassAttributeID = $Params['ContentClassAttributeID'];
$languageCode = $Params['LanguageCode'];

$survey = eZSurvey::fetchByObjectInfo( $contentObjectID, $contentClassAttributeID, $languageCode );
if ( !is_object( $survey ) )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

// $surveyID = $survey->attribute( 'id' );

// $db = eZDB::instance();
// $db->setIsSQLOutputEnabled( false );

// $http = eZHTTPTool::instance();


$output = eZSurveyResult::exportCSV( $contentObjectID, $contentClassAttributeID, $languageCode );
if ( $output !== false )
{
    $contentLength = strlen( $output );
    header( "Pragma: " );
    header( "Cache-Control: " );
    header( "Content-Length: $contentLength" );
    // header( "Content-Type: application/vnd.ms-excel" );
    header( "Content-Type: text/comma-separated-values" );
    header( "X-Powered-By: eZ Publish" );
    header( "Content-disposition: attachment; filename=export.csv" );
    // header( "Content-Transfer-Encoding: binary" );
    ob_end_clean();
    print( $output );
}
else
{
    echo ezpI18n::tr( 'survey', 'No results' );
    echo "\n";
}

// fflush();
eZExecution::cleanExit();

?>