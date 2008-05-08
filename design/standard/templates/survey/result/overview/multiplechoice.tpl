<label>{$question.question_number}. {$question.text|wash('xhtml')}</label>

<table class="list" cellspacing="0">
<tr>
  <th>{"Label"|i18n('survey')}</th>
  <th>{"Value"|i18n('survey')}</th>
  <th>{"Count"|i18n('survey')}</th>
  <th colspan="2">{"Percentage"|i18n('survey')}</th>
</tr>
{let results=fetch('survey','multiple_choice_result',hash( 'question', $question,
                                                           'contentobject_id', $contentobject_id,
                                                           'contentclassattribute_id', $contentclassattribute_id,
                                                           'language_code', $language_code,
                                                           'metadata', $metadata ))}
{section var=result loop=$results sequence=array('bglight','bgdark')}
<tr class="{$result.sequence}">
  <td>{$result.label|wash('xhtml')}</td>
  <td>{$result.value|wash('xhtml')}</td>
  <td>{$result.count}</td>
  <td class="tight"><table class="diagram" width="101" bgcolor="#e0e0e0" border="0" cellspacing="0" cellpadding="0"><tr><td width="{switch match=$result.percentage}{case match=0}1{/case}{case}{$result.percentage}{/case}{/switch}%" {section show=$result.percentage|gt(0)}bgcolor="#2070a0"{/section}><img alt="" width="1" height="12" src={"1x1.gif"|ezimage} /></td><td><img alt="" src={"1x1.gif"|ezimage} /></td></tr></table></td>
  <td class="tight">{$result.percentage}%</td>
  </tr>
{/section}
{/let}
</table>
