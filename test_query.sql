INSERT INTO persons VALUES ('admin', 'f_admin', 'A_admin', '1111st edmonton', 'admin@gmail.com', '00000000');

/* Other user's photos */
SELECT i.photo_id FROM images i WHERE 'Bb' IN (SELECT gl.friend_id FROM group_lists gl WHERE gl.group_id = i.permitted);
/* The user's own photos */
SELECT photo_id FROM images WHERE owner_name = 'Bb';