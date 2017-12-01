/*
-- Oussema Hidri
-- d.oussema.d@gmail.com
--------------------------------------------
-- Host: localhost
-- Database: testdb
-- Username: testuser
-- Password: password 
-------------------------------------------
*/

-- Change the sql mode for handling times
-- from NO_ZERO_DATE or NO_ZERO_IN_DATE to ''
SET sql_mode = '';

-- Disable FOREIGN KEY CHECK
SET FOREIGN_KEY_CHECKS = 0;

/*
-- 
-- Table structure for table `email_domains`
--
*/
DROP TABLE IF EXISTS email_domains;

CREATE TABLE email_domains (
    id INT(11) NOT NULL AUTO_INCREMENT,
    domain_name VARCHAR(50) NOT NULL,
    domain_tld VARCHAR(6) NOT NULL,
    UNIQUE (domain_name, domain_tld),
    PRIMARY KEY (id)
);
/*
-- @domain_name: email domain name [gmail, hotmail, ...]
-- @domain_tld: top level domain [com, fr, org, ...]
*/

--
-- Dumping data for table `email_domains`
--

LOCK TABLES email_domains WRITE;

INSERT INTO email_domains (domain_name, domain_tld) VALUES ('gmail', 'com');
INSERT INTO email_domains (domain_name, domain_tld) VALUES ('hotmail', 'com');
INSERT INTO email_domains (domain_name, domain_tld) VALUES ('hotmail', 'fr');

UNLOCK TABLES;

--
-- End of dumping into table `email_domains`
--



--
-- Table structure for table `email`
--

-- Development Only!
DROP TABLE IF EXISTS email;

CREATE TABLE email (
    id INT(11) NOT NULL AUTO_INCREMENT,
    local_part VARCHAR(64) NOT NULL,
    domain_id INT(20) NOT NULL,
    UNIQUE (domain_id, local_part),
    PRIMARY KEY (id),
    FOREIGN KEY (domain_id) REFERENCES email_domains(id)
);

--
-- Dumping data for tale `email`
--

/*LOCK TABLES email WRITE;*/

INSERT INTO email (local_part, domain_id) VALUES (
    'd.oussema.d',
    (SELECT id FROM email_domains WHERE domain_name='gmail' AND domain_tld='com'));

/*
-- You can also use this method to insert with a select query
INSERT INTO email VALUES (local_part, domain_id)
    SELECT id, 'd.oussema.d' FROM email_domains WHERE domain_name='gmail' AND domain_tld='com';
*/

UNLOCK TABLES;

--
-- End of dumping into table `email`
--

--
-- Table structure for table `user_registery`
--

-- Development Only ! 
DROP TABLE IF EXISTS user_registery;

CREATE TABLE user_registery (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name varchar(100) NOT NULL,
    last_name varchar(100) NOT NULL,
    birthday date,
    username varchar(100) NOT NULL,
    password varchar(64) NOT NULL,
    melh varchar(20) NOT NULL,
    email INT(11) NOT NULL,
    sub_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    verified TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE (username),
    FOREIGN KEY (email) REFERENCES email(id)
);

