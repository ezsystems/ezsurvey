<h2 class="attributetype">{"Feedback field entry"|i18n('survey')}</h2>

<div class="block">
<label>{"Text of question"|i18n('survey')}:</label>
<input class="box" type="text" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_text_{$attribute_id}" value="{$question.text|wash('xhtml')}" size="70"{if $question.num2|ne(0)} disabled="disabled"{/if} />
</div>

<div class="block">
<div class="element">
<input type="hidden" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_mandatory_hidden_{$attribute_id}" value="1" />
<label><input type="checkbox" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_mandatory_{$attribute_id}" value="1"{if $question.mandatory|eq(1)} checked="checked"{/if} />
{"Mandatory answer"|i18n('survey')}</label>
</div>
<div class="element">
<label><input type="checkbox" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_num_{$attribute_id}" value="1"{if $question.num|eq(1)} checked="checked"{/if} />
{"Send copy in bcc to admin (%1)"|i18n('survey',,hash('%1', ezini('MailSettings', 'AdminEmail')))}</label>
</div>
{def $feedback_email_questions=$question.feedback_email_questions}
<div class="element">
<label>
{"Mapping"|i18n('survey')}
<select name="{$prefix_attribute}_ezsurvey_question_{$question.id}_num2_{$attribute_id}">
   <option value="0"{if $question.num2|eq(0)} selected="selected"{/if}>{"None"|i18n('survey')}</option>
   {foreach $feedback_email_questions as $id => $feedback_question}
     <option value="{$id}"{if $question.num2|eq($id)} selected="selected"{/if}>{$feedback_question|wash(xhtml)}</option>
   {/foreach}
</select>
</label>
</div>
</div>

<div class="block">
<label>{"Text of subject"|i18n('survey')}:</label>
<input class="box" type="text" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_text3_{$attribute_id}" value="{$question.text3|wash('xhtml')}" size="70" />
</div>

<div class="block">
<label>{"Feedback message for the receiver"|i18n('survey')}:</label>
<textarea class="box" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_text2_{$attribute_id}" rows="10">{$question.text2|wash('xhtml')}</textarea>
</div>