<form action={concat("/survey/remove/",$survey.id)|ezurl} method="post">

<div class="message-warning">
<h2>{"Are you sure you want to remove the survey '%1' with all evaluations?"|i18n('survey',,array($survey.title))}</h2>
</div>

<div class="block">
{include uri="design:gui/defaultbutton.tpl" name=ConfirmButton id_name=SurveyRemoveCommit value="Confirm"|i18n("survey")}
{include uri="design:gui/button.tpl" name=CancelButton id_name=SurveyRemoveCancel value="Cancel"|i18n("survey")}
</div>
</form>
