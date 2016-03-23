CREATE TABLE images_viewed (
   photo_id    int,
   view_count  int,
   PRIMARY KEY(photo_id),
   FOREIGN KEY(photo_id) REFERENCES images
);