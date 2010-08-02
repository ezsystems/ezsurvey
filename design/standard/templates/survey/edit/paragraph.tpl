<h2 class="attributetype">{"Paragraph"|i18n( 'survey' )}</h2>

<div class="block">
  <label>{"Text of paragraph"|i18n( 'survey' )}:</label>
  <textarea class="box" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_text_{$attribute_id}" cols="70" rows="5" >{$question.text|wash('xhtml')}</textarea>
</div>