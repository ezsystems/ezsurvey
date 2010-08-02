{if $question.num|gt(0)}
{def $surveyobject=fetch('content','object', hash('object_id', $question.num))}
{foreach $surveyobject.data_map as $datatype}
{if $datatype.data_type_string|eq('eztext')}
{$datatype.content}
{/if}
{/foreach}
{/if}

