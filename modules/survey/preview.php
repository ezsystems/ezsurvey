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
$surveyID = $Params['SurveyID'];

$survey = eZSurvey::fetch( $surveyID );

if ( !$survey )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

$res = eZTemplateDesignResource::instance();
$res->setKeys( array( array( 'survey', $surveyID ) ) );

$tpl = eZTemplate::factory();

$tpl->setVariable( 'preview', true );
$tpl->setVariable( 'survey', $survey );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:survey/view.tpl' );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'survey', 'Survey Preview' ) ) );

?>
