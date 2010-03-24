<?php
//
// Created on: <02-Apr-2004 00:00:00 Jan Kudlicka>
//
// Copyright (C) 1999-2008 eZ Systems AS. All rights reserved.
//
// This source file is part of the eZ Publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE.GPL included in
// the packaging of this file.
//
// Licencees holding valid "eZ Publish professional licences" may use this
// file in accordance with the "eZ Publish professional licence" Agreement
// provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ Publish professional licence" is available at
// http://ez.no/products/licences/professional/. For pricing of this licence
// please contact us via e-mail to licence@ez.no. Further contact
// information is available at http://ez.no/home/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*! \file export.php
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
    echo ezi18n( 'survey', 'No results' );
    echo "\n";
}

// fflush();
eZExecution::cleanExit();

?>