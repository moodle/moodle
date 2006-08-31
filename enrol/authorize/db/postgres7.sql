CREATE TABLE prefix_enrol_authorize (
    id SERIAL PRIMARY KEY,
    paymentmethod varchar(6) default 'cc' NOT NULL,
    cclastfour integer DEFAULT 0 NOT NULL,
    ccname varchar(255) DEFAULT '',
    courseid integer DEFAULT 0 NOT NULL,
    userid integer DEFAULT 0 NOT NULL,
    transid integer DEFAULT 0 NOT NULL,
    status integer DEFAULT 0 NOT NULL,
    timecreated integer DEFAULT 0 NOT NULL,
    settletime integer DEFAULT 0 NOT NULL,
    amount varchar(10) DEFAULT '0' NOT NULL,
    currency varchar(3) DEFAULT 'USD' NOT NULL,
    CONSTRAINT enroauth_pay_ck CHECK (paymentmethod IN ('cc', 'echeck'))
);

CREATE INDEX prefix_enrol_authorize_courseid_idx ON prefix_enrol_authorize(courseid);
CREATE INDEX prefix_enrol_authorize_userid_idx ON prefix_enrol_authorize(userid);
CREATE INDEX prefix_enrol_authorize_status_idx ON prefix_enrol_authorize(status);
CREATE INDEX prefix_enrol_authorize_transid_idx ON prefix_enrol_authorize(transid);

CREATE TABLE prefix_enrol_authorize_refunds (
    id SERIAL PRIMARY KEY,
    orderid integer DEFAULT 0 NOT NULL,
    status integer DEFAULT 0 NOT NULL,
    amount varchar(10) DEFAULT '' NOT NULL,
    transid integer DEFAULT 0,
    settletime integer DEFAULT 0 NOT NULL
);

CREATE INDEX prefix_enrol_authorize_refunds_orderid_idx ON prefix_enrol_authorize_refunds(orderid);
CREATE INDEX prefix_enrol_authorize_refunds_transid_idx ON prefix_enrol_authorize_refunds(transid);
