-- ============================================================================
-- CYN TOURISM MANAGEMENT SYSTEM - COMPLETE DATABASE SCHEMA
-- Version: 2.0.0
-- Date: 2024-02-08
-- ============================================================================
-- This SQL file creates all tables required for the CYN Tourism system.
-- Run this file in your MySQL database to set up the complete schema.
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================================
-- 1. USERS TABLE - Enhanced user management with security features
-- ============================================================================

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','operator','viewer') DEFAULT 'viewer',
  `status` enum('active','inactive','suspended','pending') DEFAULT 'pending',
  `email_verified` tinyint(1) DEFAULT 0,
  `email_verified_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `failed_login_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `password_changed_at` datetime DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `remember_token_expires` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `two_factor_secret` varchar(255) DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `profile_image` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role` (`role`),
  KEY `status` (`status`),
  KEY `remember_token` (`remember_token`),
  KEY `reset_token` (`reset_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: Admin@123 - CHANGE IMMEDIATELY!)
INSERT INTO `users` (`first_name`, `last_name`, `email`, `password`, `role`, `status`, `email_verified`, `email_verified_at`, `created_at`) VALUES
('System', 'Administrator', 'admin@cyntourism.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE `email` = `email`;

-- ============================================================================
-- 2. VOUCHERS TABLE - Transfer vouchers with full details
-- ============================================================================

CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `voucher_no` varchar(60) NOT NULL,
  `company_name` varchar(120) NOT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL,
  `hotel_name` varchar(120) NOT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `dropoff_location` varchar(255) NOT NULL,
  `pickup_date` date NOT NULL,
  `pickup_time` time NOT NULL,
  `return_date` date DEFAULT NULL,
  `return_time` time DEFAULT NULL,
  `transfer_type` enum('one_way','round_trip','multi_stop') DEFAULT 'one_way',
  `total_pax` int(11) NOT NULL DEFAULT 0,
  `passengers` text DEFAULT NULL COMMENT 'JSON array of passenger names',
  `flight_number` varchar(50) DEFAULT NULL,
  `flight_arrival_time` time DEFAULT NULL,
  `vehicle_id` int(11) UNSIGNED DEFAULT NULL,
  `driver_id` int(11) UNSIGNED DEFAULT NULL,
  `guide_id` int(11) UNSIGNED DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `currency` varchar(3) DEFAULT 'USD',
  `status` enum('pending','confirmed','completed','cancelled','no_show') DEFAULT 'pending',
  `payment_status` enum('unpaid','partial','paid','refunded') DEFAULT 'unpaid',
  `notes` text DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `updated_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `voucher_no` (`voucher_no`),
  KEY `company_name` (`company_name`),
  KEY `pickup_date` (`pickup_date`),
  KEY `status` (`status`),
  KEY `created_by` (`created_by`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `driver_id` (`driver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. INVOICES TABLE - Invoice management
-- ============================================================================

CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(60) NOT NULL,
  `company_name` varchar(120) NOT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_rate` decimal(5,2) DEFAULT 0.00,
  `tax_amount` decimal(12,2) DEFAULT 0.00,
  `discount` decimal(12,2) DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(12,2) DEFAULT 0.00,
  `currency` varchar(3) DEFAULT 'USD',
  `status` enum('draft','sent','paid','overdue','cancelled','partial') DEFAULT 'draft',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `terms` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `sent_by` int(11) UNSIGNED DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_no` (`invoice_no`),
  KEY `company_name` (`company_name`),
  KEY `status` (`status`),
  KEY `invoice_date` (`invoice_date`),
  KEY `due_date` (`due_date`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. INVOICE ITEMS TABLE - Line items for invoices
-- ============================================================================

CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) UNSIGNED NOT NULL,
  `item_type` enum('voucher','tour','service','other') DEFAULT 'voucher',
  `item_id` int(11) UNSIGNED DEFAULT NULL,
  `description` text NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `item_type` (`item_type`),
  FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. PARTNERS TABLE - Partner/Company management
-- ============================================================================

CREATE TABLE IF NOT EXISTS `partners` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_name` varchar(120) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  `commission_rate` decimal(5,2) DEFAULT 0.00,
  `credit_limit` decimal(12,2) DEFAULT 0.00,
  `balance` decimal(12,2) DEFAULT 0.00,
  `payment_terms` int(11) DEFAULT 30 COMMENT 'Payment terms in days',
  `partner_type` enum('agency','hotel','supplier','other') DEFAULT 'agency',
  `status` enum('active','inactive','suspended','blacklisted') DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `contract_file` varchar(255) DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `company_name` (`company_name`),
  KEY `status` (`status`),
  KEY `partner_type` (`partner_type`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. VEHICLES TABLE - Vehicle fleet management
-- ============================================================================

CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `plate_number` varchar(20) NOT NULL,
  `make` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(4) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 4,
  `luggage_capacity` int(11) DEFAULT 2,
  `vehicle_type` enum('sedan','suv','van','minibus','bus','luxury','other') DEFAULT 'sedan',
  `fuel_type` enum('gasoline','diesel','electric','hybrid') DEFAULT 'gasoline',
  `insurance_expiry` date DEFAULT NULL,
  `registration_expiry` date DEFAULT NULL,
  `mileage` int(11) DEFAULT 0,
  `status` enum('available','in_use','maintenance','out_of_service','retired') DEFAULT 'available',
  `last_maintenance` date DEFAULT NULL,
  `next_maintenance` date DEFAULT NULL,
  `driver_id` int(11) UNSIGNED DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `plate_number` (`plate_number`),
  KEY `status` (`status`),
  KEY `vehicle_type` (`vehicle_type`),
  KEY `driver_id` (`driver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. DRIVERS TABLE - Driver management
-- ============================================================================

CREATE TABLE IF NOT EXISTS `drivers` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `full_name` varchar(200) GENERATED ALWAYS AS (concat(`first_name`,' ',`last_name`)) STORED,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `license_no` varchar(50) NOT NULL,
  `license_expiry` date NOT NULL,
  `license_type` varchar(20) DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `status` enum('active','inactive','on_leave','suspended','terminated') DEFAULT 'active',
  `rating` decimal(2,1) DEFAULT 5.0,
  `total_trips` int(11) DEFAULT 0,
  `languages` varchar(255) DEFAULT NULL COMMENT 'Comma-separated languages',
  `photo` varchar(255) DEFAULT NULL,
  `documents` text DEFAULT NULL COMMENT 'JSON array of document paths',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `license_no` (`license_no`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`),
  KEY `full_name` (`full_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. TOUR GUIDES TABLE - Tour guide management
-- ============================================================================

CREATE TABLE IF NOT EXISTS `tour_guides` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `full_name` varchar(200) GENERATED ALWAYS AS (concat(`first_name`,' ',`last_name`)) STORED,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `license_no` varchar(50) DEFAULT NULL,
  `license_expiry` date DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `languages` varchar(255) NOT NULL COMMENT 'Comma-separated languages',
  `specializations` varchar(255) DEFAULT NULL COMMENT 'Comma-separated specializations',
  `experience_years` int(11) DEFAULT 0,
  `daily_rate` decimal(10,2) DEFAULT 0.00,
  `currency` varchar(3) DEFAULT 'USD',
  `hire_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `status` enum('active','inactive','on_leave','suspended','terminated') DEFAULT 'active',
  `rating` decimal(2,1) DEFAULT 5.0,
  `total_tours` int(11) DEFAULT 0,
  `photo` varchar(255) DEFAULT NULL,
  `documents` text DEFAULT NULL COMMENT 'JSON array of document paths',
  `bio` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `license_no` (`license_no`),
  KEY `status` (`status`),
  KEY `full_name` (`full_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 9. NOTIFICATIONS TABLE - User notifications system
-- ============================================================================

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error','system') DEFAULT 'info',
  `category` enum('general','booking','invoice','system','reminder','alert') DEFAULT 'general',
  `related_id` int(11) UNSIGNED DEFAULT NULL,
  `related_type` varchar(50) DEFAULT NULL,
  `action_url` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `sent_email` tinyint(1) DEFAULT 0,
  `sent_push` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_read` (`is_read`),
  KEY `type` (`type`),
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 10. ACTIVITY LOGS TABLE - Security audit trail
-- ============================================================================

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) UNSIGNED DEFAULT NULL,
  `old_values` text DEFAULT NULL COMMENT 'JSON of old values',
  `new_values` text DEFAULT NULL COMMENT 'JSON of new values',
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `request_method` varchar(10) DEFAULT NULL,
  `request_url` varchar(500) DEFAULT NULL,
  `severity` enum('debug','info','warning','error','critical') DEFAULT 'info',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `entity_type` (`entity_type`),
  KEY `ip_address` (`ip_address`),
  KEY `created_at` (`created_at`),
  KEY `severity` (`severity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 11. LOGIN ATTEMPTS TABLE - Brute force protection
-- ============================================================================

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempts` int(11) DEFAULT 1,
  `last_attempt` timestamp NOT NULL DEFAULT current_timestamp(),
  `locked_until` datetime DEFAULT NULL,
  `success` tinyint(1) DEFAULT 0,
  `user_agent` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `ip_address` (`ip_address`),
  KEY `last_attempt` (`last_attempt`),
  KEY `locked_until` (`locked_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 12. SETTINGS TABLE - System configuration
-- ============================================================================

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) DEFAULT 'general',
  `data_type` enum('string','integer','boolean','json','array') DEFAULT 'string',
  `is_encrypted` tinyint(1) DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `setting_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `data_type`, `description`) VALUES
('site_name', 'CYN Tourism', 'general', 'string', 'Website name'),
('site_email', 'info@cyntourism.com', 'general', 'string', 'Default site email'),
('timezone', 'Europe/Istanbul', 'general', 'string', 'System timezone'),
('date_format', 'd/m/Y', 'general', 'string', 'Default date format'),
('time_format', 'H:i', 'general', 'string', 'Default time format'),
('currency', 'USD', 'general', 'string', 'Default currency'),
('max_login_attempts', '5', 'security', 'integer', 'Maximum failed login attempts before lockout'),
('lockout_duration', '30', 'security', 'integer', 'Account lockout duration in minutes'),
('password_min_length', '8', 'security', 'integer', 'Minimum password length'),
('password_require_uppercase', '1', 'security', 'boolean', 'Require uppercase letters in password'),
('password_require_numbers', '1', 'security', 'boolean', 'Require numbers in password'),
('password_require_special', '1', 'security', 'boolean', 'Require special characters in password'),
('session_timeout', '120', 'security', 'integer', 'Session timeout in minutes'),
('enable_2fa', '0', 'security', 'boolean', 'Enable two-factor authentication'),
('maintenance_mode', '0', 'system', 'boolean', 'Enable maintenance mode'),
('debug_mode', '0', 'system', 'boolean', 'Enable debug mode')
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;

-- ============================================================================
-- 13. PASSWORD RESET TOKENS TABLE - Secure password reset
-- ============================================================================

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `used_at` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `email` (`email`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 14. API TOKENS TABLE - API authentication
-- ============================================================================

CREATE TABLE IF NOT EXISTS `api_tokens` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `token_name` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `abilities` text DEFAULT NULL COMMENT 'JSON array of allowed abilities',
  `last_used_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `revoked` tinyint(1) DEFAULT 0,
  `revoked_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`),
  KEY `expires_at` (`expires_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 15. API KEYS TABLE - API key authentication
-- ============================================================================

CREATE TABLE IF NOT EXISTS `api_keys` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_key` (`api_key`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 16. EMAIL CONFIG TABLE - Email configuration
-- ============================================================================

CREATE TABLE IF NOT EXISTS `email_config` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_port` int(11) DEFAULT 587,
  `smtp_username` varchar(255) DEFAULT NULL,
  `smtp_password` varchar(255) DEFAULT NULL,
  `from_email` varchar(255) DEFAULT NULL,
  `from_name` varchar(100) DEFAULT NULL,
  `enable_notifications` tinyint(1) DEFAULT 1,
  `enable_reminders` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 17. REMINDER LOGS TABLE - Reminder tracking
-- ============================================================================

CREATE TABLE IF NOT EXISTS `reminder_logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `voucher_id` int(11) UNSIGNED NOT NULL,
  `reminder_type` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `voucher_id` (`voucher_id`),
  FOREIGN KEY (`voucher_id`) REFERENCES `vouchers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 18. TOUR ASSIGNMENTS TABLE - Link tours with guides and vehicles
-- ============================================================================

CREATE TABLE IF NOT EXISTS `tour_assignments` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `guide_id` int(11) UNSIGNED DEFAULT NULL,
  `vehicle_id` int(11) UNSIGNED DEFAULT NULL,
  `driver_id` int(11) UNSIGNED DEFAULT NULL,
  `assignment_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  KEY `guide_id` (`guide_id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `driver_id` (`driver_id`),
  KEY `assignment_date` (`assignment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- INDEXES FOR PERFORMANCE
-- ============================================================================

CREATE INDEX IF NOT EXISTS `idx_vouchers_pickup_date` ON `vouchers`(`pickup_date`);
CREATE INDEX IF NOT EXISTS `idx_vouchers_company` ON `vouchers`(`company_name`);
CREATE INDEX IF NOT EXISTS `idx_invoices_status` ON `invoices`(`status`);
CREATE INDEX IF NOT EXISTS `idx_invoices_created` ON `invoices`(`created_at`);
CREATE INDEX IF NOT EXISTS `idx_reminder_logs_voucher` ON `reminder_logs`(`voucher_id`);
CREATE INDEX IF NOT EXISTS `idx_activity_logs_user` ON `activity_logs`(`user_id`);
CREATE INDEX IF NOT EXISTS `idx_notifications_user` ON `notifications`(`user_id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ============================================================================
-- END OF SCHEMA
-- ============================================================================
