DROP TABLE IF EXISTS `#__profiles_list`;

DELETE FROM `#__content_types` WHERE (type_alias LIKE 'com_profiles.%');