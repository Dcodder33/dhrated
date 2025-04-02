-- Create the database
CREATE DATABASE IF NOT EXISTS reviews_db;
USE reviews_db;

-- Users table
CREATE TABLE `users` (
  `id` varchar(20) NOT NULL PRIMARY KEY,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL UNIQUE,
  `password` varchar(100) NOT NULL,
  `image` varchar(100) DEFAULT '',
  `status` enum('active', 'banned') DEFAULT 'active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin table
CREATE TABLE `admin` (
  `id` varchar(20) NOT NULL PRIMARY KEY,
  `name` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Posts table
CREATE TABLE `posts` (
  `id` varchar(20) NOT NULL PRIMARY KEY,
  `user_id` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `restaurant` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(100) DEFAULT '',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reviews table
CREATE TABLE `reviews` (
  `id` varchar(20) NOT NULL PRIMARY KEY,
  `post_id` varchar(20) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `rating` int(1) NOT NULL CHECK (rating BETWEEN 1 AND 5),
  `title` varchar(100) NOT NULL,
  `description` text,
  `date` timestamp DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create indexes for better performance
CREATE INDEX idx_posts_user ON posts(user_id);
CREATE INDEX idx_reviews_post ON reviews(post_id);
CREATE INDEX idx_reviews_user ON reviews(user_id);
CREATE INDEX idx_users_email ON users(email);

-- Insert default admin account
INSERT INTO `admin` (`id`, `name`, `password`) VALUES
('admin123', 'admin', '$2y$10$rS/SkIyUQqLPCekHs5RWout0b0lxK7q9gBHiJBWE7pxzH.w2cmCNi');
-- Default password is 'admin123'

-- Create triggers for data integrity
DELIMITER //

-- Trigger to validate rating range
CREATE TRIGGER before_review_insert 
BEFORE INSERT ON reviews
FOR EACH ROW
BEGIN 
    IF NEW.rating < 1 OR NEW.rating > 5 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Rating must be between 1 and 5';
    END IF;
END//

-- Trigger to prevent banned users from posting
CREATE TRIGGER before_post_insert
BEFORE INSERT ON posts
FOR EACH ROW
BEGIN
    DECLARE user_status VARCHAR(10);
    SELECT status INTO user_status FROM users WHERE id = NEW.user_id;
    IF user_status = 'banned' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Banned users cannot create posts';
    END IF;
END//

DELIMITER ;

-- Create views for common queries
CREATE VIEW vw_post_ratings AS
SELECT 
    p.id,
    p.title,
    p.restaurant,
    p.price,
    COUNT(r.id) as total_reviews,
    COALESCE(AVG(r.rating), 0) as avg_rating
FROM posts p
LEFT JOIN reviews r ON p.id = r.post_id
GROUP BY p.id;

CREATE VIEW vw_user_activity AS
SELECT 
    u.id,
    u.name,
    COUNT(DISTINCT p.id) as total_posts,
    COUNT(DISTINCT r.id) as total_reviews,
    COALESCE(AVG(r.rating), 0) as avg_rating_given
FROM users u
LEFT JOIN posts p ON u.id = p.user_id
LEFT JOIN reviews r ON u.id = r.user_id
GROUP BY u.id;