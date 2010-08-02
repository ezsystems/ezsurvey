<div class="survey">

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{"Survey list"|i18n('survey')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

<table class="list" cellspacing="0">
<tr>
    <th>{"Title"|i18n('survey')}</th>
    <th class="tight">{"Enabled"|i18n('survey')}</th>
    <th class="tight">{"Persistent"|i18n('survey')}</th>
    <th class="tight">{"Activity"|i18n('survey')}</th>
    <th class="tight">{"Answers"|i18n('survey')}</th>
    <th class="tight">&nbsp;</th>
</tr>
{section var=survey loop=$survey_list sequence=array('bglight','bgdark')}
    {let can_view=and( $survey.survey.enabled, $survey.survey.published, $survey.survey.valid )
         contentsurveyobject_id=0
         contentsurveyobject=0}
         {set contentsurveyobject_id=$survey.info.contentobject_id}
         {set contentsurveyobject=fetch('content','object',hash('object_id',$contentsurveyobject_id))}
<tr class="{$survey.sequence}">
    <td class="survey-title"><img src="{$survey.info.contentobjectattribute_language_code|flag_icon}" alt="{$survey.info.name|wash(xhtml)}" />&nbsp;<a href={concat($contentsurveyobject.main_node.url_alias, '/(language)/', $survey.info.contentobjectattribute_language_code)|ezurl()}>{$contentsurveyobject.main_node.url_alias|ezurl(no)}</a></td>
    <td>
    {switch match=$survey.survey.enabled}
      {case match=0}{"No"|i18n('survey')}{/case}
      {case match=1}{"Yes"|i18n('survey')}{/case}
    {/switch}
    </td>
    <td>
    {switch match=$survey.survey.persistent}
      {case match=0}{"No"|i18n('survey')}{/case}
      {case match=1}{"Yes"|i18n('survey')}{/case}
    {/switch}
    </td>
    <td style="white-space: nowrap;">
    {switch match=$survey.survey.activity_status}
      {case match=0}{"Not started"|i18n('survey')}{/case}
      {case match=1}{"Open"|i18n('survey')}{/case}
      {case match=2}{"Closed"|i18n('survey')}{/case}
    {/switch}
    </td>
    <td style="text-align: right;">
    {let answers=fetch('survey', 'result_count', hash( 'contentobject_id', $survey.survey.contentobject_id,
                                                       'contentclassattribute_id', $survey.survey.contentclassattribute_id,
                                                       'language_code', $survey.survey.language_code))}
						       {$answers}
    {/let}
    </td>
    <td>
  <a href={concat("/survey/result/", $survey.survey.contentobject_id, '/', $survey.survey.contentclassattribute_id, '/', $survey.survey.language_code)|ezurl()}><img src={"results.gif"|ezimage} border="0" title="{"Results"|i18n('survey')}" /></a>
  </td>
</tr>
{/let}
{/section}
</table>

{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/survey/list'
         item_count=$count
         view_parameters=$view_parameters
         item_limit=$limit}

{* DESIGN: Content END *}</div></div></div></div></div></div>

</div>

</div>