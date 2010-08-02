<div class="survey">

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
{def $survey_object=fetch('content', 'object', hash('object_id', $contentobject_id))}
<h1 class="context-title">{"Survey result overview"|i18n('survey')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="survey-submenu float-break tab-block">
<ul class="tabs">
<li class="first selected current"><a href={concat('/survey/result/', $contentobject_id, '/', $contentclassattribute_id, '/', $language_code)|ezurl}>{"Summary"|i18n('survey')}</a></li>
<li class="middle"><a href={concat('/survey/result_list/', $contentobject_id, '/', $contentclassattribute_id, '/', $language_code)|ezurl}>All evaluations</a></li>
</ul>
</div>
<div class="tab-content selected">
<div class="survey-questions">
{section show=$count|gt(0)}
<div class="block">
<p>{"Survey"i18n('survey')} <a href={$survey_object.main_node.url_alias|ezurl()}>{$survey_object.name|wash(xhtml)}</a> {"has %count answers."|i18n('survey',,hash('%count', $count))}</p>
</div>

{section var=question loop=$survey_questions}
<div class="block">
{survey_question_result_gui view=overview
                            question=$question
                            metadata=$survey_metadata
                            contentobject_id=$contentobject_id
                            contentclassattribute_id=$contentclassattribute_id
                            language_code=$language_code}
</div>
{/section}
{section-else}
<div class="block">
<p>
{"No results yet."|i18n('survey')}
</p>
</div>
{/section}
</div>
</div>
{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block float-break">

<form class="float" action={concat('/survey/export/', $contentobject_id, '/', $contentclassattribute_id, '/', $language_code)|ezurl}>
<input class="button" type="submit" value="Export CSV" />
</form>

</div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</div>

</div>
