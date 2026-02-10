<?php
/**
 * 500 Internal Server Error Page
 * 
 * @package CYN_Tourism
 */

http_response_code(500);

// Try to load language if available
$lang = [];
if (file_exists(__DIR__ . '/../languages/en.php')) {
    $lang = require __DIR__ . '/../languages/en.php';
}

function __e($key, $default = '') {
    global $lang;
    return isset($lang[$key]) ? $lang[$key] : $default;
}

$pageTitle = __e('server_error', 'Server Error');

// Log the error if possible
if (file_exists(__DIR__ . '/../Logger.php')) {
    require_once __DIR__ . '/../Logger.php';
    Logger::error('500 Internal Server Error', [
        'uri' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'unknown',
        'method' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'unknown',
        'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'none'
    ]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - <?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #eef2ff 0%, #fef2f2 50%, #fff7ed 100%);
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
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
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
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(234, 88, 12, 0.3);
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
        
        .support-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            color: #94a3b8;
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
        <div class="error-icon">⚠️</div>
        <div class="error-code">500</div>
        <h1 class="error-title"><?php echo htmlspecialchars(__e('server_error', 'Server Error')); ?></h1>
        <p class="error-message">
            <?php echo htmlspecialchars(__e('server_error_message', 'Something went wrong on our end. We are working to fix the issue. Please try again later.')); ?>
        </p>
        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <span>🏠</span> <?php echo htmlspecialchars(__e('go_home', 'Go to Homepage')); ?>
            </a>
            <a href="javascript:location.reload()" class="btn btn-secondary">
                <span>🔄</span> <?php echo htmlspecialchars(__e('refresh', 'Refresh Page')); ?>
            </a>
        </div>
        <div class="support-info">
            <p>If the problem persists, please contact support.</p>
            <p>Error ID: <?php echo uniqid('ERR_'); ?></p>
        </div>
    </div>
</body>
</html>
