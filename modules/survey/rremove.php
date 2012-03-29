<?php
//
// Definition of Rremove class
//
// Created on: <10-Jun-2005 15:55:33 sp>
//

// Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
//
// This source file is part of the eZ Publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE included in
// the packaging of this file.
//
// Licencees holding a valid "eZ Publish professional licence" version 2
// may use this file in accordance with the "eZ Publish professional licence"
// version 2 Agreement provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ Publish professional licence" version 2 is available at
// http://ez.no/ez_publish/licences/professional/ and in the file
// PROFESSIONAL_LICENCE included in the packaging of this file.
// For pricing of this licence please contact us via e-mail to licence@ez.no.
// Further contact information is available at http://ez.no/company/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*! \file rremove.php
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
