<h2 class="attributetype">{"Email entry"|i18n('survey')}</h2>

<div class="block">
<label>{"Text of question"|i18n('survey')}:</label>
<input class="box" type="text" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_text_{$attribute_id}" value="{$question.text|wash('xhtml')}" size="70" />
</div>

<div class="block">
<input type="hidden" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_mandatory_hidden_{$attribute_id}" value="1" />
<label><input type="checkbox" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_mandatory_{$attribute_id}" value="1" {section show=$question.mandatory}checked{/section} />
{"Mandatory answer"|i18n('survey')}</label>
</div>

<div class="block">
<label>{"Default settings"|i18n('survey')}:</label><div class="labelbreak"></div>
<select name="{$prefix_attribute}_ezsurvey_question_{$question.id}_text3_{$attribute_id}">
<option value="" {section show=$question.text3|eq('')} selected="selected"{/section}>{"Default answer"|i18n('survey')}</option>
<option value="user_email" {section show=$question.text3|eq('user_email')} selected="selected"{/section}>{"User email"|i18n('survey')}</option>
</select>
</div>

<div class="block">
<label>{"Default answer"|i18n('survey')}:</label>
<input class="box" type="text" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_default_{$attribute_id}" value="{$question.default_value|wash('xhtml')}" size="20" />
</div>
