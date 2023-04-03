PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS "employees"
(
    [EmployeeId] INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    [LastName] NVARCHAR(20)  NOT NULL,
    [FirstName] NVARCHAR(20)  NOT NULL,
    [Title] NVARCHAR(30),
    [ReportsTo] INTEGER,
    [BirthDate] DATETIME,
    [HireDate] DATETIME,
    [Address] NVARCHAR(70),
    [City] NVARCHAR(40),
    [State] NVARCHAR(40),
    [Country] NVARCHAR(40),
    [PostalCode] NVARCHAR(10),
    [Phone] NVARCHAR(24),
    [Fax] NVARCHAR(24),
    [Email] NVARCHAR(60),
    FOREIGN KEY ([ReportsTo]) REFERENCES "employees" ([EmployeeId]) 
		ON DELETE NO ACTION ON UPDATE NO ACTION
);
INSERT INTO "employees" VALUES(1,'Adams','Andrew','General Manager',NULL,'1962-02-18 00:00:00','2002-08-14 00:00:00','11120 Jasper Ave NW','Edmonton','AB','Canada','T5K 2N1','+1 (780) 428-9482','+1 (780) 428-3457','andrew@chinookcorp.com');
INSERT INTO "employees" VALUES(2,'Edwards','Nancy','Sales Manager',1,'1958-12-08 00:00:00','2002-05-01 00:00:00','825 8 Ave SW','Calgary','AB','Canada','T2P 2T3','+1 (403) 262-3443','+1 (403) 262-3322','nancy@chinookcorp.com');
INSERT INTO "employees" VALUES(3,'Peacock','Jane','Sales Support Agent',2,'1973-08-29 00:00:00','2002-04-01 00:00:00','1111 6 Ave SW','Calgary','AB','Canada','T2P 5M5','+1 (403) 262-3443','+1 (403) 262-6712','jane@chinookcorp.com');
INSERT INTO "employees" VALUES(4,'Park','Margaret','Sales Support Agent',2,'1947-09-19 00:00:00','2003-05-03 00:00:00','683 10 Street SW','Calgary','AB','Canada','T2P 5G3','+1 (403) 263-4423','+1 (403) 263-4289','margaret@chinookcorp.com');
INSERT INTO "employees" VALUES(5,'Johnson','Steve','Sales Support Agent',2,'1965-03-03 00:00:00','2003-10-17 00:00:00','7727B 41 Ave','Calgary','AB','Canada','T3B 1Y7','1 (780) 836-9987','1 (780) 836-9543','steve@chinookcorp.com');
INSERT INTO "employees" VALUES(6,'Mitchell','Michael','IT Manager',1,'1973-07-01 00:00:00','2003-10-17 00:00:00','5827 Bowness Road NW','Calgary','AB','Canada','T3B 0C5','+1 (403) 246-9887','+1 (403) 246-9899','michael@chinookcorp.com');
INSERT INTO "employees" VALUES(7,'King','Robert','IT Staff',6,'1970-05-29 00:00:00','2004-01-02 00:00:00','590 Columbia Boulevard West','Lethbridge','AB','Canada','T1K 5N8','+1 (403) 456-9986','+1 (403) 456-8485','robert@chinookcorp.com');
INSERT INTO "employees" VALUES(8,'Callahan','Laura','IT Staff',6,'1968-01-09 00:00:00','2004-03-04 00:00:00','923 7 ST NW','Lethbridge','AB','Canada','T1H 1Y8','+1 (403) 467-3351','+1 (403) 467-8772','laura@chinookcorp.com');
CREATE INDEX [IFK_EmployeeReportsTo] ON "employees" ([ReportsTo]);
COMMIT;
