<?php
//
// Created on: <02-Apr-2004 00:00:00 Jan Kudlicka>
//
// Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
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

/*! \file list.php
 */

$http = eZHTTPTool::instance();

$Module = $Params['Module'];
$surveyWizard = eZSurveyWizard::instance();
if ( $surveyWizard->state() != 'none' )
{
    $uri = 'survey/wizard';
    $Module->redirectTo( $uri );
}
else
{
    $offset = $Params['Offset'];
    if ( !$offset )
        $offset = 0;
    $limit = 25;

    $params = array( 'offset' => $offset,
                     'limit' => $limit );
    $count = 0;
    $surveyList = eZSurvey::fetchSurveyList( $params, $count );

    $viewParameters = array( 'offset' => $offset );

    require_once( 'kernel/common/template.php' );
    $tpl = templateInit();

    $tpl->setVariable( 'survey_list', $surveyList );
    $tpl->setVariable( 'count', $count );
    $tpl->setVariable( 'limit', $limit );
    $tpl->setVariable( 'view_parameters', $viewParameters );

    $Result = array();
    $Result['left_menu'] = 'design:parts/survey/menu.tpl';
    $Result['content'] = $tpl->fetch( 'design:survey/list.tpl' );
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezpI18n::tr( 'survey', 'Survey' ) ) );
}
?>
