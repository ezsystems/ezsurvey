# Convert ezsurveyquestionresult.text from CLOB (when imported using the .dba file in 2.2-) to VARCHAR(4000)
ALTER TABLE ezsurveyquestionresult ADD text_temp VARCHAR2(4000);
UPDATE ezsurveyquestionresult SET text_temp = text;
ALTER TABLE ezsurveyquestionresult DROP COLUMN text;
ALTER TABLE ezsurveyquestionresult RENAME COLUMN text_temp TO text;