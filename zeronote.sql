DROP TABLE IF EXISTS "user";
CREATE TABLE 'user' (
    'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    'email' TEXT NOT NULL,
    'password' TEXT NOT NULL,
    'nickname' TEXT,
    'created' INTEGER NOT NULL DEFAULT (strftime('%s','now'))
, 'disabled' BOOLEAN NOT NULL DEFAULT 0);
DROP TABLE IF EXISTS "notebook";
CREATE TABLE 'notebook' (
    'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    'userid' INTEGER NOT NULL,
    'name' TEXT NOT NULL,
    'created' INTEGER DEFAULT (strftime('%s','now'))
);
DROP TABLE IF EXISTS "sharing";
CREATE TABLE 'sharing' (
    'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    'noteid' INTEGER NOT NULL,
    'touserid' INTEGER NOT NULL,
    'permission' INTEGER NOT NULL DEFAULT 0
);
DROP TABLE IF EXISTS "upvote";
CREATE TABLE 'upvote' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'userid' INTEGER NOT NULL, 'noteid' INTEGER NOT NULL);
DROP TABLE IF EXISTS "follow";
CREATE TABLE 'follow' ('id' INTEGER PRIMARY KEY NOT NULL, 'fromuserid' INTEGER NOT NULL, 'touserid' INTEGER NOT NULL);
DROP TABLE IF EXISTS "note";
CREATE TABLE 'note' (
    'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    'userid' INTEGER NOT NULL,
    'nbid' INTEGER NOT NULL DEFAULT 0,
    'title' TEXT,
    'content' TEXT,
    'public' BOOLEAN NOT NULL DEFAULT 0,
    'created'  INTEGER NOT NULL DEFAULT (strftime('%s','now')),
    'updated'  INTEGER NOT NULL DEFAULT (strftime('%s','now'))
, 'tags' TEXT);
DROP INDEX IF EXISTS "idx_notebook_userid";
CREATE INDEX 'idx_notebook_userid' ON "notebook" ("userid");
DROP INDEX IF EXISTS "idx_sharing_noteid";
CREATE INDEX 'idx_sharing_noteid' ON "sharing" ("noteid");
DROP INDEX IF EXISTS "idx_sharing_touserid";
CREATE INDEX 'idx_sharing_touserid' ON "sharing" ("touserid");
DROP INDEX IF EXISTS "idx_upvote_noteid";
CREATE INDEX 'idx_upvote_noteid' ON "upvote" ("noteid");
DROP INDEX IF EXISTS "idx_follow_fromuserid";
CREATE INDEX 'idx_follow_fromuserid' ON "follow" ("fromuserid");
DROP INDEX IF EXISTS "idx_follow_touserid";
CREATE INDEX 'idx_follow_touserid' ON "follow" ("touserid");
DROP INDEX IF EXISTS "idx_note_nbid";
CREATE INDEX 'idx_note_nbid' ON "note" ("nbid");
DROP INDEX IF EXISTS "idx_note_userid";
CREATE INDEX 'idx_note_userid' ON "note" ("userid");
