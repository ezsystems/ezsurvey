<form action={"survey/relatedobjectconfig"|ezurl()} method="post">
<div class="survey">

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h1 class="context-title">{"Related object configuration"|i18n('survey')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="block">
<p>{"Set the parent node for the survey attributes, which are of the type Related Object"|i18n('survey')}</p>
<label>{"Content class."|i18n('survey')}</label>
<select name="SurveyRelatedClassID">
{def $content_class_list=fetch('class', 'list')}
{foreach $content_class_list as $class}
<option value="{$class.id}"{if $class.id|eq($config.contentclass_id)} selected="selected"{/if}>{$class.name|wash(xhtml)}</option>
{/foreach}
</select>
</div>

<div class="block">
<label>{"Set the parent folder for the survey attributes."|i18n('survey')}</label>
{if $config.node_id|gt(0)}
{def $node=fetch('content', 'node', hash('node_id', $config.node_id))}
{def $section=fetch( 'section', 'object', hash( 'section_id', $node.object.section_id ))}
<table class="list" cellspacing="0">
<tr>
    <th>Name</th>
    <th>Type</th>
    <th>Section</th>
</tr>
<tr>
    <td>{$node.name|wash(xhtml)}</td>
    <td>{$node.object.class_name|wash(xhtml)}</td>
    <td>{$section.name|wash(xhtml)}</td>
</tr>
</table>
{/if}
<input class="button" type="submit" value="{"Browse"|i18n('survey')}" name="BrowseSurveyRelatedNode" title="{"Browse for the parent node for the related survey attributes."|i18n('survey')}" />

</div>
{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block float-break">
<input class="button" type="submit" value="{"Update all"|i18n('survey')}" name="UpdateSurveyRelatedConfig" title="{"Update the configuration."|i18n('survey')}" />
</div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</div>

</div>
</form>
