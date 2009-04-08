-- ezsurvey: index on primary key id

ALTER TABLE ONLY ezsurvey
    ADD CONSTRAINT ezsurvey_pkey PRIMARY KEY (id);

CREATE INDEX ezsurvey_contentobject_id ON ezsurvey(contentobject_id); 
CREATE INDEX ezsurvey_contentobjectattribute_id ON ezsurvey(contentobjectattribute_id); 
CREATE INDEX ezsurvey_contentobjectattribute_version ON ezsurvey(contentobjectattribute_version); 
CREATE INDEX ezsurvey_contentclassattribute_id ON ezsurvey(contentclassattribute_id); 
CREATE INDEX ezsurvey_language_code ON ezsurvey(language_code);

-- ezsurveyquestion: index on primary key id, index on survey_id

ALTER TABLE ONLY ezsurveyquestion
    ADD CONSTRAINT ezsurveyquestion_pkey PRIMARY KEY (id);

CREATE INDEX ezsurveyquestion_survey_id ON ezsurveyquestion(survey_id);

-- ezsurveyresult: index on primary key id, index on survey_id

ALTER TABLE ONLY ezsurveyresult
    ADD CONSTRAINT ezsurveyresult_pkey PRIMARY KEY (id);

CREATE INDEX ezsurveyresult_survey_id ON ezsurveyresult(survey_id);

-- ezsurveyquestionresult: index on primary key id, index on result_id, index on question_id

ALTER TABLE ONLY ezsurveyquestionresult
    ADD CONSTRAINT ezsurveyquestionresult_pkey PRIMARY KEY (id);

CREATE INDEX ezsurveyquestionresult_result_id ON ezsurveyquestionresult(result_id);
CREATE INDEX ezsurveyquestionresult_question_id ON ezsurveyquestionresult(question_id);

-- ezsurveymetadata: index on primary key id, index on result_id, index on attr_name, index on attr_value

ALTER TABLE ONLY ezsurveymetadata
    ADD CONSTRAINT ezsurveymetadata_pkey PRIMARY KEY (id);

CREATE INDEX ezsurveymetadata_result_id ON ezsurveymetadata(result_id);
CREATE INDEX ezsurveymetadata_attr_name ON ezsurveymetadata(attr_name);
CREATE INDEX ezsurveymetadata_attr_value ON ezsurveymetadata(attr_value);
