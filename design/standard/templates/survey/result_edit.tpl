<div class="survey">

<form enctype="multipart/form-data" method="post" action={concat("survey/result_edit/", $survey_result.id)|ezurl}>

<input type="hidden" name="{$prefix_attribute}_ezsurvey_id_{$contentobjectattribute_id}" value="{$survey.id}" />

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
{def $survey_object=fetch('content', 'object', hash('object_id', $contentobject_id))}
<h1 class="context-title">{"Edit survey results for: %result"|i18n('survey',,hash('%result', $survey_object.name|wash(xhtml)))}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>
{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{include uri="design:survey/view_validation.tpl"}

{let question_results=$survey_result.question_results}

  {section var=question loop=$survey.questions}
    <div class="block">
    <input type="hidden" name="{$prefix_attribute}_ezsurvey_question_list_{$contentobjectattribute_id}[]" value="{$question.id}" />
    {section show=is_set( $question_results[$question.id] )}
      {survey_question_view_gui question=$question question_result=$question_results[$question.id] attribute_id=$contentobjectattribute_id prefix_attribute=$prefix_attribute}
    {section-else}
      {survey_question_view_gui question=$question attribute_id=$contentobjectattribute_id prefix_attribute=$prefix_attribute}
    {/section}
    <div class="break"></div>
    </div>
  {/section}

{/let}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block">

<input class="button" type="submit" name="SurveyStoreButton" value="{'Submit'|i18n( 'survey' )}" />
<input class="button" type="submit" name="SurveyCancelButton" value="{'Cancel'|i18n( 'survey' )}" />

</div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</div>

</form>

</div>