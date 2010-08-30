{$question.question_number}. {$question.text}
{if is_set($question.multiple_choice_answers.options)}{def $options=$question.multiple_choice_answers.options}{else}{def $options=$question.options}{/if}


{def $first=true()}
    {foreach $options as $option}{if $option.toggled|eq(1)}{if $first|eq(false())}, {/if}{$option.value} {if $option.label|count_chars} ({$option.label}){set $first=false()}{/if}{/if}{/foreach}{if $question.extra_info.value_checked|eq(1)}{if $first|eq(false())}, {/if}{$question.extra_info.value} {if $question.extra_info.label|count_chars} ({$question.extra_info.label})
{$question.extra_info.extra_answer}{/if}{/if}

