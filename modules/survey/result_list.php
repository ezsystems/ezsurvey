<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
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

$tpl = eZTemplate::factory();

$tpl->setVariable( 'result_list', $resultList );
$tpl->setVariable( 'survey', $survey );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'count', $countList );

$tpl->setVariable( 'contentobject_id', $contentObjectID );
$tpl->setVariable( 'contentclassattribute_id', $contentClassAttributeID );
$tpl->setVariable( 'language_code', $languageCode );

$Result = array();
$Result['left_menu'] = 'design:parts/survey/menu.tpl';
$Result['content'] = $tpl->fetch( 'design:survey/result_list.tpl' );
$Result['path'] = array( array( 'url' => '/survey/list',
                                'text' => ezpI18n::tr( 'survey', 'Survey' ) ),
                         array( 'url' => '/survey/result/' .  $contentObjectID . '/' . $contentClassAttributeID .'/' . $languageCode,
                                'text' => ezpI18n::tr( 'survey', 'Result overview' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'survey', 'All' ) ) );


?>
