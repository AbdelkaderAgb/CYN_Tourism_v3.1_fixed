<?php
/**
 * 404 Not Found Error Page
 * 
 * @package CYN_Tourism
 */

http_response_code(404);

// Try to load language if available
$lang = [];
if (file_exists(__DIR__ . '/../languages/en.php')) {
    $lang = require __DIR__ . '/../languages/en.php';
}

function __e($key, $default = '') {
    global $lang;
    return isset($lang[$key]) ? $lang[$key] : $default;
}

$pageTitle = __e('page_not_found', 'Page Not Found');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - <?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 50%, #c7d2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e293b;
        }
        
        .error-container {
            background: white;
            border-radius: 1rem;
            padding: 60px;
            text-align: center;
            box-shadow: 0 12px 40px rgba(79, 70, 229, 0.1);
            max-width: 500px;
            width: 90%;
        }
        
        .error-code {
            font-size: 120px;
            font-weight: 700;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 20px;
        }
        
        .error-title {
            font-size: 28px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 15px;
        }
        
        .error-message {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.25s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        }
        
        .btn-secondary {
            background: #f1f5f9;
            color: #1e293b;
        }
        
        .btn-secondary:hover {
            background: #e2e8f0;
        }
        
        .error-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 480px) {
            .error-container {
                padding: 40px 30px;
            }
            
            .error-code {
                font-size: 80px;
            }
            
            .error-title {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">🔍</div>
        <div class="error-code">404</div>
        <h1 class="error-title"><?php echo htmlspecialchars(__e('page_not_found', 'Page Not Found')); ?></h1>
        <p class="error-message">
            <?php echo htmlspecialchars(__e('page_not_found_message', 'The page you are looking for does not exist or has been moved.')); ?>
        </p>
        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <span>🏠</span> <?php echo htmlspecialchars(__e('go_home', 'Go to Homepage')); ?>
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <span>←</span> <?php echo htmlspecialchars(__e('go_back', 'Go Back')); ?>
            </a>
        </div>
    </div>
</body>
</html>
