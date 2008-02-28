README - some information about the survey module


LABEL WRAP

The <label> tag is set to "white-space: nowrap;" in the eZ publish admin CSS.
This can be a problem if you have long survey questions. To override this,
add this in the <head> section of your pagelayout.tpl:

<link rel="stylesheet" type="text/css" href={"stylesheets/survey.css"|ezdesign} />


SQL UPDATES

If you're using an old version of this extension then you might need to run
the following SQL queries:

ALTER TABLE ezsurvey ADD COLUMN persistent int NOT NULL DEFAULT 0;
ALTER TABLE ezsurvey ADD COLUMN visible int NOT NULL DEFAULT 1;
ALTER TABLE ezsurveyquestion ADD COLUMN visible int NOT NULL DEFAULT 1;

-- WARNING: This one destroys text results!
ALTER TABLE ezsurveyquestionresult DROP COLUMN text;
ALTER TABLE ezsurveyquestionresult ADD COLUMN text longtext;
