{$question.question_number}. {$question.text}
    {section show=is_array($question.answer)}
    {section var=qans loop=$question.answer}{$qans} {section show=$question.options[sub($qans,1)].label|count_chars}({$question.options[sub($qans,1)].label}){/section}{delimiter}, {/delimiter}{/section}
    {section-else}
    {$question.answer} {section show=$question.options[sub($question.answer,1)].label|count_chars}({$question.options[sub($question.answer,1)].label}){/section}
    {/section}

