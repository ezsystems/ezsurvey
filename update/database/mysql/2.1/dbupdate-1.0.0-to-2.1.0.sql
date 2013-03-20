ALTER TABLE ezsurvey
    ADD one_answer int(11) DEFAULT 0 NOT NULL,
    ADD contentobject_id int(11) DEFAULT 0 NOT NULL,
    ADD contentobjectattribute_id int(11) DEFAULT 0 NOT NULL,
    ADD contentobjectattribute_version int(11) DEFAULT 0 NOT NULL,
    ADD contentclassattribute_id int(11) DEFAULT 0 NOT NULL,
    ADD language_code varchar(20) DEFAULT NULL,
    ADD INDEX ezsurvey_contentobject_id (contentobject_id),
    ADD INDEX ezsurvey_contentobjectattribute_id (contentobjectattribute_id),
    ADD INDEX ezsurvey_contentobjectattribute_version (contentobjectattribute_version),
    ADD INDEX ezsurvey_contentclassattribute_id (contentclassattribute_id),
    ADD INDEX ezsurvey_language_code (language_code);

ALTER TABLE ezsurveyquestion
    ADD original_id int(11) DEFAULT 0 NOT NULL;

ALTER TABLE ezsurveyresult
    ADD user_session_id varchar(255) DEFAULT '' NOT NULL;

ALTER TABLE ezsurveyquestionresult
    ADD questionoriginal_id int(11) DEFAULT 0 NOT NULL;

CREATE TABLE ezsurveyrelatedconfig (
    id int(11) NOT NULL auto_increment,
    contentclass_id int(11) DEFAULT 1 NOT NULL,
    node_id int(11) DEFAULT 0 NOT NULL,
    PRIMARY KEY (id)
);


CREATE TABLE ezsurveyquestionmetadata (
    id int(11) NOT NULL auto_increment,
    result_id int(11) DEFAULT 0 NOT NULL,
    question_id int(11) DEFAULT 0 NOT NULL,
    question_original_id int(11) DEFAULT 0 NOT NULL,
    name varchar(255) NOT NULL,
    value longtext,
    PRIMARY KEY (id)
);

ALTER TABLE ezsurvey ENGINE = innodb;
ALTER TABLE ezsurveyquestion ENGINE = innodb;
ALTER TABLE ezsurveyresult ENGINE = innodb;
ALTER TABLE ezsurveyquestionresult ENGINE = innodb;
ALTER TABLE ezsurveymetadata ENGINE = innodb;
ALTER TABLE ezsurveyrelatedconfig ENGINE = innodb;
ALTER TABLE ezsurveyquestionmetadata ENGINE = innodb;
