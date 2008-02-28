CREATE SEQUENCE ezsurvey_s
    START 1
    INCREMENT 1
    MAXVALUE 9223372036854775807
    MINVALUE 1
    CACHE 1;

CREATE TABLE ezsurvey (
    id integer DEFAULT nextval('ezsurvey_s'::text) NOT NULL,
    title varchar DEFAULT '' NOT NULL,
    enabled integer DEFAULT 1 NOT NULL,
    valid_from integer DEFAULT 0 NOT NULL,
    valid_to integer DEFAULT 0 NOT NULL,
    redirect_cancel varchar DEFAULT '' NOT NULL,
    redirect_submit varchar DEFAULT '' NOT NULL,
    published integer DEFAULT 0 NOT NULL,
    persistent integer DEFAULT 0 NOT NULL,
    contentobjectattribute_id integer DEFAULT 0 NOT NULL,
    contentobjectattribute_version integer DEFAULT 0 NOT NULL
);

SELECT setval('ezsurvey_s', max(id)) , 'ezsurvey' as tablename FROM ezsurvey;

--

CREATE SEQUENCE ezsurveyquestion_s
    START 1
    INCREMENT 1
    MAXVALUE 9223372036854775807
    MINVALUE 1
    CACHE 1;

CREATE TABLE ezsurveyquestion (
    id integer DEFAULT nextval('ezsurveyquestion_s'::text) NOT NULL,
    survey_id integer DEFAULT 0 NOT NULL,
    tab_order integer DEFAULT 0 NOT NULL,
    mandatory integer DEFAULT 1 NOT NULL,
    visible integer DEFAULT 1 NOT NULL,
    type varchar DEFAULT '' NOT NULL,
    default_value varchar,
    text varchar,
    text2 varchar,
    text3 varchar,
    num integer,
    num2 integer
);

SELECT setval('ezsurveyquestion_s', max(id)) , 'ezsurveyquestion' as tablename FROM ezsurveyquestion;

--

CREATE SEQUENCE ezsurveyresult_s
    START 1
    INCREMENT 1
    MAXVALUE 9223372036854775807
    MINVALUE 1
    CACHE 1;

CREATE TABLE ezsurveyresult (
    id integer DEFAULT nextval('ezsurveyresult_s'::text) NOT NULL,
    survey_id integer DEFAULT 0 NOT NULL,
    user_id integer DEFAULT 0 NOT NULL,
    tstamp integer DEFAULT 0 NOT NULL,
    user_session_id varchar DEFAULT '' NOT NULL
);

SELECT setval('ezsurveyresult_s', max(id)) , 'ezsurveyresult' as tablename FROM ezsurveyresult;

--

CREATE SEQUENCE ezsurveyquestionresult_s
    START 1
    INCREMENT 1
    MAXVALUE 9223372036854775807
    MINVALUE 1
    CACHE 1;

CREATE TABLE ezsurveyquestionresult (
    id integer DEFAULT nextval('ezsurveyquestionresult_s'::text) NOT NULL,
    result_id integer DEFAULT 0 NOT NULL,
    question_id integer DEFAULT 0 NOT NULL,
    text varchar
);

SELECT setval('ezsurveyquestionresult_s', max(id)) , 'ezsurveyquestionresult' as tablename FROM ezsurveyquestionresult;

--

CREATE SEQUENCE ezsurveymetadata_s
    START 1
    INCREMENT 1
    MAXVALUE 9223372036854775807
    MINVALUE 1
    CACHE 1;

CREATE TABLE ezsurveymetadata (
    id integer DEFAULT nextval('ezsurveymetadata_s'::text) NOT NULL,
    result_id integer DEFAULT 0 NOT NULL,
    attr_name varchar NOT NULL,
    attr_value varchar NOT NULL
);

SELECT setval('ezsurveymetadata_s', max(id)) , 'ezsurveymetadata' as tablename FROM ezsurveymetadata;
