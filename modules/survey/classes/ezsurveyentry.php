<?php
/**
 * This file is part of the eZSurvey extension.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

class eZSurveyEntry extends eZSurveyQuestion
{
    function eZSurveyEntry( $row = false )
    {
        $this->eZSurveyQuestion( $row );
    }

    function processViewActions( &$validation, $params )
    {
        $http = eZHTTPTool::instance();
        $variableArray = array();

        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params['contentobjectattribute_id'];

        $postSurveyAnswer = $prefix . '_ezsurvey_answer_' . $this->ID . '_' . $attributeID;
        if ( $this->attribute( 'mandatory' ) == 1 and strlen( trim ( $http->postVariable( $postSurveyAnswer ) ) ) == 0 )
        {
            $validation['error'] = true;
            $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Please answer the question %number as well!', null,
                                              array( '%number' => $this->questionNumber() ) ),
                                                     'question_number' => $this->questionNumber(),
                                             'code' => 'general_answer_number_as_well',
                                             'question' => $this );
        }
        $this->setAnswer( trim ( $http->postVariable( $postSurveyAnswer ) ) );
        $variableArray['answer'] = trim ( $http->postVariable( $postSurveyAnswer ) );

        return $variableArray;
    }

    function result()
    {
        $surveyID = $this->attribute( 'survey_id' );
        $survey = eZSurvey::fetch( $surveyID );
        $contentObjectID = $survey->attribute( 'contentobject_id' );
        $contentClassAttributeID = $survey->attribute( 'contentclassattribute_id' );
        $languageCode = $survey->attribute( 'language_code' );

        $result = eZSurveyEntry::fetchResult( $this, $contentObjectID, $contentClassAttributeID, $languageCode, false, 5 );
        return $result['result'];
    }

    function fetchResult( $question, $contentObjectID, $contentClassAttributeID, $languageCode, $metadata = false, $limit = false, $sortBy = false )
    {
        $db = eZDB::instance();

        $resultArray = array();
        $questionOriginalID = $question->attribute( 'original_id' );

        $query = 'SELECT text as value FROM ezsurveyquestionresult';
        if ( $metadata != false )
        {
            for( $index = 1; $index <= count( $metadata ); $index++ )
            {
                $query .= ', ezsurveymetadata as m';
                $query .= $index;
            }
        }
        $query .= ' where questionoriginal_id=\'';
        $query .= $question->attribute( 'original_id' );
        $query .= '\' and length(text)>0';
        $index = 0;
        if ( $metadata != false )
        {
            foreach ( array_keys( $metadata ) as $key )
            {
                $index++;
                if ( $index == 1 )
                    $query .= ' and ezsurveyquestionresult.result_id=m1.result_id';
                else
                {
                    $query .= ' and m';
                    $query .= ( $index - 1 );
                    $query .= '.result_id=m';
                    $query .= $index;
                    $query .= '.result_id';
                }
                $query .= ' and m';
                $query .= $index;
                $query .= '.attr_name=\'';
                $query .= $key;
                $query .= '\' and m';
                $query .= $index;
                $query .= '.attr_value=\'';
                $query .= $metadata[$key];
                $query .= '\'';
            }
        }

        if ( is_array( $sortBy ) )
        {
            $sortField = 'text';
            if ( isSet( $sortBy[0] ) )
                $sortField = $sortBy[0];

            switch ( $sortField )
            {
                case 'text':
                {
                    $sortingFields = 'ezsurveyquestionresult.text';
                } break;

                default:
                {
                    eZDebug::writeWarning( 'Unknown sort field: ' . $sortField, 'eZSurveyEntry::fetchResult' );
                    continue;
                };
            }

            $sortOrder = true;
            if ( isSet( $sortBy[1] ) )
                $sortOrder = $sortBy[1];
            $sortingFields .= $sortOrder ? " ASC" : " DESC";

            $query .= " ORDER BY $sortingFields";
        }
        else
        {
            $query .= ' ORDER BY ezsurveyquestionresult.id DESC';
        }

        $queryParams = array();
        if ( $limit != false && $limit != 0 )
            $queryParams['limit'] = $limit;

        $rows = $db->arrayQuery( $query, $queryParams );
        $result = array( 'result' => $rows );
        return $result;
    }

    function &fetchResultItem( $question, $result_id, $metadata = false )
    {
        $result = eZPersistentObject::fetchObject( eZSurveyQuestionResult::definition(),
                                                   'text',
                                                   array( 'question_id' => $question->attribute( 'id' ),
                                                          'result_id' => $result_id ),
                                                   false );
        $returnArray = array( 'result' => $result['text'] );
        return $returnArray;
    }
}

?>
