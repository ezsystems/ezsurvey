<div class="survey">

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

{def $survey_object=fetch('content', 'object', hash('object_id', $contentobject_id))}

<h1 class="context-title">{"Survey results for %results"|i18n('survey',,hash('%results', $survey_object.name|wash(xhtml)))}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">


<div class="survey-submenu tab-block float-break">
<ul class="tabs">
<li class="first"><a href={concat('/survey/result/', $contentobject_id, '/', $contentclassattribute_id, '/', $language_code)|ezurl}>{"Summary"|i18n('survey')}</a></li>
<li class="middle"><a href={concat('/survey/result_list/', $contentobject_id, '/', $contentclassattribute_id, '/', $language_code)|ezurl}>{"All evaluations"|i18n('survey')}</a></li>
</ul>
</div>
<div class="tab-content selected">
<div class="block">
<p>{def $user=fetch( 'content', 'object', hash( 'object_id', $survey_user_id ))}
   {def $result=fetch('survey','survey_result',hash('result_id',$result_id))}
{"Participiant:"|i18n('survey')} {$user.name}<br />
{"Evaluated:"|i18n('survey')} {$result.tstamp|l10n(datetime)}</p>
</div>

{section var=question loop=$survey_questions}
<div class="block">
{survey_question_result_gui view=item
                            question=$question
                            result_id=$result_id
                            metadata=$survey_metadata}
<br />
</div>
{/section}
</div>
{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block float-break">

{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri=concat('/survey/rview/', $contentobject_id, '/', $contentclassattribute_id, '/', $language_code)
         item_count=$count
         view_parameters=$view_parameters
         item_limit=$limit}
</div>
<div class="block float-break">
<form class="float" action={concat('/survey/result_edit/', $result_id)|ezurl()} style="float: left; margin-right: 0.3em;">
<input class="button" name="EditSruveyResultButton" type="submit" value="Edit" method="post" />
</form>
<form class="float" action={concat('/survey/export/', $contentobject_id, '/', $contentclassattribute_id, '/', $language_code, '/')|ezurl()} style="float: left;">
<input class="button" name="ExportCSVButton" type="submit" value="Export CSV" method="post" />
</form>

</div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>


</div>

</div>