{default te_limit=5}
<label>{$question.question_number}. {$question.text|wash('xhtml')}</label>

<dl>
  <dt>{"Last answers"|i18n( 'survey' )}:</dt>
  <dd>
  <ul>
  {def $results=fetch( 'survey', 'text_entry_result', hash( 'question', $question,
                                                            'contentobject_id', $contentobject_id,
                                                            'contentclassattribute_id', $contentclassattribute_id,
                                                            'language_code', $language_code,
                                                            'metadata', $metadata,
                                                            'limit', $te_limit ))}
  {foreach $results as $result}
    <li>{$result.value|wash('xhtml')}</li>
  {/foreach}
  </ul>
  </dd>
</dl>
{/default}
