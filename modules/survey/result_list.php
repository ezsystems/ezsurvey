<?php
//
// Created on: <28-Jun-2004 15:00:00 kk>
//
// Copyright (C) 1999-2008 eZ Systems as. All rights reserved.
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

/*! \file result_list.php
*/

$http = eZHTTPTool::instance();
$contentObjectID = $Params['ContentObjectID'];
$contentClassAttributeID = $Params['ContentClassAttributeID'];
$languageCode = $Params['LanguageCode'];

$offset = $Params['Offset'];
if ( !$offset )
    $offset = 0;


$limit = 25;
$viewParameters['offset'] = $offset;

$http = eZHTTPTool::instance();
if ( $http->hasPostVariable( 'ExportCSVButton' ) )
{
    $href = 'survey/export/' . $contentObjectID . '/' . $contentClassAttributeID . '/' . $languageCode;
    $status = eZURI::transformURI( $href );
    if ( $status === true )
    {
        $http->redirect( $href );
    }
}

if ( $http->hasPostVariable( 'RemoveButton' ) )
{
    if ( $http->hasPostVariable( 'DeleteIDArray' ) )
    {
        foreach( $http->postVariable( 'DeleteIDArray' ) as $resultID )
        {
            $surveyResult = eZSurveyResult::fetch( $resultID );
            if ( is_object( $surveyResult ) )
            {
                $surveyResult->remove();
            }
        }
    }
}

$resultList = eZSurveyResult::fetchResultArray( $contentObjectID, $contentClassAttributeID, $languageCode, $offset, $limit, $countList );

$survey = eZSurvey::fetchByObjectInfo( $contentObjectID, $contentClassAttributeID, $languageCode );

require_once( 'kernel/common/template.php' );
$tpl = templateInit();

$tpl->setVariable( 'result_list', $resultList );
$tpl->setVariable( 'survey', $survey );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'count', $countList );

$tpl->setVariable( 'contentobject_id', $contentObjectID );
$tpl->setVariable( 'contentclassattribute_id', $contentClassAttributeID );
$tpl->setVariable( 'language_code', $languageCode );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:survey/result_list.tpl' );
$Result['path'] = array( array( 'url' => '/survey/list',
                                'text' => ezi18n( 'survey', 'Survey' ) ),
                         array( 'url' => '/survey/result/' .  $contentObjectID . '/' . $contentClassAttributeID .'/' . $languageCode,
                                'text' => ezi18n( 'survey', 'Result overview' ) ),
                         array( 'url' => false,
                                'text' => ezi18n( 'survey', 'All' ) ) );


?>
