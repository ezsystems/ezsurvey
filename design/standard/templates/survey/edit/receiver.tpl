<h2 class="attributetype">{"Receiver"|i18n( 'survey' )}</h2>

<div class="block">

<p>{"N. B. If you enter just one email address, user will not see this question. Instead the posting
will be directly sent to the address."|i18n( 'survey' )}</p>

<label>{"Text of question"|i18n( 'survey' )}:</label>
<input class="box" type="text" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_text_{$attribute_id}" value="{$question.text|wash('xhtml')}" size="70" />

<table class="list" cellspacing="0">
<tr>
  <th class="tight">&nbsp;</th>
  <th>{"Name"|i18n( 'survey' )}</th>
  <th>{"Email"|i18n( 'survey' )}</th>
  <th class="tight">{"Presel."|i18n( 'survey' )}</th>
  <th class="tight">{"Order"|i18n( 'survey' )}</th>
</tr>
{section name=option loop=$question.options sequence=array(bglight,bgdark)}
<tr class="{$:sequence}">
  <td><input name="{$prefix_attribute}_ezsurvey_receiver_{$question.id}_{$:item.id}_selected_{$attribute_id}" type="checkbox" /></td>
  <td><input class="box" name="{$prefix_attribute}_ezsurvey_receiver_{$question.id}_{$:item.id}_label_{$attribute_id}" type="text" value="{$:item.label|wash('xhtml')}" size="30" /></td>
  <td><input class="box" name="{$prefix_attribute}_ezsurvey_receiver_{$question.id}_{$:item.id}_value_{$attribute_id}" type="text" value="{$:item.value|wash('xhtml')}" size="20" /></td>
  <td><input name="{$prefix_attribute}_ezsurvey_receiver_{$question.id}_{$:item.id}_checked_{$attribute_id}" type="checkbox"{section show=$:item.checked|eq(1)} checked="checked"{/section} /></td>
  <td><input name="{$prefix_attribute}_ezsurvey_receiver_{$question.id}_{$:item.id}_tab_order_{$attribute_id}" type="text" size="2" value="{$:item.id|wash('xhtml')}" /></td>
</tr>
{/section}
</table>

{section show=$disabled|not}
<div class="block">
<input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_receiver_{$question.id}_remove_selected]" value="{'Remove selected'|i18n( 'survey' )}" />
<input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_receiver_{$question.id}_new_option]" value="{'New option'|i18n( 'survey' )}" />
</div>
{/section}

</div>
