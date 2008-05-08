<label>{$question.question_number}. {$question.text|wash('xhtml')}</label>

{let result=fetch('survey', 'text_entry_result_item', hash( 'question', $question, 'metadata', $metadata, 'result_id', $result_id ))}
{$result|number($question.num)}
{/let}
