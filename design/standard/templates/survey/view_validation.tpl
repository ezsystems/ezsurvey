{section show=and( is_set( $survey_validation ), or( $survey_validation.error, $survey_validation.warning ))}
<div class="message-warning">
<h2>{"Warning"|i18n( 'survey' )}</h2>
<ul>
{foreach $survey_validation.errors as $key => $error}
  <li>
  <a href="#survey_question_{$error.question_number}">{$error.question_number}</a>:   {$error.message}
  </li>
{/foreach}
{foreach $survey_validation.warnings as $key => $warning}
  <li>
  <a href="#survey_question_{$warning.question_number}">{$warning.question_number}</a>: {$warning.message}
  </li>
{/foreach}
</ul>
</div>
<br/ >
{/section}
