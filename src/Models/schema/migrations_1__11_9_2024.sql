SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
START TRANSACTION;

CREATE TABLE IF NOT EXISTS admin_roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS role_permissions (
    admin_roles_id INT NOT NULL,
    permissions_id INT NOT NULL,
    PRIMARY KEY (admin_roles_id, permissions_id),
    FOREIGN KEY (admin_roles_id) REFERENCES admin_roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permissions_id) REFERENCES permissions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    image VARCHAR(200),
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    admin_roles_id INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP,
    FOREIGN KEY (admin_roles_id) REFERENCES admin_roles(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS admin_activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_users_id INT,
    target_id INT,
    target_type VARCHAR(50),
    action TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_users_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    KEY target_id (target_id),
    KEY target_type (target_type)
);

CREATE TABLE IF NOT EXISTS password_reset_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    target_id INT NOT NULL,
    target_type VARCHAR(100) NOT NULL,
    token VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY target_id (target_id),
    KEY target_type (target_type),
    KEY token (token)
);

CREATE TABLE IF NOT EXISTS options (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    value TEXT,
    preload BOOLEAN DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS terms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    terms_id INT DEFAULT NULL,
    term_type ENUM('category', 'tag') DEFAULT 'category',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (terms_id) REFERENCES terms(id) ON DELETE SET NULL,
    KEY term_type (term_type)
);

CREATE TABLE IF NOT EXISTS posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    thumbnail VARCHAR(255),
    content TEXT,
    status ENUM('published', 'draft') DEFAULT 'draft',
    post_type ENUM('page', 'blog', 'custom') DEFAULT 'page',
    seo_settings TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY post_type (post_type),
    KEY title (title)
);

CREATE TABLE IF NOT EXISTS posts_terms (
    posts_id INT NOT NULL,
    terms_id INT NOT NULL,
    PRIMARY KEY (posts_id, terms_id),
    FOREIGN KEY (posts_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (terms_id) REFERENCES terms(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS posts_meta (
    posts_id INT NOT NULL,
    meta_key VARCHAR(100) NOT NULL,
    meta_value TEXT NOT NULL,
    PRIMARY KEY (posts_id, meta_key),
    FOREIGN KEY (posts_id) REFERENCES posts(id) ON DELETE CASCADE
);

COMMIT;