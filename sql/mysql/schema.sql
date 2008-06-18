CREATE TABLE ezsurvey (
    id int(11) NOT NULL auto_increment,
    title varchar(255) DEFAULT '' NOT NULL,
    enabled int(11) DEFAULT 1 NOT NULL,
    valid_from int(11) DEFAULT 0 NOT NULL,
    valid_to int(11) DEFAULT 0 NOT NULL,
    redirect_cancel varchar(255) DEFAULT '' NOT NULL,
    redirect_submit varchar(255) DEFAULT '' NOT NULL,
    published int(11) DEFAULT 0 NOT NULL,
    persistent int(11) DEFAULT 0 NOT NULL,
    one_answer int(11) DEFAULT 0 NOT NULL,
    contentobject_id int(11) DEFAULT 0 NOT NULL,
    contentobjectattribute_id int(11) DEFAULT 0 NOT NULL,
    contentobjectattribute_version int(11) DEFAULT 0 NOT NULL,
    contentclassattribute_id int(11) DEFAULT 0 NOT NULL,
    language_code varchar(20) DEFAULT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE ezsurveyquestion (
    id int(11) NOT NULL auto_increment,
    survey_id int(11) DEFAULT 0 NOT NULL,
    original_id int(11) DEFAULT 0 NOT NULL,
    tab_order int(11) DEFAULT 0 NOT NULL,
    mandatory int(11) DEFAULT 1 NOT NULL,
    visible int(11) DEFAULT 1 NOT NULL,
    type varchar(255) DEFAULT '' NOT NULL,
    default_value longtext,
    text longtext,
    text2 longtext,
    text3 longtext,
    num int(11),
    num2 int(11),
    PRIMARY KEY (id),
    KEY ezsurveyquestion_survey_id (survey_id)
);

CREATE TABLE ezsurveyresult (
    id int(11) NOT NULL auto_increment,
    survey_id int(11) DEFAULT 0 NOT NULL,
    user_id int(11) DEFAULT 0 NOT NULL,
    tstamp int(11) DEFAULT 0 NOT NULL,
    user_session_id varchar(255) DEFAULT '' NOT NULL,
    PRIMARY KEY (id),
    KEY ezsurveyresult_survey_id (survey_id)
);

CREATE TABLE ezsurveyquestionresult (
    id int(11) NOT NULL auto_increment,
    result_id int(11) DEFAULT 0 NOT NULL,
    question_id int(11) DEFAULT 0 NOT NULL,
    questionoriginal_id int(11) DEFAULT 0 NOT NULL,
    text longtext,
    PRIMARY KEY (id),
    KEY ezsurveyquestionresult_result_id (result_id),
    KEY ezsurveyquestionresult_question_id (question_id)
);

CREATE TABLE ezsurveymetadata (
    id int(11) NOT NULL auto_increment,
    result_id int(11) DEFAULT 0 NOT NULL,
    attr_name varchar(255) NOT NULL,
    attr_value varchar(255) NOT NULL,
    PRIMARY KEY (id),
    KEY ezsurveymetadata_result_id (result_id),
    KEY ezsurveymetadata_attr_name (attr_name),
    KEY ezsurveymetadata_attr_value (attr_value)
);

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

ALTER TABLE ezsurvey TYPE = innodb;
ALTER TABLE ezsurveyquestion TYPE = innodb;
ALTER TABLE ezsurveyresult TYPE = innodb;
ALTER TABLE ezsurveyquestionresult TYPE = innodb;
ALTER TABLE ezsurveymetadata TYPE = innodb;
ALTER TABLE ezsurveyrelatedconfig TYPE = innodb;
ALTER TABLE ezsurveyquestionmetadata TYPE = innodb;
