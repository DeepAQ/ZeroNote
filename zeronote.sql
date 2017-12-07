BEGIN TRANSACTION;

DROP TABLE IF EXISTS "user";
CREATE TABLE 'user' (
    'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    'email' TEXT NOT NULL,
    'password' TEXT NOT NULL,
    'nickname' TEXT,
    'created' INTEGER NOT NULL DEFAULT (strftime('%s','now'))
);

DROP TABLE IF EXISTS "notebook";
CREATE TABLE 'notebook' (
    'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    'userid' INTEGER NOT NULL,
    'name' TEXT NOT NULL,
    'created' INTEGER DEFAULT (strftime('%s','now'))
);

DROP INDEX IF EXISTS "idx_notebook_userid";
CREATE INDEX 'idx_notebook_userid' ON "notebook" ("userid");

DROP TABLE IF EXISTS "note";
CREATE TABLE 'note' (
    'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    'userid' INTEGER NOT NULL,
    'nbid' INTEGER NOT NULL DEFAULT 0,
    'title' TEXT,
    'content' TEXT,
    'public' BOOLEAN NOT NULL DEFAULT 0,
    'upvote' INTEGER NOT NULL DEFAULT 0,
    'created'  INTEGER NOT NULL DEFAULT (strftime('%s','now')),
    'updated'  INTEGER NOT NULL DEFAULT (strftime('%s','now'))
);

DROP INDEX IF EXISTS "idx_note_nbid";
CREATE INDEX 'idx_note_nbid' ON "note" ("nbid");

DROP TABLE IF EXISTS "sharing";
CREATE TABLE 'sharing' (
    'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    'noteid' INTEGER NOT NULL,
    'touserid' INTEGER NOT NULL,
    'permission' INTEGER NOT NULL DEFAULT 0
);

DROP INDEX IF EXISTS "idx_sharing_noteid";
CREATE INDEX 'idx_sharing_noteid' ON "sharing" ("noteid");
DROP INDEX IF EXISTS "idx_sharing_touserid";
CREATE INDEX 'idx_sharing_touserid' ON "sharing" ("touserid");

COMMIT;
