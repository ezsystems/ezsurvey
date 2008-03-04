<?php
//
// Definition of eZSurveyRelatedObject class
//
// Created on: <17-Feb-2008 20:40:08 br>
//
// Copyright (C) 1999-2008 eZ Systems as. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE included in
// the packaging of this file.
//
// Licencees holding a valid "eZ publish professional licence" version 2
// may use this file in accordance with the "eZ publish professional licence"
// version 2 Agreement provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" version 2 is available at
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

/*! \file ezsurveyrelatedobject.php
*/

/*!
  \class eZSurveyRelatedObject ezsurveyrelatedobject.php
  \brief The class eZSurveyRelatedObject does

*/

class eZSurveyRelatedObject extends eZSurveyQuestion
{
    /*!
     Constructor
    */
    function eZSurveyRelatedObject( $row = false )
    {
        $row['type'] = 'RelatedObject';
        $this->eZSurveyQuestion( $row );

        $surveyID = $this->attribute( 'survey_id' );
        $survey = eZSurvey::fetch( $surveyID );
        $contentObjectID = $survey->attribute( 'contentobject_id' );

        $http = eZHTTPTool::instance();
        $module = $GLOBALS['module'];

//         $http->removeSessionVariable( 'LastAccessesURI' );
//         $http->removeSessionVariable( 'RedirectURIAfterPublish' );
//         $http->removeSessionVariable( 'RedirectIfDiscarded' );

        if ( $module->exitStatus() !== eZModule::STATUS_REDIRECT )
        {
            if ( $http->hasSessionVariable( 'LastAccessesURI_Backup_' . $contentObjectID ) and
                 $http->sessionVariable( 'LastAccessesURI_Backup_' . $contentObjectID ) !== null )
            {
                $value = $http->sessionVariable( 'LastAccessesURI_Backup_' . $contentObjectID );
                $http->setSessionVariable( 'LastAccessesURI', $value['content'] );
                $http->removeSessionVariable( 'LastAccessesURI_Backup_' . $contentObjectID );
            }

            if ( $http->hasSessionVariable( 'RedirectURIAfterPublish_Backup_' . $contentObjectID ) and
                 $http->sessionVariable( 'RedirectURIAfterPublish_Backup_' . $contentObjectID ) !== null )
            {
                $value = $http->sessionVariable( 'RedirectURIAfterPublish_Backup_' . $contentObjectID );
                $http->setSessionVariable( 'RedirectURIAfterPublish', $value['content'] );
                $http->removeSessionVariable( 'RedirectURIAfterPublish_Backup_' . $contentObjectID );
            }

            if ( $http->hasSessionVariable( 'RedirectIfDiscarded_Backup_' . $contentObjectID ) and
                 $http->sessionVariable( 'RedirectIfDiscarded_Backup_' . $contentObjectID ) !== null )
            {
                $value = $http->sessionVariable( 'RedirectIfDiscarded_Backup_' . $contentObjectID );
                $http->setSessionVariable( 'RedirectIfDiscarded', $value['content'] );
                $http->removeSessionVariable( 'RedirectIfDiscarded_Backup_' . $contentObjectID );
            }
        }
    }

    function handleAttributeHTTPAction( $http, $action, $objectAttribute, $parameters )
    {
        $returnValue = false;
        $postSurveyMCNewAction = 'ezsurvey_related_object_' . $this->ID . '_add';
        if ( $postSurveyMCNewAction == $action )
        {
            $returnValue = $this->createNewRelatedObject( $http, $action, $objectAttribute, $parameters );
        }

        return $returnValue;
    }

    /*!
      Create a new contentobject and redirect to the new object to fill in information
    */
    function createNewRelatedObject( $http, $action, $objectAttribute, $parameters )
    {
        $hasClassInformation = false;
        $contentClassID = false;
        $contentClassIdentifier = false;
        $languageCode = false;
        $class = false;
        $languageCode = $objectAttribute->attribute( 'language_code' );
        $originalContentObjectID = $objectAttribute->attribute( 'contentobject_id' );
        $originalContentObjectVersion = $objectAttribute->attribute( 'version' );

        $contentObject = false;
        $addVersion = false;
        if ( $this->Num != 0 )
        {
            $contentObject = eZContentObject::fetch( $this->Num );
        }

        if ( $this->Num == 0 or !( get_class( $contentObject ) ==  'eZContentObject' ) )
        {
            $addVersion = true;
            $ini = eZINI::instance( 'ezsurvey.ini' );
            $configList = eZSurveyRelatedConfig::fetchList();
            if ( count( $configList ) > 0 )
            {
                $config = $configList[0];
                $contentClassID = $config->attribute( 'contentclass_id' );
                $contentClass = eZContentClass::fetch( $contentClassID );

                $nodeID = $config->attribute( 'node_id' );
                $attributeParentNode = eZContentObjectTreeNode::fetch( $nodeID );

                if ( ( get_class( $contentClass ) == 'eZContentClass' ) and
                     ( get_class( $attributeParentNode ) ==  'eZContentObjectTreeNode' ) )
                {
                    $languageID = eZContentLanguage::idByLocale( $languageCode );
                    $contentObject = eZContentObject::fetch( $objectAttribute->attribute( 'contentobject_id' ) );
                    $node = eZContentObjectTreeNode::fetch( $nodeID, $languageCode );

                    if ( get_class( $node ) == "eZContentObjectTreeNode" )
                    {
                        $contentObject = eZContentObject::createWithNodeAssignment( $node,
                                                                                    $contentClassID,
                                                                                    $languageCode,
                                                                                    false );
                    }
                    else
                    {
                        eZDebug::writeWarning( 'node is not a valid eZContentObjectTreeNode', 'eZSurveyRelatedObject::createNewRelatedObject' );
                    }
                }
                else
                {
                    eZDebug::writeWarning( 'Config is not valid', 'eZSurveyRelatedObject::createNewRelatedObject' );
                }
            }
        }

        if ( $contentObject )
        {
            $redirectHref = 'content/edit/' . $originalContentObjectID . '/' . $originalContentObjectVersion;

            $http->setSessionVariable( 'LastAccessesURI_Backup_' . $originalContentObjectID, array( 'content' => $http->sessionVariable( 'LastAccessesURI' ) ) );
            $http->setSessionVariable( 'RedirectURIAfterPublish_Backup_' . $originalContentObjectID, array( 'content' => $http->sessionVariable( 'RedirectURIAfterPublish' ) ) );
            $http->setSessionVariable( 'RedirectIfDiscarded_Backup_' . $originalContentObjectID, array( 'content' => $http->sessionVariable( 'RedirectIfDiscarded' ) ) );

            $http->setSessionVariable( 'LastAccessesURI', $redirectHref );
            $http->setSessionVariable( 'RedirectURIAfterPublish', $redirectHref );
            $http->setSessionVariable( 'RedirectIfDiscarded', $redirectHref );

            $this->Num = $contentObject->attribute( 'id' );
            $this->store();
            $parameters = array( $contentObject->attribute( 'id' ) );
            if ( $addVersion === true )
                $parameters[] = $contentObject->attribute( 'current_version' );

            $module = $GLOBALS['module'];
            $module->redirectToView( 'edit', $parameters );
        }

        return true;
    }

    /*!
      This is a related object and should not require an answer.
      \return false
    */
    function canAnswer()
    {
        return false;
    }

    /*!
      Iterate the number of the question. Since the related object do not have any input fields,
      The iterator should not be runned.
    */
    function questionNumberIterate( &$iterator )
    {
    }

    function &cloneQuestion( $surveyID, $resetOriginalID = false )
    {
        $num = $resetOriginalID === false ? $this->Num : 0;
        $row = array( 'id' => null,
                      'survey_id' => $surveyID,
                      'original_id' => $this->OriginalID,
                      'tab_order' => $this->TabOrder,
                      'type' => $this->Type,
                      'mandatory' => $this->Mandatory,
                      'default_value' => $this->Default,
                      'text' => $this->Text,
                      'text2' => $this->Text2,
                      'text3' => $this->Text3,
                      'num' => $num,
                      'num2' => $this->Num2 );
        $classname = implode( '', array( 'eZSurvey', $this->Type ) );
        $cloned = new $classname( $row );
        $cloned->store();
        if ( $resetOriginalID === true )
        {
            $cloned->setAttribute( 'original_id', $cloned->attribute( 'id' ) );
            $cloned->store();
        }
        return $cloned;
    }
}

eZSurveyQuestion::registerQuestionType( 'Related object', 'RelatedObject' );

?>
