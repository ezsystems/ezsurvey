{if $question.num|gt(0)}
{def $surveyobject=fetch('content','object', hash('object_id', $question.num))}
{content_view_gui content_object=$surveyobject view='survey'}
{/if}