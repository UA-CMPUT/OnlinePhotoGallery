DROP TABLE images_viewed;
CREATE TABLE images_viewed (
   photo_id int,
   viewer   varchar(24),
   PRIMARY KEY(photo_id),
   UNIQUE (photo_id, viewer),
   FOREIGN KEY(viewer)  REFERENCES users,
   FOREIGN KEY(photo_id) REFERENCES images
);