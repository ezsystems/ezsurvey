{set-block scope=root variable=subject}{"Filled Survey"|i18n('survey')}: {$survey_node.name}{/set-block}
{$intro}

{section var=question loop=$survey_questions}


{survey_question_result_gui view=mail question=$question}
{/section}
