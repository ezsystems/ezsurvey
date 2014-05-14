<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

$surveyINI = eZINI::instance( 'ezsurvey.ini' );
$extensionDirectories = $surveyINI->variable( 'QuestionTypeSettings', 'ExtensionDirectories' );
if ( is_array( $extensionDirectories ) )
{
    foreach ( $extensionDirectories as $extensionName )
    {
        $settingsGroup = 'QuestionTypeSettings_' . $extensionName;
        if ( $surveyINI->hasVariable( $settingsGroup, 'QuestionTypeList' ) )
        {
            $questionList = $surveyINI->variable( $settingsGroup, 'QuestionTypeList' );
            foreach ( $questionList as $questionItem )
            {
                $fileName = eZExtension::baseDirectory() . '/' . $extensionName . '/modules/survey/classes/ezsurvey' . $questionItem . '.php';
                if ( file_exists( $fileName ) )
                {
                    require_once( $fileName );
                }
                else
                {
                    eZDebug::writeWarning( 'File does not exist: ' . $fileName, 'ezsurveyquestions.php' );
                }
            }
        }
        else
        {
            eZDebug::writeWarning( 'The SettingsGroup: \'' . $settingsGroup . '\' does not exist.', 'ezsurveyquestions.php' );
        }
    }
}

?>
