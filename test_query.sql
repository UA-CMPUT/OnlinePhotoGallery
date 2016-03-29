INSERT INTO persons VALUES ('admin', 'f_admin', 'A_admin', '1111st edmonton', 'admin@gmail.com', '00000000');

/* Other user's photos */
SELECT i.photo_id FROM images i WHERE 'Bb' IN (SELECT gl.friend_id FROM group_lists gl WHERE gl.group_id = i.permitted);
/* The user's own photos */
SELECT photo_id FROM images WHERE owner_name = 'Bb';
/**/
SELECT group_id, count(*) AS numberOfviewer FROM group_lists GROUP BY group_id;


SELECT i.*, g.group_name, iv.viewer, count() AS numberOfView FROM images i, groups g, images_viewed iv WHERE i.owner_name = 'Aa' AND i.permitted = g.group_id AND i.photo_id = iv.photo_id GROUP BY i.photo_id;

SELECT count(*) AS numberOfviewer FROM images_viewed WHERE photo_id ='3' GROUP BY photo_id
