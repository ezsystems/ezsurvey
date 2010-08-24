<script type="text/javascript">
<!--
{literal}
function synchFormElements()
{
    var synchElement = document.getElementById( synchFormElements.arguments[0] );
    if ( synchElement != undefined && synchElement != null )
    {
        var tag = synchElement.tagName.toLowerCase();
        var synchPolarity = synchFormElements.arguments[ arguments.length - 1 ];
        var inputArray = new Array();

        for ( var x = 0; x < ( synchFormElements.arguments.length - 2 ); x++ )
        {
            inputArray[x] = synchFormElements.arguments[ x + 1 ];
        }

        if ( ( tag == 'input' && ( ( synchElement.checked && synchPolarity ) || ( !synchElement.checked && !synchPolarity ) ) ) || ( tag == 'option' && ( ( synchElement.selected && synchPolarity ) || ( !synchElement.selected && !synchPolarity ) ) ) )
        {
            setFormElements( inputArray, true );
        }
        else
        {
            setFormElements( inputArray, false );
        }
    }
}

function setFormElements( inputArray, enabled )
{
    var modeString = '';

    if ( !enabled )
    {
        modeString = 'disabled';
    }

    for ( var x = 0; x < inputArray.length; x++ )
    {
        var inputElement = document.getElementById( inputArray[x] );
        inputElement.disabled = modeString;
        if ( modeString == 'disabled' )
        {
            setClass( inputElement, 'disabled' );
        }
        else
        {
            removeClass( inputElement, 'disabled' );
        }
    }
}

function readClassArray( element )
{
    if ( typeof( element ) == 'string' )
    {
        element = document.getElementById( element );
    }

    var classString = element.className;
    var classArray = classString.split( ' ' );
    return classArray;
}

function writeClassArray( element, classArray )
{
    if ( typeof( element ) == 'string' )
    {
        element = document.getElementById( element );
    }

    var classString = classArray.join( ' ' );
    element.className = classString;
}

function setClass( element, className )
{
    if ( !checkClass( element, className ) )
    {
        var classArray = readClassArray( element );
        classArray[ classArray.length ] = className;
        writeClassArray( element, classArray );
    }
}

function checkClass( element, className )
{
    var classArray = readClassArray( element );

    for ( x = 0; x < classArray.length; x++ )
    {
        if ( classArray[ x ] == className )
        {
            return true;
        }
    }

    return false;
}

function removeClass( element, className )
{
    var classArray = readClassArray( element );

    for ( x = 0; x < classArray.length; x++ )
    {
        if ( classArray[ x ] == className )
        {
            classArray[ x ] = '';
        }
    }

    writeClassArray( element, classArray );
}
{/literal}
// -->
</script>

<fieldset>
<legend>{'Survey'|i18n( 'survey' )}</legend>
{def $survey=$attribute.content.survey}
{def $survey_validation=$attribute.content.survey_validation}
{def $survey_questions=$survey.questions}
{def $prefixAttribute='ContentObjectAttribute'}
{def $attributeID=$attribute.id}
<input type="hidden" name="{$prefixAttribute}_ezsurvey_id_{$attributeID}" value="{$survey.id}" />
<input type="hidden" name="ContentObjectAttribute_id[]" value="{$attributeID}" />

    {include uri="design:survey/edit_validation.tpl"}


    <input type="hidden" name="{$prefixAttribute}_ezsurvey_title_{$attributeID}" value="{$survey.title|wash('xhtml')}" />

    <div class="block float-break">
    <div class="element">
    <label><input type="checkbox" name="{$prefixAttribute}_ezsurvey_enabled_{$attributeID}" {section show=$survey.enabled|eq(1)}checked="checked"{/section} /> {'Enabled'|i18n( 'survey' )}</label>
    </div>
<div class="element">
    <label><input type="checkbox" name="{$prefixAttribute}_ezsurvey_one_answer_{$attributeID}"{section show=$survey.one_answer|eq(1)} checked="checked"{/section} /> {'Only one answer allowed.'|i18n('survey')}</label>
    </div>
    <div class="element">
    <label><input type="checkbox" name="{$prefixAttribute}_ezsurvey_persistent_{$attributeID}"{section show=$survey.persistent|eq(1)} checked="checked"{/section} /> {'Persistent user input. ( Users will be able to edit survey later. )'|i18n('survey')}</label>
    </div>
    </div>

    <div class="block">
    <fieldset>
    <legend>{'Active from'|i18n('survey')}</legend>
    <div class="block float-break">
    <div class="element">
    <label>{'No limitation'|i18n( 'survey' )}:</label>
    <input id="{$prefixAttribute}_ezsurvey_valid_from_no_limit_{$attributeID}" type="checkbox" name="{$prefixAttribute}_ezsurvey_valid_from_no_limit_{$attributeID}" value="1"{section show=$survey.valid_from_array.no_limit} checked="checked"{/section} onclick="synchFormElements( '{$prefixAttribute}_ezsurvey_valid_from_no_limit_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_from_year_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_from_month_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_from_day_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_from_hour_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_from_minute_{$attributeID}', false )" />
    </div>
    <div class="element">
    <label>{'Year'|i18n( 'survey' )}:</label>
    <input id="{$prefixAttribute}_ezsurvey_valid_from_year_{$attributeID}" name="{$prefixAttribute}_ezsurvey_valid_from_year_{$attributeID}" size="5" value="{$survey.valid_from_array.year}" />
    <input type="hidden" name="{$prefixAttribute}_ezsurvey_valid_from_year_hidden_{$attributeID}" size="5" value="{$survey.valid_from_array.year}" />
    </div>
    <div class="element">
    <label>{'Month'|i18n( 'survey' )}:</label>
    <input id="{$prefixAttribute}_ezsurvey_valid_from_month_{$attributeID}" name="{$prefixAttribute}_ezsurvey_valid_from_month_{$attributeID}" size="3" value="{$survey.valid_from_array.month}" />
    <input type="hidden" name="{$prefixAttribute}_ezsurvey_valid_from_month_hidden_{$attributeID}" size="3" value="{$survey.valid_from_array.month}" />
    </div>
    <div class="element">
    <label>{'Day'|i18n( 'survey' )}:</label>
    <input id="{$prefixAttribute}_ezsurvey_valid_from_day_{$attributeID}" name="{$prefixAttribute}_ezsurvey_valid_from_day_{$attributeID}" size="3" value="{$survey.valid_from_array.day}" />
    <input type="hidden" name="{$prefixAttribute}_ezsurvey_valid_from_day_hidden_{$attributeID}" size="3" value="{$survey.valid_from_array.day}" />
    </div>
    <div class="element">
    <label>{'Hour'|i18n( 'survey' )}:</label>
    <input id="{$prefixAttribute}_ezsurvey_valid_from_hour_{$attributeID}" name="{$prefixAttribute}_ezsurvey_valid_from_hour_{$attributeID}" size="3" value="{$survey.valid_from_array.hour}" />
    <input type="hidden" name="{$prefixAttribute}_ezsurvey_valid_from_hour_hidden_{$attributeID}" size="3" value="{$survey.valid_from_array.hour}" />
    </div>
    <div class="element">
    <label>{'Minute'|i18n( 'survey' )}:</label>
    <input id="{$prefixAttribute}_ezsurvey_valid_from_minute_{$attributeID}" name="{$prefixAttribute}_ezsurvey_valid_from_minute_{$attributeID}" size="3" value="{$survey.valid_from_array.minute}" />
    <input type="hidden" name="{$prefixAttribute}_ezsurvey_valid_from_minute_hidden_{$attributeID}" size="3" value="{$survey.valid_from_array.minute}" />
    </div>
    </div>
    </fieldset>
    </div>

    <div class="block">
    <fieldset>
    <legend>{'Active to'|i18n('survey')}</legend>
    <div class="block float-break">
    <div class="element">
        <label>{'No limitation'|i18n( 'survey' )}:</label>
    <input id="{$prefixAttribute}_ezsurvey_valid_to_no_limit_{$attributeID}" type="checkbox" name="{$prefixAttribute}_ezsurvey_valid_to_no_limit_{$attributeID}" value="1"{section show=$survey.valid_to_array.no_limit} checked="checked"{/section} onclick="synchFormElements( '{$prefixAttribute}_ezsurvey_valid_to_no_limit_{$attributeID}',  '{$prefixAttribute}_ezsurvey_valid_to_year_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_to_month_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_to_day_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_to_hour_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_to_minute_{$attributeID}', false );" />
    </div>
    <div class="element">
    <label>{'Year'|i18n( 'survey' )}:</label>
    <input id="{$prefixAttribute}_ezsurvey_valid_to_year_{$attributeID}" name="{$prefixAttribute}_ezsurvey_valid_to_year_{$attributeID}" size="5" value="{$survey.valid_to_array.year}" />
    <input type="hidden" name="{$prefixAttribute}_ezsurvey_valid_to_year_hidden_{$attributeID}" size="5" value="{$survey.valid_to_array.year}" />
    </div>
    <div class="element">
    <label>{'Month'|i18n( 'survey' )}:</label>
    <input id="{$prefixAttribute}_ezsurvey_valid_to_month_{$attributeID}" name="{$prefixAttribute}_ezsurvey_valid_to_month_{$attributeID}" size="3" value="{$survey.valid_to_array.month}" />
    <input type="hidden" name="{$prefixAttribute}_ezsurvey_valid_to_month_hidden_{$attributeID}" size="3" value="{$survey.valid_to_array.month}" />
    </div>
    <div class="element">
    <label>{'Day'|i18n( 'survey' )}:</label>
    <input id="{$prefixAttribute}_ezsurvey_valid_to_day_{$attributeID}" name="{$prefixAttribute}_ezsurvey_valid_to_day_{$attributeID}" size="3" value="{$survey.valid_to_array.day}" />
    <input type="hidden" name="{$prefixAttribute}_ezsurvey_valid_to_day_hidden_{$attributeID}" size="3" value="{$survey.valid_to_array.day}" />
    </div>
    <div class="element">
    <label>{'Hour'|i18n( 'survey' )}:</label>
    <input id="{$prefixAttribute}_ezsurvey_valid_to_hour_{$attributeID}" name="{$prefixAttribute}_ezsurvey_valid_to_hour_{$attributeID}" size="3" value="{$survey.valid_to_array.hour}" />
    <input type="hidden" name="{$prefixAttribute}_ezsurvey_valid_to_hour_hidden_{$attributeID}" size="3" value="{$survey.valid_to_array.hour}" />
    </div>
    <div class="element">
    <label>{'Minute'|i18n( 'survey' )}:</label>
    <input id="{$prefixAttribute}_ezsurvey_valid_to_minute_{$attributeID}" name="{$prefixAttribute}_ezsurvey_valid_to_minute_{$attributeID}" size="3" value="{$survey.valid_to_array.minute}" />
    <input type="hidden" name="{$prefixAttribute}_ezsurvey_valid_to_minute_hidden_{$attributeID}" size="3" value="{$survey.valid_to_array.minute}" />
    </div>
    </div>
    </fieldset>
    </div>

    <input type="hidden" name="{$prefixAttribute}_ezsurvey_redirect_cancel_{$attributeID}" value="" />

    <div class="block">
    <label>{'After "Submit" redirect to URL'|i18n('survey')}:</label>
    <input class="box" name="{$prefixAttribute}_ezsurvey_redirect_submit_{$attributeID}" size="30" value="{$survey.redirect_submit|wash('xhtml')}" />
    </div>

<div class="survey-edit">
<table class="list" cellspacing="0">
{section name=Question loop=$survey_questions sequence=array(bgdark,bglight)}
<tr>
    <th class="tight">
    {section show=$:item.can_be_selected}<input name="{$prefixAttribute}_ezsurvey_question_{$:item.id}_selected_{$attributeID}" type="checkbox" />{/section}
    </th>
    <th>ID: {$:item.id}</th>
    <th class="tight">
    <label><input type="checkbox" name="{$prefixAttribute}_ezsurvey_question_visible_{$:item.id}_{$attributeID}"{section show=$:item.visible|eq(1)} checked="checked"{/section} /> {"Visible"|i18n('survey')}</label>
    </th>
    <th class="tight"><input type="image" name="CustomActionButton[{$attributeID}_ezsurvey_question_copy_{$:item.id}]" value="" src={"copy.gif"|ezimage()} /></th>
    <th class="tight"><input type="text" size="2" name="{$prefixAttribute}_ezsurvey_question_tab_order_{$:item.id}_{$attributeID}" value="{$:item.tab_order}" /></th>
</tr>
<tr>
    <td>&nbsp;</td>
    <td colspan="4">
    <input type="hidden" name="{$prefixAttribute}_ezsurvey_question_list_{$attributeID}[]" value="{$:item.id}" />
    <a name="survey_question_{$:item.id}"></a>
    {survey_question_edit_gui question=$:item attribute_id=$attributeID prefix_attribute=$prefixAttribute}
    </td>
</tr>

{/section}
</table>
</div>

    <div class="block">
    <input class="button" type="submit" name="CustomActionButton[{$attributeID}_remove_selected]" value="{'Remove selected'|i18n( 'survey' )}" />&nbsp;&nbsp;&nbsp;&nbsp;
    <select name="{$prefixAttribute}_ezsurvey_new_question_type_{$attributeID}">
    {section var=type loop=$survey.question_types}
	<option value="{$type.type}"{if $attribute.content.last_new_question_type|eq($type.type)} selected="selected"{/if}>{$type.name}</option>
    {/section}
    </select>
    <input class="button" type="submit" name="CustomActionButton[{$attributeID}_new_question]" value="{'New attribute'|i18n( 'survey' )}" />
    </div>
</fieldset>

<script type="text/javascript">
<!--
synchFormElements( '{$prefixAttribute}_ezsurvey_valid_from_no_limit_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_from_year_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_from_month_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_from_day_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_from_hour_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_from_minute_{$attributeID}', false );

synchFormElements( '{$prefixAttribute}_ezsurvey_valid_to_no_limit_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_to_year_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_to_month_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_to_day_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_to_hour_{$attributeID}', '{$prefixAttribute}_ezsurvey_valid_to_minute_{$attributeID}', false );
// -->
</script>
