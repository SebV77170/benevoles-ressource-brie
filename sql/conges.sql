CREATE TABLE `conges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_user` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uuid_user` (`uuid_user`),
  CONSTRAINT `conges_ibfk_1` FOREIGN KEY (`uuid_user`) REFERENCES `users` (`uuid_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;