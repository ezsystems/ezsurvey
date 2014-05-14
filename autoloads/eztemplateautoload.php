<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

$eZTemplateOperatorArray = array();

$eZTemplateOperatorArray[] = array( 'script' => eZExtension::baseDirectory() . '/ezsurvey/modules/survey/classes/ezsurveyoperators.php',
                                    'class' => 'eZSurveyOperators',
                                    'operator_names' => array( 'number' ) );

$eZTemplateFunctionArray[] = array( 'function' => 'eZSurveyForwardInit',
                                    'function_names' => array( 'survey_question_edit_gui',
                                                               'survey_question_view_gui',
                                                               'survey_question_result_gui' ) );

if ( !function_exists( 'eZSurveyForwardInit' ) )
{
    function &eZSurveyForwardInit()
    {
        $forward_rules = array(
            'survey_question_edit_gui' => array( 'template_root' => 'survey/edit',
                                                 'input_name' => 'question',
                                                 'output_name' => 'question',
                                                 'namespace' => 'SurveyQuestion',
                                                 'attribute_access' => array( array( 'template_name' ) ),
                                                 'use_views' => false ),

            'survey_question_view_gui' => array( 'template_root' => 'survey/view',
                                                 'input_name' => 'question',
                                                 'output_name' => 'question',
                                                 'namespace' => 'SurveyQuestion',
                                                 'attribute_access' => array( array( 'template_name' ) ),
                                                 'use_views' => false ),

            'survey_question_result_gui' => array( 'template_root' => 'survey/result',
                                                   'input_name' => 'question',
                                                   'output_name' => 'question',
                                                   'namespace' => 'SurveyQuestion',
                                                   'attribute_access' => array( array( 'template_name' ) ),
                                                   'use_views' => 'view' ) );

        $forwarder = new eZObjectForwarder( $forward_rules );
        return $forwarder;
    }
}

?>
