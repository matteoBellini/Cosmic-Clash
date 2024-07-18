DROP SCHEMA IF EXISTS cosmic_clash;
CREATE SCHEMA IF NOT EXISTS cosmic_clash DEFAULT CHARSET utf8;

-- -----------------------
-- CREAZIONE TABELLE
-- -----------------------
DROP TABLE IF EXISTS cosmic_clash.Utente;
CREATE TABLE cosmic_clash.Utente(
    Username varchar(255) not null,
    Password varchar(255) not null,
    Nome varchar(255) not null,
    Cognome varchar(255) not null,
    Email varchar(255) not null,
    ImmagineProfilo varchar(255) default null,
    primary key (Username)
) ENGINE = InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS cosmic_clash.Classifica;
CREATE TABLE cosmic_clash.Classifica(
    Username varchar(255) not null,
    DataRecord date not null,
    Punteggio int unsigned not null,
    primary key (Username),
    foreign key (Username) references Utente(Username)
) ENGINE = InnoDB DEFAULT CHARSET=latin1;


-- Inserimento di un punteggio in classifica: viene 
-- effettuato solo se migliore dell'ultimo punteggio registrato oppure è la prima partita dell'utente
DROP PROCEDURE IF EXISTS cosmic_clash.inserimentoClassifica;
DELIMITER $$
CREATE PROCEDURE cosmic_clash.inserimentoClassifica(IN _Username VARCHAR(255), IN _Punteggio INT)
BEGIN
    DECLARE uname VARCHAR(255) DEFAULT '';
    DECLARE punt INTEGER DEFAULT 0;

    SELECT Username, Punteggio INTO uname, punt
    FROM Classifica
    WHERE Username = _Username;

    IF uname <> '' THEN
        IF _Punteggio > punt THEN
            UPDATE Classifica
            SET Punteggio = _Punteggio, DataRecord = CURRENT_DATE
            WHERE Username = _Username;
        END IF;
    ELSE
        INSERT INTO Classifica VALUES (_Username, CURRENT_DATE, _Punteggio);
    END IF;
END $$
DELIMITER ;

INSERT INTO cosmic_clash.`utente` VALUES ('AlessandroBianchi','$2y$10$/1FoBsF6EqKnWpOgNaqJa.ZZfeQhPvUteqTuEHk/ji120cfuIZDAC','Alessandro','Bianchi','a.bianchi@gmail.com','5'),('ChiaraEsposito','$2y$10$I9AWudpQL0xNPzGjvsGwruBQU9dSf95DhkNgbrQ4bl2IORZvGlh4i','Chiara','Esposito','c.esposito@gmail.com','29'),('FrancescaGallo','$2y$10$xQED4h4L1/BpjvMuUQeSre2pfP35WYfj96feqaPY2oQY91Avu28Eu','Francesca','Gallo','f.gallo@gmail.com','7'),('FrancescoFerrari','$2y$10$6QN/zAvymSyEnOWkOeCQtOfEMAKsmPH6v1SB9Jfrob2AIVuhrwcrC','Francesco','Ferrari','f.ferrari@gmail.com','16'),('GiuliaRusso','$2y$10$zNzzIuDmq2Jxrml5PU.OsOXalwjzgHtVzOODeJm.eX4YPWKcIMoty','Giulia','Russo','g.russo@gmail.com','19'),('LeonardoBianchi','$2y$10$I7gr4GIzPvc0IK.SJ/pfWO5bD7V3F2ZHDj0EDXmME/q0E3lgmfvqy','Leonardo','Bianchi','l.bianchi@gmail.com','2'),('LorenzoColombo','$2y$10$lqOP6.mTIFTHHU3xGHqEqeUnlkrw8DjY1JzjJMpDIzsgTd0Otu5Eq','Lorenzo','Colombo','l.colombo@gmail.com','8'),('MarcoDeLuca','$2y$10$9kMi6lZ2ZWAggG83sfaP0uhl8pao5QqP77M.5F7et5UeAewb1cBFG','Marco','De Luca','m.deluca@gmail.com','28'),('MarioRossi','$2y$10$jX/SG4MMk5PXxZnzv/qViuMIZI2X0xyokda5WbadCVwHN59/UGyq2','Mario','Rossi','m.rossi@gmail.com','16'),('MatteoBellini','$2y$10$yJQ06rxoRXAGmKRvxBih7e4CIhqD4pxesIAViZIvsFthbYO6q3j3C','Matteo','Bellini','m.bellini@gmail.com','16');

INSERT INTO cosmic_clash.`classifica` VALUES ('AlessandroBianchi','2024-07-13',155),('ChiaraEsposito','2024-07-13',100),('FrancescaGallo','2024-07-12',150),('FrancescoFerrari','2024-07-13',150),('GiuliaRusso','2024-07-13',5),('LeonardoBianchi','2024-07-13',161),('LorenzoColombo','2024-07-13',45),('MarcoDeLuca','2024-07-13',87),('MarioRossi','2024-07-13',175),('MatteoBellini','2024-07-13',463);
