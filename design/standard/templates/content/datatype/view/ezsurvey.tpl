<div class="survey-view">
{def $survey=$attribute.content.survey
     $survey_questions=$survey.questions}
 {* The survey questions need to be fetched before validation can be runned, beacause of static variables are updated *}
{if is_set($attribute.content.survey_validation.one_answer_need_login)}
<p>{"You need to log in in order to answer this survey"|i18n('survey')}.</p>
{include uri='design:user/login.tpl'}
{else}
{if $survey.valid|eq(false())}
<p>{"The survey is not active"|i18n('survey')}.</p>
{else}
{def $survey_validation=first_set($survey.global_survey_content.survey_validation, $attribute.content.survey_validation)}
{if or(is_set( $survey_validation.one_answer ), and(is_set($survey_validation.one_answer_count), $survey_validation.one_answer_count|gt(0)))}
<p>{"The survey does already have an answer from you"|i18n('survey')}.</p>
{else}
{def $prefixAttribute='ContentObjectAttribute'}
{def $node=fetch( 'content', 'node', hash( 'node_id', module_params().parameters.NodeID ))}
{def $module_param_value=concat(module_params().module_name,'/', module_params().function_name)}
{if $module_param_value|ne('content/edit')}
{literal}
<script language="Javascript">
var isSurveySubmitted = false;
<!--
    function DisableSurvey{/literal}{$attribute.id}{literal}( form ) {
    document.body.style.cursor = 'progress';
    for (var i = 0; i < form.length; i++) {
             form.elements[i].setAttribute( 'readonly', 'readonly' );

       }
       var retVal = true;
       if ( isSurveySubmitted == true )
           retVal = false;

       isSurveySubmitted = true;

       return retVal;
    }
//-->
</script>
{/literal}
<form enctype="multipart/form-data" method="post" action={$node.url_alias|ezurl()} onsubmit="DisableSurvey{$attribute.id}(this);">
{/if}
<input type="hidden" name="{$prefixAttribute}_ezsurvey_contentobjectattribute_id_{$attribute.id}" value="{$attribute.id}" />
<input type="hidden" name="{$prefixAttribute}_ezsurvey_node_id_{$attribute.id}" value="{module_params().parameters.NodeID}" />

<input type="hidden" name="{$prefixAttribute}_ezsurvey_id_{$attribute.id}" value="{$survey.id}" />
<input type="hidden" name="{$prefixAttribute}_ezsurvey_id_view_mode_{$attribute.id}" value="{$survey.id}" />

{"Questions marked with %mark% are required."|i18n('survey', '', hash( '%mark%', '<strong class="required">*</strong>' ) )}

{section show=$preview|not}
{include uri="design:survey/view_validation.tpl"}
{/section}

{let question_results=$survey.question_results}
{section show=$question_results}
  {section var=question loop=$survey_questions}
    {section show=$question.visible}
      <div class="block">
      <input type="hidden" name="{$prefix}_ezsurvey_question_list_{$attribute.id}[]" value="{$question.id}" />
      <a name="survey_question_{$question.question_number}"></a>
      {if is_set($question_results[$question.id])}
        {survey_question_view_gui question=$question question_result=$question_results[$question.id] attribute_id=$attribute.id prefix_attribute=$prefixAttribute survey_validation=$survey_validation}
      {else}
        {survey_question_view_gui question=$question question_result=0 attribute_id=$attribute.id prefix_attribute=$prefixAttribute}
      {/if}
      <div class="break"></div>
      </div>
    {/section}
  {/section}
{section-else}
  {section var=question loop=$survey_questions}
    {section show=$question.visible}
      <div class="block">
      <input type="hidden" name="{$prefixAttribute}_ezsurvey_question_list_{$attribute.id}[]" value="{$question.id}" />
      <a name="survey_question_{$question.question_number}"></a>
      {survey_question_view_gui question=$question question_result=0 attribute_id=$attribute.id prefix_attribute=$prefixAttribute}
      <div class="break"></div>
      </div>
    {/section}
  {/section}
{/section}
{/let}

<div class="block">
<input class="button" type="submit" name="{$prefixAttribute}_ezsurvey_store_button_{$attribute.id}" value="{'Submit'|i18n( 'survey' )}" />
</div>

{if $module_param_value|ne('content/edit')}
</form>
{/if}
{/if}
{/if}
{/if}
</div>