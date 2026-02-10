<?php
/**
 * CYN Tourism - Login Fix & Diagnostic Tool
 * 
 * This script diagnoses login issues and allows resetting the admin password.
 * WARNING: Delete this file immediately after use!
 */

// Define APP_ROOT if not defined
if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__);
}

// Load configuration and database
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

$message = '';
$messageType = 'info';
$action = $_POST['action'] ?? '';

function setMessage($msg, $type = 'info') {
    global $message, $messageType;
    $message = $msg;
    $messageType = $type;
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Test Password
    if ($action === 'test_password') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        try {
            $user = Database::getInstance()->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
            
            if ($user) {
                $check = password_verify($password, $user['password']);
                if ($check) {
                    setMessage("✅ Password is CORRECT! The hash matches.", 'success');
                } else {
                    // Check if it's plain text (legacy)
                    if ($password === $user['password']) {
                         setMessage("⚠️ Password matches as PLAIN TEXT (insecure). Login should update it.", 'warning');
                    } else {
                        setMessage("❌ Password is INCORRECT. The stored hash does not match.", 'danger');
                    }
                }
            } else {
                setMessage("❌ User not found with email: " . htmlspecialchars($email), 'danger');
            }
        } catch (Exception $e) {
            setMessage("Error: " . $e->getMessage(), 'danger');
        }
    }
    
    // 2. Reset Password
    elseif ($action === 'reset_password') {
        $email = $_POST['email'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        
        if (strlen($newPassword) < 4) {
             setMessage("Password must be at least 4 characters.", 'danger');
        } else {
            try {
                $user = Database::getInstance()->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
                
                if ($user) {
                    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                    Database::getInstance()->query(
                        "UPDATE users SET password = ?, locked_until = NULL, failed_login_attempts = 0 WHERE id = ?",
                        [$hash, $user['id']]
                    );
                    setMessage("✅ Password successfully reset for {$email}!", 'success');
                } else {
                    // Try to create the user if it doesn't exist (Admin backup)
                    if ($email === 'admin@cyntourism.com') {
                         $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                         Database::getInstance()->query(
                            "INSERT INTO users (first_name, last_name, email, password, role, status, email_verified) 
                             VALUES ('System', 'Admin', ?, ?, 'admin', 'active', 1)",
                            [$email, $hash]
                         );
                         setMessage("✅ Admin user created and password set!", 'success');
                    } else {
                        setMessage("❌ User not found.", 'danger');
                    }
                }
            } catch (Exception $e) {
                setMessage("Error: " . $e->getMessage(), 'danger');
            }
        }
    }
    
    // 3. unlock account
    elseif ($action === 'unlock_account') {
        $email = $_POST['email'] ?? '';
        try {
             Database::getInstance()->query(
                "UPDATE users SET locked_until = NULL, failed_login_attempts = 0 WHERE email = ?",
                [$email]
            );
            // Clear session lockouts too
            if (session_status() !== PHP_SESSION_ACTIVE) session_start();
            $key = 'login_attempts_' . md5($email);
            unset($_SESSION[$key]);
            
            setMessage("✅ Account unlocked for {$email}.", 'success');
        } catch (Exception $e) {
            setMessage("Error: " . $e->getMessage(), 'danger');
        }
    }
}

// Get User Status for Display
$adminUser = null;
$dbStatus = false;
$dbError = "";

try {
    $db = Database::getInstance();
    $dbStatus = true;
    $adminUser = $db->fetchOne("SELECT * FROM users WHERE email = 'admin@cyntourism.com' OR role = 'admin' LIMIT 1");
} catch (Exception $e) {
    $dbStatus = false;
    $dbError = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CYN Tourism - Login Diagnostic</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f3f4f6; padding: 20px; color: #1f2937; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        h1 { font-size: 24px; margin-bottom: 20px; border-bottom: 2px solid #e5e7eb; padding-bottom: 15px; }
        h2 { font-size: 18px; margin-top: 30px; margin-bottom: 15px; color: #4b5563; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-weight: 600; font-size: 14px; }
        .status-success { background: #d1fae5; color: #065f46; }
        .status-error { background: #fee2e2; color: #b91c1c; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-info { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-danger { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        
        .code-block { background: #111827; color: #e5e7eb; padding: 15px; border-radius: 6px; font-family: monospace; overflow-x: auto; font-size: 13px; line-height: 1.5; }
        
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 500; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; }
        button { background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 500; }
        button:hover { background: #1d4ed8; }
        button.btn-danger { background: #dc2626; }
        button.btn-danger:hover { background: #b91c1c; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; }
        
        .warning-banner { background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; padding: 10px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; text-align: center; }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="warning-banner">
            ⚠️ SECURITY WARNING: Delete this file (fix-login.php) from your server immediately after use!
        </div>

        <h1>Login Diagnostic Tool</h1>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Database Status -->
        <h2>1. Database Connection</h2>
        <?php if ($dbStatus): ?>
            <div class="alert alert-success">
                ✅ Connected to database: <strong><?php echo DB_NAME; ?></strong> at <?php echo DB_HOST; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                ❌ Database connection failed!<br>
                Error: <?php echo htmlspecialchars($dbError); ?>
            </div>
        <?php endif; ?>

        <!-- Admin User Status -->
        <h2>2. Admin User Status</h2>
        <?php if ($adminUser): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <td><?php echo $adminUser['id']; ?></td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td><?php echo htmlspecialchars($adminUser['first_name'] . ' ' . $adminUser['last_name']); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo htmlspecialchars($adminUser['email']); ?></td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td><span class="status-badge status-success"><?php echo $adminUser['role']; ?></span></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?php echo $adminUser['status']; ?></td>
                </tr>
                <tr>
                    <th>Password Hash</th>
                    <td style="font-family:monospace; font-size:12px; word-break:break-all;">
                        <?php echo htmlspecialchars(substr($adminUser['password'], 0, 20) . '...'); ?>
                        <?php if (strlen($adminUser['password']) < 60): ?>
                            <span style="color:red; font-weight:bold;">(Warning: Length < 60 chars, likely not a valid bcrypt hash)</span>
                        <?php else: ?>
                            <span style="color:green; font-weight:bold;">(Valid Hash Format)</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        <?php else: ?>
            <div class="alert alert-warning">
                ⚠️ No admin user found! You can create one below.
            </div>
        <?php endif; ?>

        <hr>

        <!-- Test Password -->
        <h2>3. Test a Password</h2>
        <form method="POST" style="background: #f9fafb; padding: 20px; border-radius: 8px;">
            <input type="hidden" name="action" value="test_password">
            <div class="form-group">
                <label>Email to Test:</label>
                <input type="email" name="email" value="<?php echo $adminUser ? htmlspecialchars($adminUser['email']) : 'admin@cyntourism.com'; ?>" required>
            </div>
            <div class="form-group">
                <label>Password to Check:</label>
                <input type="text" name="password" placeholder="Enter password to test (e.g. Admin@123)" required>
            </div>
            <button type="submit">Test Password</button>
        </form>

        <!-- Reset Password -->
        <h2>4. Reset Admin Password</h2>
        <form method="POST" style="background: #fff0f0; padding: 20px; border-radius: 8px; border: 1px solid #fecaca;">
            <input type="hidden" name="action" value="reset_password">
            <div class="form-group">
                <label>Email to Reset:</label>
                <input type="email" name="email" value="<?php echo $adminUser ? htmlspecialchars($adminUser['email']) : 'admin@cyntourism.com'; ?>" required>
            </div>
            <div class="form-group">
                <label>New Password:</label>
                <input type="text" name="new_password" placeholder="Enter NEW password" required>
            </div>
            <button type="submit" class="btn-danger">Reset Password & Create Admin</button>
        </form>
        
         <!-- Clear Lockout -->
        <h2>5. Clear Lockout</h2>
        <form method="POST" style="background: #f0fdf4; padding: 20px; border-radius: 8px; border: 1px solid #bbf7d0;">
            <input type="hidden" name="action" value="unlock_account">
            <div class="form-group">
                <label>Email to Unlock:</label>
                <input type="email" name="email" value="<?php echo $adminUser ? htmlspecialchars($adminUser['email']) : 'admin@cyntourism.com'; ?>" required>
            </div>
            <button type="submit" style="background:#16a34a;">Clear Lockout</button>
        </form>

    </div>
</body>
</html>
