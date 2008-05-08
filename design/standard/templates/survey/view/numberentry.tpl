<label>{$question.question_number}.
{$question.text|wash('xhtml')} {section show=$question.mandatory}<strong class="required">*</strong>{/section}</label>

<div class="survey-choices">
{section show=$question_result}
  <input size="10" name="{$prefix_attribute}_ezsurvey_answer_{$question.id}_{$attribute_id}" type="text" value="{$question_result.text|number($question.num)|wash('xhtml')}" />
{section-else}
  <input size="10" name="{$prefix_attribute}_ezsurvey_answer_{$question.id}_{$attribute_id}" type="text" value="{$question.answer|number($question.num)|wash('xhtml')}" />
{/section}
</div>