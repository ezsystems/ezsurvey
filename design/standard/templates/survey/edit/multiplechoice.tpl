<h2 class="attributetype">{"Single/Multiple choice"|i18n( 'survey' )}</h2>

<div class="block">
<label>{"Text of question"|i18n( 'survey' )}:</label>
<input class="box" type="text" name="{$prefix_attribute}_ezsurvey_question_{$question.id}_text_{$attribute_id}" value="{$question.text|wash('xhtml')}" size="70"  />
</div>


<div class="block">
<label>{"Rendering style"|i18n( 'survey' )}:</label>
<select name="{$prefix_attribute}_ezsurvey_question_{$question.id}_num_{$attribute_id}">
  <option value="1"{if $question.num|eq(1)} selected="selected"{/if}>{"Radio buttons in a row"|i18n( 'survey' )}</option>
  <option value="2"{if $question.num|eq(2)} selected="selected"{/if}>{"Radio buttons in a column"|i18n( 'survey' )}</option>
  <option value="3"{if $question.num|eq(3)} selected="selected"{/if}>{"Checkboxes in a row"|i18n( 'survey' )}</option>
  <option value="4"{if $question.num|eq(4)} selected="selected"{/if}>{"Checkboxes in a column"|i18n( 'survey' )}</option>
  <option value="5"{if $question.num|eq(5)} selected="selected"{/if}>{"Selector"|i18n( 'survey' )}</option>
</select>
</div>

<table class="list" cellspacing="0">
<tr>
  <th class="tight">&nbsp;</th>
  <th colspan="4">{"Option label"|i18n( 'survey' )}</th>
  <th class="tight">{"Value"|i18n( 'survey' )}</th>
  <th class="tight">{"Checked"|i18n( 'survey' )}</th>
  <th class="tight">{"Order"|i18n( 'survey' )}</th>
</tr>
{def $is_selected=false()}
{foreach $question.options as $option sequence array(bglight,bgdark) as $class} {* used namespace instead of var because of the bug with the 'sequence' *}
<tr class="{$class}">
  <td><input name="{$prefix_attribute}_ezsurvey_mc_{$question.id}_{$option.id}_selected_{$attribute_id}" type="checkbox" ></td>
  <td colspan="4"><input class="box" name="{$prefix_attribute}_ezsurvey_mc_{$question.id}_{$option.id}_label_{$attribute_id}" type="text" value="{$option.label|wash('xhtml')}" size="30" /></td>
  <td><input name="{$prefix_attribute}_ezsurvey_mc_{$question.id}_{$option.id}_value_{$attribute_id}" type="text" value="{$option.value|wash('xhtml')}" size="5"  /></td>
  <td>{if or($question.num|eq(1), $question.num|eq(2), $question.num|eq(5))}
  <input name="{$prefix_attribute}_ezsurvey_mc_{$question.id}_checked_{$attribute_id}" type="radio"{if $option.checked|eq(1)} checked="checked"{set $is_selected=true()}{/if} value="{$option.value}" />{else}<input name="{$prefix_attribute}_ezsurvey_mc_{$question.id}_{$option.id}_checked_{$attribute_id}" type="checkbox" {if $option.checked|eq(1)}checked="checked"{set $is_selected=true()}{/if} />{/if}</td>
  <td><input name="{$prefix_attribute}_ezsurvey_mc_{$question.id}_{$option.id}_tab_order_{$attribute_id}" type="text" size="2" value="{$option.id|wash('xhtml')}" /></td>
</tr>
{/foreach}
{if $question.extra_info.enabled|eq(1)}
<tr class="bglight">
  <td rowspan="3"><input name="{$prefix_attribute}_ezsurvey_mc_{$question.id}_extra_selected_{$attribute_id}" type="checkbox" ></td>
  <td colspan="4"><input class="box" name="{$prefix_attribute}_ezsurvey_mc_{$question.id}_extra_label_{$attribute_id}" type="text" value="{$question.extra_info.label|wash('xhtml')}" size="30" /></td>
  <td rowspan="3"><input name="{$prefix_attribute}_ezsurvey_mc_{$question.id}_extra_value_{$attribute_id}" type="text" value="{$question.extra_info.value|wash('xhtml')}" size="5"  /></td>
  <td rowspan="3">{if or($question.num|eq(1), $question.num|eq(2), $question.num|eq(5))}
  <input name="{$prefix_attribute}_ezsurvey_mc_{$question.id}_checked_{$attribute_id}" type="radio"{if $question.extra_info.value_checked|eq(1)} checked="checked"{set $is_selected=true()}{/if} value="{$question.extra_info.value}" />{else}<input name="{$prefix_attribute}_ezsurvey_mc_{$question.id}_extra_value_checked_{$attribute_id}" type="checkbox"{if $question.extra_info.value_checked|eq(1)} checked="checked"{set $is_selected=true()}{/if} />{/if}</td>
  <td rowspan="3"><input name="dummy" type="text" size="2" value="" disabled="disabled" /></td>
</tr>
<tr class="bglight">
  <th>Default value</th>
  <th class="tight">CSS</th>
  <th class="tight">Width</th>
  <th class="tight">Height</th>
</tr>
<tr class="bgdark">
  <td><input class="box" name="{$prefix_attribute}_ezsurvey_mc_{$question.id}_extra_default_value_{$attribute_id}" type="text" value="{$question.extra_info.default_value|wash('xhtml')}" size="30" /></td>
  <td><input id="{$prefix_attribute}_ezsurvey_mc_{$question.id}_extra_enable_css_style_{$attribute_id}" name="{$prefix_attribute}_ezsurvey_mc_{$question.id}_extra_enable_css_style_{$attribute_id}" type="checkbox"{if $question.extra_info.enable_css_style|eq(1)} checked="checked"{/if} onchange="synchFormElements( '{$prefix_attribute}_ezsurvey_mc_{$question.id}_extra_enable_css_style_{$attribute_id}', '{$prefix_attribute}_ezsurvey_mc_{$question.id}_extra_column_{$attribute_id}', false );" /></td>
<td><input id="{$prefix_attribute}_ezsurvey_mc_{$question.id}_extra_column_{$attribute_id}" class="box" name="{$prefix_attribute}_ezsurvey_mc_{$question.id}_extra_column_{$attribute_id}" type="text" value="{$question.extra_info.column|wash('xhtml')}" size="5" /></td>
  <td><input class="box" name="{$prefix_attribute}_ezsurvey_mc_{$question.id}_extra_row_{$attribute_id}" type="text" value="{$question.extra_info.row|wash('xhtml')}" size="5" /></td>
</tr>
{/if}
</table>

<div class="block">
<input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_mc_{$question.id}_remove_selected]" value="{'Remove selected'|i18n( 'survey' )}" />
<input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_mc_{$question.id}_new_option]" value="{'New option'|i18n( 'survey' )}" />
&nbsp;
<input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_mc_{$question.id}_enable_extra_info]" value="{'Add extra option'|i18n( 'survey' )}"{if $question.extra_info.enabled|eq(1)} disabled="disabled"{/if}/>
<input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_mc_{$question.id}_uncheck_options]" value="{'Uncheck options'|i18n( 'survey' )}"{if $is_selected|eq(false())} disabled="disabled"{/if}/>
</div>
</div class="element object_left" >
</div>

<script type="text/javascript">
synchFormElements( '{$prefix_attribute}_ezsurvey_mc_{$question.id}_extra_enable_css_style_{$attribute_id}', '{$prefix_attribute}_ezsurvey_mc_{$question.id}_extra_column_{$attribute_id}', false );
</script>