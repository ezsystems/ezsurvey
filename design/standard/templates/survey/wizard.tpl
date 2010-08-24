<form action={"survey/wizard"|ezurl()} method="post">
{def $action_name='Import'
     $action_name_disabled=false()
     $action_value='Import'
     $action_manual_name=false()
     $action_manual_value=false()}
<div class="survey">

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{"Survey wizard"|i18n( 'survey' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

{switch match=$state}
{case match='database'}
<div class="block">
<h2>{"Import database tables"|i18n( 'survey' )}</h2>
<p>{"This installs the tables eZ Survey needs to store data."|i18n( 'survey' )}</p>
{set $action_name='ImportDatabase'
     $action_value='Import'}
</div>
{/case}
{case in=array('survey_class', 'survey_classattribute')}
<div class="block">
<h2>{"Import survey content classes"|i18n( 'survey' )}</h2>

<ul>
<li>{"Survey class (needs at least one attribute with a survey datatype)."|i18n( 'survey' )}{if $state|eq('survey_classattribute')}<b> {"(exist)"|i18n( 'survey' )}</b>{/if}</li>
<li>{"Survey attribute class (needs one text attribute and xml text attribute, used for formatted text inside the surveys)."|i18n( 'survey' )}{if $content_class_list|count|gt(0)} {"(exist)"|i18n( 'survey' )}{/if}</li>
</ul>

<h3>{"Automatic installation"|i18n( 'survey' )}</h3>
<p>{"The installer creates the classes, from the package included in eZ Survey. This is recommended for most users."|i18n( 'survey' )}
<h3>{"Manual installation"|i18n( 'survey' )}</h3>
<p>{"Experienced users may create the classes manually. When done continue this installer by clicking the Survey link in the top menu."|i18n( 'survey' )}</p>
{if and($state|eq('survey_classattribute'), $content_class_list|count|gt(0))}{set $action_name_disabled=true()}{/if}
    {set $action_name='ImportSurveyPackage'
         $action_value='Automatic'
         $action_manual_name='ImportSurveyManual'
         $action_manual_value='Manual'}
</div>
{/case}
{case match='conf_survey_classattribute'}
<div class="block">
<h2>{"Configure the survey class attribute."|i18n( 'survey' )}</h2>
<p>{'The survey attribute "Related object" need to be configured before it can be used. The related object should be an object class containing atleast one xml text attribute and a normal text attribute.'|i18n( 'survey' )}</p>
<p>{'The survey attribute is already added previously as the content class "Survey Attribute".'|i18n( 'survey' )}</p>

<div class="block">
<h2>{"Configure the survey related object"|i18n( 'survey' )}</h2>
<p>{'The survey attribute "Related object" need to be configured, to allow xml formatted texts to be added to a survey. The related object should be an object class containing at least one normal text attribute and one xml text attribute, as installed in the previous step.'|i18n('survey')}</p>
{if $content_class_list|count|gt(0)}
<label>{"Select the content class from the list of valid classes:"|i18n('survey')}</label>
<select name="SurveyRelatedClassID">
{foreach $content_class_list as $class}
<option value="{$class.id}"{if $class.id|eq($config.contentclass_id)} selected="selected"{/if}>{$class.name|wash(xhtml)}</option>
{/foreach}
</select>
{/if}
{if and($survey_attribute_found|eq(true()), $content_class_list|count|gt(1))}<p>{"<b>Note</b>: The default survey attribute was found and set as default."|i18n( 'survey', '', hash('<b>', '<b>', '</b>', '</b>') )}<p>{/if}
</div>
</div>
{set $action_name='ConfigureSurveyClassAttribute'
     $action_value='Next'}
{/case}

{case match='conf_survey_classattribute_parent'}
<div class="block">
{if $browse_attribute|eq(true())}<div class="message-warning"><h2>{"You need to select a parent for survey attribute."|i18n( 'survey' )}</h2>
{"You may select the parent folder by enter the Browse button"|i18n( 'survey' )}
</div>{/if}
{if $config.node_id|eq(0)}
<h3>{"New parent folder for the survey attributes"|i18n('survey')}</h3>
<p>{"The survey attributes must be stored in a folder somewhere in the content structure. If you want a new folder for this purpose it must be created first, and you may continue this installer by clicking the Survey link in the top menu."|i18n('survey')}</p>
<input class="button" type="submit" value="{"Create new"|i18n('survey')}" name="AddNewSurveyRelatedNode" title="{"Add new parent node for the related survey attributes."|i18n('survey')}" />
{/if}
<h3>{"Select the parent folder for the survey attributes"|i18n('survey')}</h3>
{if $config.node_id|gt(0)}
{def $node=fetch('content', 'node', hash('node_id', $config.node_id))}
{def $section=fetch( 'section', 'object', hash( 'section_id', $node.object.section_id ))}
<table class="list" cellspacing="0">
<tr>
    <th>{"Name"|i18n('survey')}</th>
    <th>{"Type"|i18n('survey')}</th>
    <th>{"Section"|i18n('survey')}</th>
</tr>
<tr>
    <td><a href={$node.url_alias|ezurl()}>{$node.name|wash(xhtml)}</a></td>
    <td><a href={concat('class/view/', $node.object.contentclass_id)|ezurl()}>{$node.object.class_name|wash(xhtml)}</td>
    <td><a href={concat('section/view/', $node.object.section_id)|ezurl()}>{$section.name|wash(xhtml)}</td>
</tr>
</table>
<p>{"Remember that the parent folder need to be readable by the user that should see the survey. You may configure this in the role setup of eZ Publish."|i18n('survey')}</p>
{else}
<p>{"If you already have a folder for the survey attributes you may browse directly for it. You also need to browse if you just created a new folder."|i18n('survey')}</p>
{/if}
<div class="block">
<input class="button" type="submit" value="{"Browse"|i18n('survey')}" name="BrowseSurveyRelatedNode" title="{"Browse for the parent node for the related survey attributes."|i18n('survey')}" />
</div>
</div>
</div>
{set $action_name='ConfigureSurveyAttribute'
     $action_value='Next'}
{/case}
{case}
<div class="block">
<h2>{"Installation completed"|i18n('survey')}</h2>
<p>{"eZ Survey is now installed. You may now create new surveys in the content structure. The results will be available in the <a>survey list</a> when you get the first answer for a survey. You may also change the configuration in the <aconfig>related object configuration</a> page at any time."|i18n('survey', '', hash('<a>', concat('<a href=', 'survey/list'|ezurl(), '>'), '<aconfig>', concat('<a href=', 'survey/relatedobjectconfig'|ezurl(), '>'), '</a>', '</a>'))}</p>
</div>
{set $action_name=false()
     $action_value=false()}
{/case}
{/switch}

{* DESIGN: Content END *}</div>

<div class="controlbar">
<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block">
{if $action_name|ne(false())}
<input class="button" type="submit" name="{$action_name}" value="{$action_value}"{if $action_name_disabled|eq(true())} disabled="disabled"{/if} />
{/if}
{if $action_manual_name|ne(false())}
&nbsp;<input class="button" type="submit" name="{$action_manual_name}" value="{$action_manual_value}" />
{/if}
</div>
</div></div></div></div></div></div>
</div>
</div></div></div></div>
</div>
</div>
</div>
</form>
