<h2 class="attributetype">{"Related object entry"|i18n( 'survey' )}</h2>
{if $question.num|gt(0)}
{def $surveyobject=fetch('content','object', hash('object_id', $question.num))}
{content_view_gui content_object=$surveyobject view='survey'}
<input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_related_object_{$question.id}_add]" value="{'Edit related object'|i18n( 'survey' )}" />
{else}
<p>{"Enter the button to create a new related object to the survey."|i18n( 'survey' )}</p>
<input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_related_object_{$question.id}_add]" value="{'Add related content'|i18n( 'survey' )}" />
{/if}
