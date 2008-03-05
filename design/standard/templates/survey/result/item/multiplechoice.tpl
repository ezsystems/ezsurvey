<label>{$question.question_number}. {$question.text|wash('xhtml')}</label>

{let result=fetch('survey', 'multiple_choice_result_item', hash( 'question', $question,
                                                                 'metadata', $metadata,
                                                                 'result_id', $result_id ))}
{section var=ans loop=$result}{$ans['value']|wash('xhtml')} {if $ans['label']|wash('xhtml')|count_chars}({$ans['label']|wash('xhtml')}){/if}{delimiter}, {/delimiter}
{if $ans['extra_value']|count_chars}<div class="block">{$ans['extra_value']|wash('xhtml')}</div>{/if}
{/section}
{/let}
