<?php
// install_composer_phpmailer.php
// Complete Composer and PHPMailer installation script

echo "<h2>🚀 Installing Composer & PHPMailer</h2>";

echo "<h3>📥 Step 1: Download Composer</h3>";

// Download Composer installer
$composer_url = 'https://getcomposer.org/installer';
$composer_installer = 'composer-setup.php';

echo "<p>Downloading Composer installer...</p>";

if (file_put_contents($composer_installer, file_get_contents($composer_url))) {
    echo "<p style='color: green;'>✅ Composer installer downloaded successfully</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to download Composer installer</p>";
    echo "<p><strong>Manual download required:</strong></p>";
    echo "<ol>";
    echo "<li>Go to: <a href='https://getcomposer.org/download/' target='_blank'>https://getcomposer.org/download/</a></li>";
    echo "<li>Download the Windows installer (Composer-Setup.exe)</li>";
    echo "<li>Run the installer</li>";
    echo "<li>Restart your command prompt</li>";
    echo "</ol>";
    exit;
}

echo "<h3>🔧 Step 2: Install Composer</h3>";

// Install Composer
$php_path = 'C:\\xampp\\php\\php.exe';
$install_command = "$php_path $composer_installer";

echo "<p>Running: <code>$install_command</code></p>";

$output = [];
$return_code = 0;
exec($install_command, $output, $return_code);

if ($return_code === 0) {
    echo "<p style='color: green;'>✅ Composer installed successfully</p>";
    echo "<pre>" . implode("\n", $output) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ Composer installation failed</p>";
    echo "<pre>" . implode("\n", $output) . "</pre>";
    echo "<p><strong>Manual installation required:</strong></p>";
    echo "<ol>";
    echo "<li>Download Composer from: <a href='https://getcomposer.org/download/' target='_blank'>https://getcomposer.org/download/</a></li>";
    echo "<li>Run the Windows installer</li>";
    echo "<li>Restart command prompt</li>";
    echo "</ol>";
    exit;
}

// Clean up installer
unlink($composer_installer);

echo "<h3>📦 Step 3: Install PHPMailer</h3>";

// Install PHPMailer
$phpmailer_command = "$php_path composer.phar require phpmailer/phpmailer";

echo "<p>Running: <code>$phpmailer_command</code></p>";

$output = [];
$return_code = 0;
exec($phpmailer_command, $output, $return_code);

if ($return_code === 0) {
    echo "<p style='color: green;'>✅ PHPMailer installed successfully</p>";
    echo "<pre>" . implode("\n", $output) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ PHPMailer installation failed</p>";
    echo "<pre>" . implode("\n", $output) . "</pre>";
    echo "<p><strong>Manual installation:</strong></p>";
    echo "<ol>";
    echo "<li>Open command prompt in project directory</li>";
    echo "<li>Run: <code>composer require phpmailer/phpmailer</code></li>";
    echo "</ol>";
    exit;
}

echo "<h3>✅ Installation Complete!</h3>";

// Check if files exist
$files_to_check = [
    'composer.json' => 'Composer configuration',
    'composer.lock' => 'Composer lock file',
    'vendor/autoload.php' => 'Composer autoloader',
    'vendor/phpmailer/phpmailer/src/PHPMailer.php' => 'PHPMailer main class'
];

echo "<h3>📁 Verifying Installation</h3>";

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $description: $file</p>";
    } else {
        echo "<p style='color: red;'>❌ $description: $file (Missing)</p>";
    }
}

echo "<h3>🎯 Next Steps</h3>";
echo "<ol>";
echo "<li>✅ Composer and PHPMailer installed</li>";
echo "<li>🔧 Configure email settings</li>";
echo "<li>🧪 Test email functionality</li>";
echo "<li>📧 Test notification system</li>";
echo "</ol>";

echo "<div style='margin-top: 30px;'>";
echo "<a href='configure_phpmailer.php' class='btn btn-primary'>Configure PHPMailer</a>";
echo "<a href='test_phpmailer_composer.php' class='btn btn-success'>Test PHPMailer</a>";
echo "<a href='index.php' class='btn btn-secondary'>Back to Home</a>";
echo "</div>";

echo "<hr>";
echo "<p><strong>💡 Note:</strong> If installation failed, you may need to install Composer manually from <a href='https://getcomposer.org/download/' target='_blank'>getcomposer.org</a></p>";
?>
