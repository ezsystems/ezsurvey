<form enctype="multipart/form-data" method="post" action={concat("/survey/edit/",$survey.id)|ezurl}>

<input type="hidden" name="SurveyID" value="$survey.id" />

    <h1>{"Edit survey"|i18n( 'survey' )} '{$survey.title|wash('xhtml')}' ({"id"|i18n('survey')} {$survey.id})</h1>
    {include uri="design:survey/edit_validation.tpl"}


    <div class="block">
    <label>{'Survey title'|i18n( 'survey' )}:</label>
    <input class="box" type="text" name="SurveyTitle" value="{$survey.title|wash('xhtml')}" size="70" />
    </div>

    <div class="block">
    <label>{'Enabled'|i18n( 'survey' )}:</label>
    <input type="checkbox" name="SurveyEnabled" {section show=$survey.enabled|eq(1)}checked="checked"{/section} />
    </div>

    <div class="block">
    <label>{'Valid from'|i18n('survey')}:</label>
    <div class="block">
    <label><input type="checkbox" name="SurveyValidFromNoLimit" value="1" {section show=$survey.valid_from_array.no_limit}checked{/section} /> {'No limitation'|i18n( 'survey' )}</label>
    </div>
    <div class="block">
    <div class="element">
    <label>{'Year'|i18n( 'survey' )}:</label>
    <input name="SurveyValidFromYear" size="5" value="{$survey.valid_from_array.year}" />
    </div>
    <div class="element">
    <label>{'Month'|i18n( 'survey' )}:</label>
    <input name="SurveyValidFromMonth" size="3" value="{$survey.valid_from_array.month}" />
    </div>
    <div class="element">
    <label>{'Day'|i18n( 'survey' )}:</label>
    <input name="SurveyValidFromDay" size="3" value="{$survey.valid_from_array.day}" />
    </div>
    <div class="element">
    <label>{'Hour'|i18n( 'survey' )}:</label>
    <input name="SurveyValidFromHour" size="3" value="{$survey.valid_from_array.hour}" />
    </div>
    <div class="element">
    <label>{'Minute'|i18n( 'survey' )}:</label>
    <input name="SurveyValidFromMinute" size="3" value="{$survey.valid_from_array.minute}" />
    </div>
<div class="break"></div>
    </div>
    </div>

    <div class="block">
    <label>{'Valid to'|i18n('survey')}:</label>
    <div class="block">
    <label><input type="checkbox" name="SurveyValidToNoLimit" value="1" {section show=$survey.valid_to_array.no_limit}checked{/section} /> {'No limitation'|i18n( 'survey' )}</label>
    </div>
    <div class="block">
    <div class="element">
    <label>{'Year'|i18n( 'survey' )}:</label>
    <input name="SurveyValidToYear" size="5" value="{$survey.valid_to_array.year}" />
    </div>
    <div class="element">
    <label>{'Month'|i18n( 'survey' )}:</label>
    <input name="SurveyValidToMonth" size="3" value="{$survey.valid_to_array.month}" />
    </div>
    <div class="element">
    <label>{'Day'|i18n( 'survey' )}:</label>
    <input name="SurveyValidToDay" size="3" value="{$survey.valid_to_array.day}" />
    </div>
    <div class="element">
    <label>{'Hour'|i18n( 'survey' )}:</label>
    <input name="SurveyValidToHour" size="3" value="{$survey.valid_to_array.hour}" />
    </div>
    <div class="element">
    <label>{'Minute'|i18n( 'survey' )}:</label>
    <input name="SurveyValidToMinute" size="3" value="{$survey.valid_to_array.minute}" />
    </div>
<div class="break"></div>
    </div>
    </div>

    <div class="block">
    <label>{'After "Cancel" redirect to URL'|i18n('survey')}:</label>
    <input class="box" name="SurveyRedirectCancel" size="30" value="{$survey.redirect_cancel|wash('xhtml')}" />
    </div>

    <div class="block">
    <label>{'After "Submit" redirect to URL'|i18n('survey')}:</label>
    <input class="box" name="SurveyRedirectSubmit" size="30" value="{$survey.redirect_submit|wash('xhtml')}" />
    </div>

    <div class="block">
    <label><input type="checkbox" name="SurveyPersistent" {section show=$survey.persistent|eq(1)}checked="checked"{/section} /> {'Persistent user input. ( Users will be able to edit survey later. )'|i18n('survey')}</label>
    </div>
<div class="survey-edit">
{section name=Question loop=$survey_questions sequence=array(bgdark,bglight)}
<div class="survey-edit-option-header">
<div class="block">
<div class="element">
    {section show=$:item.can_be_selected}    <label><input name="SurveyQuestion_{$:item.id}_Selected" type="checkbox" />
    {'Selected'|i18n( 'survey' )}</label>{/section}
</div>
<div class="element">
    <label><input type="checkbox" name="SurveyQuestionVisible_{$:item.id}" {section show=$:item.visible|eq(1)}checked="checked"{/section} />{"Visible"|i18n('survey')}</label>
</div>
<div class="element">
    <input type="image" name="SurveyQuestionCopy_{$:item.id}" src={"copy.gif"|ezimage} border="0" alt="{"Copy"|i18n('survey')}" title="{"Copy question"|i18n('survey')}" />
</div>
<div class="element">
    <label>{'Order'|i18n( 'survey' )}: <input type="input" size="2" name="SurveyQuestionTabOrder_{$:item.id}" value="{$:item.tab_order}" /></label>
</div>
<div class="break"></div>
</div>
</div>
<div class="survey-edit-option">
    <input type="hidden" name="SurveyQuestionList[]" value="{$:item.id}" />
    {survey_question_edit_gui question=$:item}
</div>
{/section}
</div>

    <div class="block">
    <select name="SurveyNewQuestionType">
    {section var=type loop=$survey.question_types}
	<option value="{$type.type}">{$type.name}</option>
    {/section}
    </select>
    <input class="button" type="submit" name="SurveyNewQuestion" value="{'Add question'|i18n( 'survey' )}" />
    &nbsp;&nbsp;
    <input class="button" type="submit" name="SurveyRemoveSelected" value="{'Remove selected'|i18n( 'survey' )}" />
    </div>

    <div class="block">
    <input class="button" type="submit" name="SurveyPublishButton" value="{'Publish'|i18n( 'survey' )}" />
    <input class="button" type="submit" name="SurveyApplyButton" value="{'Apply'|i18n( 'survey' )}"  />
    <input class="button" type="submit" name="SurveyPreviewButton" value="{'Apply and Preview'|i18n( 'survey' )}"  />
    <input class="button" type="submit" name="SurveyDiscardButton" value="{'Discard'|i18n( 'survey' )}" />
    </div>

</form>
