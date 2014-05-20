<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
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

        $postRelatedObject = 'SelectedNodeIDArray';
        $http = eZHTTPTool::instance();

        if ( $http->hasPostVariable( $postRelatedObject ) )
        {
            // need to do an extra check if this is the datatype that should receive the information.
            $value = $http->postVariable( $postRelatedObject );
        }

        $http = eZHTTPTool::instance();
        $module = $GLOBALS['eZRequestedModule'];

//         $http->removeSessionVariable( 'LastAccessesURI' );
//         $http->removeSessionVariable( 'RedirectURIAfterPublish' );
//         $http->removeSessionVariable( 'RedirectIfDiscarded' );

        if ( $module->exitStatus() !== eZModule::STATUS_REDIRECT )
        {
            if ( $http->hasSessionVariable( 'LastAccessesURI_Backup_' . $contentObjectID . '_' . $this->ID ) and
                 $http->sessionVariable( 'LastAccessesURI_Backup_' . $contentObjectID . '_' . $this->ID ) !== null )
            {
                $value = $http->sessionVariable( 'LastAccessesURI_Backup_' . $contentObjectID . '_' . $this->ID);
                $http->setSessionVariable( 'LastAccessesURI', $value['content'] );
                $http->removeSessionVariable( 'LastAccessesURI_Backup_' . $contentObjectID . '_' . $this->ID );

                if ( is_numeric( $this->Num ) and $this->Num > 0 )
                {
                    $contentObjectExists = eZContentObject::exists( $this->Num );
                    if ( $contentObjectExists !== true )
                    {
                        $this->Num = 0;
                        $this->store();
                    }
                }
            }

            if ( $http->hasSessionVariable( 'RedirectURIAfterPublish_Backup_' . $contentObjectID . '_' . $this->ID ) and
                 $http->sessionVariable( 'RedirectURIAfterPublish_Backup_' . $contentObjectID . '_' . $this->ID ) !== null )
            {
                $value = $http->sessionVariable( 'RedirectURIAfterPublish_Backup_' . $contentObjectID . '_' . $this->ID );
                $http->setSessionVariable( 'RedirectURIAfterPublish', $value['content'] );
                $http->removeSessionVariable( 'RedirectURIAfterPublish_Backup_' . $contentObjectID . '_' . $this->ID );
            }

            if ( $http->hasSessionVariable( 'RedirectIfDiscarded_Backup_' . $contentObjectID . '_' . $this->ID ) and
                 $http->sessionVariable( 'RedirectIfDiscarded_Backup_' . $contentObjectID . '_' . $this->ID ) !== null )
            {
                $value = $http->sessionVariable( 'RedirectIfDiscarded_Backup_' . $contentObjectID . '_' . $this->ID );
                $http->setSessionVariable( 'RedirectIfDiscarded', $value['content'] );
                $http->removeSessionVariable( 'RedirectIfDiscarded_Backup_' . $contentObjectID . '_' . $this->ID );
            }
        }
    }

    function handleAttributeHTTPAction( $http, $action, $objectAttribute, $parameters )
    {
        $returnValue = false;
        $postSurveyRONewAction = 'ezsurvey_related_object_' . $this->ID . '_add';
        $postSurveyROEditAction = 'ezsurvey_related_object_' . $this->ID . '_edit';
        $postSurveyROAddNewAction = 'ezsurvey_related_object_' . $this->ID . '_add_new';
        if ( $postSurveyRONewAction == $action or
             $postSurveyROEditAction == $action or
             $postSurveyROAddNewAction == $action )
        {
            $returnValue = $this->createNewRelatedObject( $http, $action, $objectAttribute, $parameters );
        }

        $postSurveyROAddExistingAction = 'ezsurvey_related_object_' . $this->ID . '_add_existing';
        if ( $postSurveyROAddExistingAction == $action  )
        {
            $returnValue = $this->relateExistingObject( $http, $action, $objectAttribute, $parameters );
        }

        $postSurveyRORemoveAction = 'ezsurvey_related_object_' . $this->ID . '_remove';
        if ( $postSurveyRORemoveAction == $action )
        {
            $returnValue = $this->removeRelatedObject( $http, $action, $objectAttribute, $parameters );
        }

        $postSurveyROAddRelatedAction = 'ezsurvey_related_object_' . $this->ID . '_relate_existing_node';
        if ( $postSurveyROAddRelatedAction == $action )
        {
            $returnValue = $this->updateExistingObject( $http, $action, $objectAttribute, $parameters );
        }

        return $returnValue;
    }

    function updateExistingObject( $http, $action, $objectAttribute, $parameters )
    {
        $returnValue = false;
        $postVariable = 'SelectedObjectIDArray';
        if ( $http->hasPostVariable( $postVariable ) )
        {
            $value = $http->postVariable( $postVariable );

            if ( is_array( $value ) )
            {
                $this->Num = $value[0];
                $this->store();
                $returnValue = true;
            }
        }
        return $returnValue;
    }

    function removeRelatedObject( $http, $action, $objectAttribute, $parameters )
    {
        $this->Num = 0;
        $this->store();
        return true;
    }


    /*!
      Relate a existing contentobject. Redirect to browse modus.
    */
    function relateExistingObject( $http, $action, $objectAttribute, $parameters )
    {
        $assignedNodesIDs = array();
        $module = $GLOBALS['eZRequestedModule'];
        $objectID = $objectAttribute->attribute( 'contentobject_id' );
        $contentObjectAttributeID = $objectAttribute->attribute( 'id' );
        $contentClassAttributeID = $objectAttribute->attribute( 'contentclassattribute_id' );
        $version = $objectAttribute->attribute( 'version' );
        $languageCode = $objectAttribute->attribute( 'language_code' );
        $postQuestionID = eZSurveyType::PREFIX_ATTRIBUTE . '_ezsurvey_id_' . $contentObjectAttributeID;
        $contentClassAttribute = eZContentClassAttribute::fetch( $contentClassAttributeID );
        $classID = $contentClassAttribute->attribute( 'contentclass_id' );
        $contentClass = eZContentClass::fetch( $classID );
        $classIdentifier = $contentClass->attribute( 'identifier' );

        $customActionButton = "CustomActionButton[" . $contentObjectAttributeID . "_ezsurvey_related_object_" .$this->ID . "_relate_existing_node]";

        eZContentBrowse::browse( array( 'action_name' => 'AddRelatedSurveyObject',
                                        'persistent_data' => array( $postQuestionID => $this->ID,
                                                                    'ContentObjectAttribute_id[]' => $contentObjectAttributeID,
                                                                    'ClassIdentifier' => $classIdentifier,
                                                                    'ContentLanguageCode' => $languageCode,
                                                                    $customActionButton => $this->ID,
                                                                    'HasObjectInput' => false ),
                                        'description_template' => 'design:content/browse_related.tpl',
                                        'content' => array(),
                                        'keys' => array(),
                                        'ignore_nodes_select' => $assignedNodesIDs,
                                        'from_page' => $module->redirectionURI( 'content', 'edit', array( $objectID, $version, $languageCode ) ) ),
                                 $module );

        return eZModule::HOOK_STATUS_CANCEL_RUN;
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

            $http->setSessionVariable( 'LastAccessesURI_Backup_' . $originalContentObjectID . '_' . $this->ID,
                                       array( 'content' => $http->sessionVariable( 'LastAccessesURI' ) ) );
            $http->setSessionVariable( 'RedirectURIAfterPublish_Backup_' . $originalContentObjectID . '_' . $this->ID,
                                       array( 'content' => $http->sessionVariable( 'RedirectURIAfterPublish' ) ) );
            $http->setSessionVariable( 'RedirectIfDiscarded_Backup_' . $originalContentObjectID . '_' . $this->ID,
                                       array( 'content' => $http->sessionVariable( 'RedirectIfDiscarded' ) ) );

            $http->setSessionVariable( 'LastAccessesURI', $redirectHref );
            $http->setSessionVariable( 'RedirectURIAfterPublish', $redirectHref );
            $http->setSessionVariable( 'RedirectIfDiscarded', $redirectHref );

            $this->Num = $contentObject->attribute( 'id' );
            $this->store();
            $parameters = array( $contentObject->attribute( 'id' ) );
            if ( $addVersion === true )
                $parameters[] = $contentObject->attribute( 'current_version' );

            $module = $GLOBALS['eZRequestedModule'];
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

eZSurveyQuestion::registerQuestionType( ezpI18n::tr( 'survey', 'Related object' ), 'RelatedObject' );

?>
