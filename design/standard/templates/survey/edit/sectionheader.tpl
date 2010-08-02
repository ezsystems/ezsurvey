<h2 class="attributetype">{"Section header"|i18n( 'survey' )}</h2>

<div class="block">
<label>{"Text of header"|i18n( 'survey' )}:</label><div class="labelbreak"></div>
<input class="box" type="text" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_text_{$attribute_id}" value="{$question.text|wash('xhtml')}" size="30" />
</div>
