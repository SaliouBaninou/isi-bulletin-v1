-- Active: 1702105196646@@127.0.0.1@3306@bulletinisi
CREATE TABLE admin(
    id INTEGER  AUTO_INCREMENT NOT NULL,
    name VARCHAR(250),
    identifiant VARCHAR(250),
    password TEXT,
    PRIMARY KEY(id)
);

CREATE TABLE levels(
    id INTEGER  AUTO_INCREMENT NOT NULL,
    name VARCHAR(250),
    PRIMARY KEY(id)
);


CREATE TABLE students(
    id INTEGER  AUTO_INCREMENT NOT NULL,
    firstname VARCHAR(250) NOT NULL,
    lastname VARCHAR(250) NOT NULL,
    dateOfBirth VARCHAR(100) NOT NULL,
    placeOfBirth VARCHAR(250) NOT NULL,
    mail VARCHAR(300) NOT NULL,
    password TEXT NOT NULL,
    motDePasse TEXT NOT NULL,
    profile VARCHAR(250) NOT NULL,
    field_id INT NOT NULL,
    Foreign Key (field_id) REFERENCES fields(id),
    classe_id INT NOT NULL,
    Foreign Key (classe_id) REFERENCES classe(id),
    serie_id INT NOT NULL,
    Foreign Key (serie_id) REFERENCES series(id),
    school_id INT NOT NULL,
    Foreign Key (school_id) REFERENCES school(id),
    admin_id INT NOT NULL,
    Foreign Key (admin_id) REFERENCES admin(id),
    level_id INT NOT NULL,
    Foreign Key (level_id) REFERENCES levels(id),
    PRIMARY KEY(id)
);

CREATE TABLE bulletin(
    id INTEGER AUTO_INCREMENT NOT NULL,
    college_year INTEGER,
    bulletin1 VARCHAR(250),
    bulletin2 VARCHAR(250),
    student_id INT,
    Foreign Key (student_id) REFERENCES students(id),
    PRIMARY KEY(id)
);

CREATE TABLE admine(
    id INTEGER AUTO_INCREMENT NOT NULL,
    admin_id INT,
    bulletin_id INT,
    Foreign Key (admin_id) REFERENCES admin(id),
    Foreign Key (bulletin_id) REFERENCES bulletin(id),
    PRIMARY key(id)
);

CREATE TABLE classe(
    id INTEGER AUTO_INCREMENT NOT NULL,
    name VARCHAR(250) NOT NULL,
    PRIMARY key(id)
);

CREATE TABLE fields(
    id INTEGER AUTO_INCREMENT NOT NULL,
    name VARCHAR(250) NOT NULL,
    PRIMARY key(id)
);

CREATE TABLE series(
    id INTEGER AUTO_INCREMENT NOT NULL,
    name VARCHAR(250) NOT NULL,
    PRIMARY key(id)
);
CREATE TABLE school(
    id INTEGER AUTO_INCREMENT NOT NULL,
    name VARCHAR(250) NOT NULL,
    PRIMARY key(id)
);






SELECT * FROM  admin WHERE identifiant = 'saliou';

SELECT * FROM  students   JOIN bulletin ON students.id = bulletin.student_id ;

UPDATE bulletin SET bulletin1 = "saliou" WHERE student_id = 1;

INSERT INTO bulletin(student_id) VALUES(2);

SELECT COUNT(id) FROM bulletin WHERE student_id = 1;

DELETE  FROM classe WHERE id = 9;


SELECT stdt.id, stdt.firstname,stdt.lastname, stdt.dateOfBirth, stdt.placeOfBirth,
    stdt.mail, stdt.password, stdt.profile, cls.name as classe_id ,
    fds.name as field_id, srs.name as serie_id, scl.name as school_id, lvl.name as level_id
FROM  students stdt  
JOIN classe cls ON stdt.classe_id = cls.id
JOIN fields fds ON stdt.field_id = fds.id
JOIN series srs ON stdt.serie_id = srs.id
JOIN school scl ON stdt.school_id = scl.id
JOIN levels lvl ON stdt.level_id = lvl.id
WHERE stdt.id = 6;

