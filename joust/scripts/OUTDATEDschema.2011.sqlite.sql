CREATE TABLE alliances(
  id INTEGER PRIMARY KEY NOT NULL -- alliance id
, team1 VARCHAR(10) -- alliance match partner
, team2 VARCHAR(10) -- alliance match partner
, game1 NUMERIC DEFAULT 0 NOT NULL
, game2 NUMERIC DEFAULT 0 NOT NULL
, game3 NUMERIC DEFAULT 0 NOT NULL
);
CREATE TABLE awards(
  id     INTEGER PRIMARY KEY NOT NULL
, name 	 VARCHAR(255)
, type   VARCHAR(255) -- T= Trophies, C = certificate
, team   VARCHAR(10)
, notes  TEXT
);
CREATE TABLE brackets (
  id      INTEGER PRIMARY KEY NOT NULL -- should be a power of 2
, status  INTEGER DEFAULT 0 NOT NULL
, log     INTEGER DEFAULT 0 NOT NULL
, file VARCHAR(255)
);
CREATE TABLE games(
  id      INTEGER PRIMARY KEY NOT NULL -- game id
, b
, a.org as org
, a.p1doc as p1doc , a.p2doc as p2doc , a.p3doc as p3doc, a.p4doc as p4doc
, a.p1docrank as p1docrank
, a.p2docrank as p2docrank
, a.p3docrank as p3docrank
, a.p4docrank as p4docrank
, a.teamcount as teamcount
, a.docscore  as docscore
, (select count(1)  
     from raw_standings_view b 
    where b.docscore > a.docscore    
    )+1 as docrank
, a.seed1 as seed1, a.seed2 as seed2, a.seed3 as seed3
, a.seedavg as seedavg
, (select count(1)  
     from raw_standings_view b 
    where b.seedavg > a.seedavg
   ) +1 as seedrank
, a.seedmax as seedmax
, (select max(seedmax) from raw_standings_view) as tmtseedmax -- max seed score of any team
, a.demax as demax
,(select count(1)  
     from raw_standings_view b 
    where b.demax > a.demax
   ) +1 as derank
FROM raw_standings_view as a;
CREATE INDEX "alliancesid" on alliances(id);
CREATE INDEX awardsid on awards(id);
CREATE INDEX "bracketsid" on brackets(id);
CREATE INDEX "gamesid" on games(id);
CREATE INDEX "teamscode" ON teams(code);
