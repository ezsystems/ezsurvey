{let survey=fetch( 'survey', 'survey', hash( 'id', $id ) )}
{section show=$survey}
{include uri="design:survey/view_embed.tpl" survey=$survey}
{/section}
{/let}
