<?php
/**
 * শব্দকুঞ্জ — Cache Clear Script
 * public_html এ রাখুন, একবার চালান, তারপর মুছুন
 * URL: https://shabdakunja.com/clear_cache.php
 */

// Security: এই ফাইল চালানোর পর অবশ্যই মুছুন!
// অথবা নিচের লাইনটা uncomment করে secret দিন:
// if ($_GET['key'] ?? '' !== 'your-secret') die('Forbidden');

define('BASE', dirname(__DIR__)); // public_html এর parent = laravel root

$deleted = [];
$failed  = [];

// Bootstrap cache
$cacheFiles = [
    BASE . '/bootstrap/cache/config.php',
    BASE . '/bootstrap/cache/routes-v7.php',
    BASE . '/bootstrap/cache/services.php',
    BASE . '/bootstrap/cache/packages.php',
    BASE . '/bootstrap/cache/events.php',
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        @unlink($file) ? $deleted[] = basename($file) : $failed[] = basename($file);
    }
}

// Storage/framework cache
$dirs = [
    BASE . '/storage/framework/cache/data',
    BASE . '/storage/framework/views',
];

$count = 0;
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
        if ($path->isFile() && $path->getFilename() !== '.gitignore') {
            @unlink($path->getPathname()) && $count++;
        }
    }
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Cache Clear</title>
<style>body{font-family:monospace;padding:2rem;background:#f0fdf4}h1{color:#166534}.ok{color:#166534}.err{color:#dc2626}.info{color:#1e40af}</style>
</head><body>
<h1>✅ Shabdakunja Cache Clear</h1>
<p class="ok">Bootstrap cache files deleted: <?= implode(', ', $deleted) ?: 'none' ?></p>
<?php if ($failed): ?><p class="err">Failed: <?= implode(', ', $failed) ?></p><?php endif; ?>
<p class="ok">Framework cache/view files deleted: <?= $count ?></p>
<hr>
<p class="err" style="font-weight:bold">⚠️ এই ফাইলটি এখনই মুছুন: public_html/clear_cache.php</p>
<p class="info">এরপর সাইটে গিয়ে লেখা edit করে দেখুন।</p>
</body></html>
