/*
 *  File name:  setup.sql
 *  Function:   to create the initial database schema for the CMPUT 391 project: An Online Image Sharing System
 *              Winter, 2016
 *  Author:     Prof. Li-Yan Yuan
 *  Modified:   Bo Zhou Mar.26 2016
 */

/* drop images_viewed table first. added by Bo Zhou */ 
DROP TABLE images_viewed;
/** original tables by Prof. Yuan **/
DROP TABLE images;
DROP TABLE group_lists;
DROP TABLE groups;
DROP TABLE persons;
DROP TABLE users;

CREATE TABLE users (
   user_name varchar(24),
   password  varchar(24),
   date_registered date,
   primary key(user_name)
);

CREATE TABLE persons (
   user_name  varchar(24),
   first_name varchar(24),
   last_name  varchar(24),
   address    varchar(128),
   email      varchar(128),
   phone      char(10),
   PRIMARY KEY(user_name),
   UNIQUE (email),
   FOREIGN KEY (user_name) REFERENCES users
);


CREATE TABLE groups (
   group_id   int,
   user_name  varchar(24),
   group_name varchar(24),
   date_created date,
   PRIMARY KEY (group_id),
   UNIQUE (user_name, group_name),
   FOREIGN KEY(user_name) REFERENCES users
);

INSERT INTO groups values(1,null,'public', sysdate);
INSERT INTO groups values(2,null,'private',sysdate);

CREATE TABLE group_lists (
   group_id    int,
   friend_id   varchar(24),
   date_added  date,
   notice      varchar(1024),
   PRIMARY KEY(group_id, friend_id),
   FOREIGN KEY(group_id) REFERENCES groups,
   FOREIGN KEY(friend_id) REFERENCES users
);

CREATE TABLE images (
   photo_id    int,
   owner_name  varchar(24),
   permitted   int,
   subject     varchar(128),
   place       varchar(128),
   timing      date,
   description varchar(2048),
   thumbnail   blob,
   photo       blob,
   PRIMARY KEY(photo_id),
   FOREIGN KEY(owner_name) REFERENCES users,
   FOREIGN KEY(permitted) REFERENCES groups
);
/** end original tables **/
/* extra table added by Bo Zhou */
CREATE TABLE images_viewed (
   photo_id int,
   viewer   varchar(24),
   PRIMARY KEY(photo_id, viewer),
   FOREIGN KEY(viewer)  REFERENCES users,
   FOREIGN KEY(photo_id) REFERENCES images
);

/* Initial admin account added by Bo Zhou */
INSERT INTO users VALUES ('admin', 'admin', sysdate );
INSERT INTO persons VALUES ('admin', 'admin_first', 'ADMIN', 'admin address', 'admin@gmail.com', '9999999999');
INSERT INTO group_lists VALUES (1, 'admin', sysdate, 'system added');
INSERT INTO group_lists VALUES (2, 'admin', sysdate, 'system added');
CREATE INDEX descIndex ON images(description) INDEXTYPE IS CTXSYS.CONTEXT; 
CREATE INDEX subjIndex ON images(subject) INDEXTYPE IS CTXSYS.CONTEXT; 
CREATE INDEX placeIndex ON images(place) INDEXTYPE IS CTXSYS.CONTEXT;

commit;

