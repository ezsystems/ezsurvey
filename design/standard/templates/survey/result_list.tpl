<div class="survey">

<div class="context-block">

<form enctype="multipart/form-data" method="post" action={concat("/survey/result_list/", $contentobject_id, '/', $contentclassattribute_id, '/', $language_code)|ezurl}>

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

{let survey_object=fetch('content', 'object', hash( 'object_id', $contentobject_id))}
<h1 class="context-title">{"Survey result"|i18n('survey')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="survey-submenu float-break">
<ul>
<li><a href={concat('/survey/result/', $contentobject_id, '/', $contentclassattribute_id, '/', $language_code)|ezurl}>Summary</a></li>
<li><span class="current">All evaluations</span></li>
</ul>
</div>

<div class="block"><p>{"Survey"i18n('survey')} <a href={$survey_object.main_node.url_alias|ezurl()}>{$survey_object.name|wash(xhtml)}</a> {"has %count answers."|i18n('survey',,hash('%count', $survey.result_count))}</p></div>
{/let}

<table class="list" cellspacing="0">
<tr>
  <th class="tight">&nbsp;</th>
  <th>{"Participant"|i18n('survey')}</th>
  <th>{"Evaluated"|i18n('survey')}</th>
  <th class="tight">&nbsp;</th>
  <th class="tight">&nbsp;</th>
</tr>
{def $showRemoveButton=false()}
{foreach $result_list as $index => $result_item sequence array('bglight','bgdark') as $style}
{let survey=fetch('survey', 'survey', hash('id', $result_item.survey_id))
     can_edit_results=$survey.can_edit_results}
<tr class="{$style}">
  <td>
   {if $can_edit_results}
        <input type="checkbox" name="DeleteIDArray[]" value="{$result_item.id}">
        {set $showRemoveButton=true()}
   {else}
      &nbsp;
    {/if}
  </td>
  {let user=fetch( content, object, hash( object_id, $result_item.user_id ) )}
  <td><a href={$user.main_node.url_alias|ezurl}>{$user.name|wash}</a></td>
  {/let}
  <td class="survey-date">{$result_item.tstamp|l10n(datetime)}</td>
  <td>

    {section show=$can_edit_results}
      <a href={concat( "/survey/result_edit/", $result_item.id )|ezurl}><img src={"edit.png"|ezimage} border="0" title="{'Edit'|i18n('survey')}" /></a>
    {section-else}
      &nbsp;
    {/section}
  </td>
  <td><a href={concat( "/survey/rview/", , $contentobject_id, '/', $contentclassattribute_id, '/', $language_code, '/', $result_item.id, '/(offset)/', sum($view_parameters.offset, $index) )|ezurl}><img src={"results.gif"|ezimage} border="0" title="{"Results"|i18n('survey')}" /></a></td>
</tr>
{/let}
{/foreach}
</table>

{include name=navigator
         uri='design:navigator/google.tpl'
	 page_uri=concat('/survey/result_list/', $contentobject_id, '/', $contentclassattribute_id, '/', $language_code )
	 item_count=$count
	 view_parameters=$view_parameters
	 item_limit=$limit}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">

{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

<div class="block">

{if $showRemoveButton|eq(true())}<input class="button" type="submit" name="RemoveButton" value="Remove" />{/if}

</div>

<div class="block float-break">

<form class="float" action={concat('/survey/export/', $contentobject_id, '/', $contentclassattribute_id, '/', $language_code)|ezurl}>
<input class="button" type="submit" name="ExportCSVButton" value="Export CSV" />
</form>

</div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>

</div>

</form>

</div>

</div>
