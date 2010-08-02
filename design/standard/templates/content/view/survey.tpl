{section name=ContentObjectAttribute loop=$object.contentobject_attributes}
{if $:item.data_type_string|eq('ezxmltext')}
<div class="block">
    {attribute_view_gui attribute=$ContentObjectAttribute:item}
</div>
{/if}
{/section}