SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
START TRANSACTION;

--
-- Dumping data for table `admin_roles`
--

INSERT IGNORE INTO `admin_roles` (`id`, `name`, `description`) VALUES
(1, 'Super', 'Super admin privileges');

INSERT IGNORE INTO `permissions` (`id`, `name`) VALUES
(1, 'create_admin_users'),
(2, 'create_roles'),
(3, 'create_pages'),
(4, 'create_posts'),
(5, 'delete_admin_users'),
(6, 'delete_roles'),
(7, 'delete_pages'),
(8, 'delete_posts'),
(9, 'edit_admin_users'),
(10, 'edit_roles'),
(11, 'edit_pages'),
(12, 'edit_posts'),
(13, 'view_admin_users'),
(14, 'view_roles'),
(15, 'view_pages'),
(16, 'view_posts');

INSERT IGNORE INTO `role_permissions` (`admin_roles_id`, `permissions_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16);

COMMIT;