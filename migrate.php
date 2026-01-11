<?php

// 数据库配置
$host = '127.0.0.1';
$port = '3306';
$database = 'genealogy_system';
$username = 'root';
$password = '';

// 创建数据库连接
try {
    $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 创建数据库（如果不存在）
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "数据库 '$database' 创建成功或已存在\n";
    
    // 选择数据库
    $pdo->exec("USE $database");
    echo "已选择数据库 '$database'\n";
    
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

// 定义迁移SQL语句
$migrations = [
    '2025_12_17_000001_create_users_table' => "
        CREATE TABLE IF NOT EXISTS users (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            email_verified_at TIMESTAMP NULL DEFAULT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(255) DEFAULT 'user',
            remember_token VARCHAR(100) DEFAULT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",
    '2025_12_17_000002_create_family_members_table' => "
        CREATE TABLE IF NOT EXISTS family_members (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            gender ENUM('male', 'female', 'other') NOT NULL,
            birth_date DATE NULL DEFAULT NULL,
            death_date DATE NULL DEFAULT NULL,
            birth_place VARCHAR(255) NULL DEFAULT NULL,
            death_place VARCHAR(255) NULL DEFAULT NULL,
            occupation VARCHAR(255) NULL DEFAULT NULL,
            biography TEXT NULL DEFAULT NULL,
            father_id BIGINT UNSIGNED NULL DEFAULT NULL,
            mother_id BIGINT UNSIGNED NULL DEFAULT NULL,
            spouse_id BIGINT UNSIGNED NULL DEFAULT NULL,
            ziwei_id BIGINT UNSIGNED NULL DEFAULT NULL,
            clan_id BIGINT UNSIGNED NULL DEFAULT NULL,
            status VARCHAR(255) DEFAULT 'active',
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (father_id) REFERENCES family_members(id) ON DELETE SET NULL,
            FOREIGN KEY (mother_id) REFERENCES family_members(id) ON DELETE SET NULL,
            FOREIGN KEY (spouse_id) REFERENCES family_members(id) ON DELETE SET NULL,
            FOREIGN KEY (ziwei_id) REFERENCES ziwei(id) ON DELETE SET NULL,
            FOREIGN KEY (clan_id) REFERENCES clans(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",
    '2025_12_17_000003_create_ziwei_table' => "
        CREATE TABLE IF NOT EXISTS ziwei (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            generation INT NOT NULL,
            characters VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",
    '2025_12_17_000004_create_clans_table' => "
        CREATE TABLE IF NOT EXISTS clans (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            origin VARCHAR(255) NULL DEFAULT NULL,
            history TEXT NULL DEFAULT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",
    '2025_12_17_000005_create_ancestral_halls_table' => "
        CREATE TABLE IF NOT EXISTS ancestral_halls (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            location VARCHAR(255) NULL DEFAULT NULL,
            construction_year INT NULL DEFAULT NULL,
            description TEXT NULL DEFAULT NULL,
            clan_id BIGINT UNSIGNED NULL DEFAULT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (clan_id) REFERENCES clans(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",
    '2025_12_17_000006_create_migrations_records_table' => "
        CREATE TABLE IF NOT EXISTS migration_records (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            family_member_id BIGINT UNSIGNED NOT NULL,
            migration_date DATE NULL DEFAULT NULL,
            from_place VARCHAR(255) NULL DEFAULT NULL,
            to_place VARCHAR(255) NULL DEFAULT NULL,
            reason VARCHAR(255) NULL DEFAULT NULL,
            description TEXT NULL DEFAULT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (family_member_id) REFERENCES family_members(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",
    '2025_12_17_000007_create_graves_table' => "
        CREATE TABLE IF NOT EXISTS graves (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            family_member_id BIGINT UNSIGNED NULL DEFAULT NULL,
            location VARCHAR(255) NOT NULL,
            burial_date DATE NULL DEFAULT NULL,
            description TEXT NULL DEFAULT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (family_member_id) REFERENCES family_members(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",
    '2025_12_17_000008_create_family_rules_table' => "
        CREATE TABLE IF NOT EXISTS family_rules (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            clan_id BIGINT UNSIGNED NULL DEFAULT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (clan_id) REFERENCES clans(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",
    '2025_12_17_000009_create_clan_activities_table' => "
        CREATE TABLE IF NOT EXISTS clan_activities (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            clan_id BIGINT UNSIGNED NULL DEFAULT NULL,
            title VARCHAR(255) NOT NULL,
            date DATE NOT NULL,
            location VARCHAR(255) NOT NULL,
            description TEXT NULL DEFAULT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (clan_id) REFERENCES clans(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",
    '2025_12_17_000010_create_media_files_table' => "
        CREATE TABLE IF NOT EXISTS media_files (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            family_member_id BIGINT UNSIGNED NULL DEFAULT NULL,
            clan_id BIGINT UNSIGNED NULL DEFAULT NULL,
            type ENUM('photo', 'video', 'document') NOT NULL,
            title VARCHAR(255) NOT NULL,
            file_path VARCHAR(255) NOT NULL,
            description TEXT NULL DEFAULT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (family_member_id) REFERENCES family_members(id) ON DELETE SET NULL,
            FOREIGN KEY (clan_id) REFERENCES clans(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    "
];

// 执行迁移
echo "开始执行数据库迁移...\n";
foreach ($migrations as $name => $sql) {
    try {
        $pdo->exec($sql);
        echo "✓ 迁移 '$name' 执行成功\n";
    } catch (PDOException $e) {
        echo "✗ 迁移 '$name' 执行失败: " . $e->getMessage() . "\n";
    }
}

echo "\n所有迁移执行完成!\n";