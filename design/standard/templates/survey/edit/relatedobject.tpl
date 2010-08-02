<h2 class="attributetype">{"Related object entry"|i18n( 'survey' )}</h2>
{if $question.num|gt(0)}
{def $surveyobject=fetch('content','object', hash('object_id', $question.num))}
{content_view_gui content_object=$surveyobject view='survey'}
<div class="block">
    <input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_related_object_{$question.id}_remove]" value="{'Remove'}" />
    <input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_related_object_{$question.id}_edit]" value="{'Edit'|i18n( 'survey' )}" />&nbsp;
    <input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_related_object_{$question.id}_add_existing]" value="{'Add existing'}" />
    <input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_related_object_{$question.id}_add_new]" value="{'Add new'|i18n( 'survey' )}" disabled="disabled" />
</div>
    {else}
<p>{"Enter the button 'Add existing' or 'Add new' to create a new related object to the survey."|i18n( 'survey' )}</p>
<div class="block">
    <input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_related_object_{$question.id}_remove]" value="{'Remove'}" disabled="disabled" />
    <input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_related_object_{$question.id}_edit]" value="{'Edit'|i18n( 'survey' )}" disabled="disabled" />&nbsp;
    <input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_related_object_{$question.id}_add_existing]" value="{'Add existing'}" />
    <input class="button" type="submit" name="CustomActionButton[{$attribute_id}_ezsurvey_related_object_{$question.id}_add_new]" value="{'Add new'|i18n( 'survey' )}" />
</div>
{/if}
