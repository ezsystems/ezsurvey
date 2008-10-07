{set-block scope=root variable=subject}{"Filled Survey"|i18n('survey')}: {$survey_node.name}{/set-block}
{"The following information was collected as the result of the survey:"|i18n('survey')}

{section var=question loop=$survey_questions}
{survey_question_result_gui view=mail question=$question}
{/section}
