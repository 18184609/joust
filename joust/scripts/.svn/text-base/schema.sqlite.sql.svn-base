-- NOTES
-- sqlite ignores foreign key constraints, the ones below are just to make me feel better


BEGIN;

CREATE TABLE brackets (
  id      INTGER PRIMARY KEY NOT NULL -- should be a power of 2
, status  INTEGER DEFAULT 0 NOT NULL
, log     INTEGER DEFAULT 0 NOT NULL
, file VARCHAR(255)
);
CREATE INDEX "bracketsid" on brackets(id);

CREATE TABLE teams(
  code  VARCHAR(10) PRIMARY KEY NOT NULL
, org   VARCHAR(255)
, email VARCHAR(255)
, status INTEGER DEFAULT 0 NOT NULL -- 0=absent, 1=present
, p1doc  NUMERIC DEFAULT 0 NOT NULL
, p2doc  NUMERIC DEFAULT 0 NOT NULL
, p3doc  NUMERIC DEFAULT 0 NOT NULL
, p4doc  NUMERIC DEFAULT 0 NOT NULL -- p4doc is onsite doc   
, seed1	 NUMERIC DEFAULT -2 NOT NULL
, seed2	 NUMERIC DEFAULT -2 NOT NULL
, seed3	 NUMERIC DEFAULT -2 NOT NULL
);
CREATE INDEX "teamscode" ON teams(code);

CREATE TABLE games(
  id      INTEGER PRIMARY KEY NOT NULL -- game id
, bracket INTEGER -- bracket id ( currently only 64 )
, round   INTEGER -- round of the tournament
, consolation INTEGER DEFAULT 0 -- 0 for win game, 1, for c1 game, 2 for c2 game
, final INTEGER DEFAULT 0 -- 0 for most games, 1 for first final, 2 for second final
, sidea	  VARCHAR(10)  -- team on side a
, sideb	  VARCHAR(10)  -- team on side b
, wgame	  INTEGER  -- winner goes to game
, wside   CHAR(1)  -- winner goes to side
, lgame   INTEGER  -- loser goes to game
, lside   CHAR(1)  -- loser goes to side
, ondeck VARCHAR(255)
, victor  VARCHAR(10)  -- winner
, FOREIGN KEY (sidea) REFERENCES teams(code) -- sqlite doesn't enforce foreign key constraints
, FOREIGN KEY (sideb) REFERENCES teams(code)
, FOREIGN KEY (wgame) REFERENCES games(id)
, FOREIGN KEY (lgame) REFERENCES games(id)
, CONSTRAINT game_lgame_lside UNIQUE (lgame,lside)
, CONSTRAINT game_wgame_wside UNIQUE (wgame,wside)
);
CREATE INDEX "gamesid" on games(id);




CREATE TABLE awards(
  id     INTEGER PRIMARY KEY NOT NULL
, name 	 VARCHAR(255)
, type   VARCHAR(255) -- T= Trophies, C = certificate
, team   VARCHAR(10)
, notes  TEXT
);
CREATE INDEX awardsid on awards(id);


-- TODO: rethink how NULLs are being handled in the summations 
CREATE VIEW raw_standings_view
AS SELECT 
  a.code as code
, a.org as org
, a.p1doc as p1doc , a.p2doc as p2doc , a.p3doc as p3doc, a.p4doc as p4doc
, (select count(1)  
     from teams b 
    where b.p1doc > a.p1doc
   ) +1 as p1docrank 
, (select count(1)  
     from teams b 
    where b.p2doc > a.p2doc
   ) +1 as p2docrank
, (select count(1)  
     from teams b 
    where b.p3doc > a.p3doc
   ) +1 as p3docrank
, (select count(1)  
     from teams b 
    where b.p4doc > a.p4doc
   ) +1 as p4docrank
, (select count(1) from teams) as teamcount  
, a.seed1 as seed1, a.seed2 as seed2, a.seed3 as seed3
, coalesce ( (((a.seed1 + a.seed2 + a.seed3) - min( a.seed1, a.seed2, a.seed3) )/2.0), 0) as seedavg  -- average of 2 highest runs 
, coalesce ( (select max( a.seed1, a.seed2, a.seed3)), 0) as seedmax -- maximum seed score
, coalesce ( (select max(round||consolation||final) from games where sidea = a.code or sideb=a.code), 0 ) as demax  
FROM teams as a;

-- NOTE: ranking first, then weighting, means we don't have to balance the documentation periods against one another
CREATE VIEW standings_view
AS SELECT  a.code as code
, a.org as org
, a.p1doc as p1doc , a.p2doc as p2doc , a.p3doc as p3doc, a.p4doc as p4doc
, a.p1docrank as p1docrank
, a.p2docrank as p2docrank
, a.p3docrank as p3docrank
, a.p4docrank as p4docrank
, a.teamcount as teamcount
,   (0.2  * (a.teamcount - a.p1docrank +1) / a.teamcount)  
  + (0.23 * (a.teamcount - a.p2docrank +1) / a.teamcount)   
  + (0.27 * (a.teamcount - a.p3docrank +1) / a.teamcount)   
  + (0.3  * (a.teamcount - a.p4docrank +1) / a.teamcount) as docscore -- docscore as defined in 2010
, (select count(1)  
     from raw_standings_view b 
    where (0.2  * (b.teamcount - b.p1docrank +1) / a.teamcount)  
    	+ (0.23 * (b.teamcount - b.p2docrank +1) / a.teamcount)   
        + (0.27 * (b.teamcount - b.p3docrank +1) / a.teamcount)   
        + (0.3 *  (b.teamcount - b.p4docrank +1) / a.teamcount) 
  	< (0.2  * (a.teamcount - a.p1docrank +1) / a.teamcount)  
  	+ (0.23 * (a.teamcount - a.p2docrank +1) / a.teamcount)   
	+ (0.27 * (a.teamcount - a.p3docrank +1) / a.teamcount)   
        + (0.3  * (a.teamcount - a.p4docrank +1) / a.teamcount) ) +1 as docrank
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
     
CREATE VIEW overall_view 
AS SELECT code
, (teamcount - derank +1.0)/teamcount  as descore
, .5 * ( teamcount - seedrank +1)/teamcount + .5 *(seedavg/tmtseedmax) as seedscore
, docscore as docscore  
from standings_view ;








COMMIT ;


-- works for sqlite but fails for Zend + sqlite
-- grrrrrrrrrr

-- CREATE VIEW standings_view2
-- AS SELECT  a.code as code
-- , a.org as org
-- , a.p1doc as p1doc , a.p2doc as p2doc , a.p3doc as p3doc, a.p4doc as p4doc
-- , a.docscore as docscore
-- , (select count(1)  
--      from raw_standings_view b 
--     where b.docscore >a.docscore
--    ) +1 as docrank
-- , a.seed1 as seed1, a.seed2 as seed2, a.seed3 as seed3
-- , a.seedavg as seedavg
-- , (select count(1)  
--      from raw_standings_view b 
--     where b.seedavg > a.seedavg
--    ) +1 as seedrank
-- , a.demax as demax
-- ,(select count(1)  
--      from raw_standings_view b 
--     where b.demax > a.demax
--    ) +1 as derank
-- , (select count(1)
--      from ( select distinct b.demax
--              from raw_standings_view b 
-- 	     where b.demax >a.demax)) +1 as delevel 
-- FROM raw_standings_view as a;



  