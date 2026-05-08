<?php
/**
 * PHPUnit bootstrap for hypeDiscussions plugin tests.
 * Plugin must be installed at {elgg_root}/mod/hypeDiscussions/
 */

// tests/ -> mod/plugin/ -> mod/ -> elgg_root/
$elggRoot = dirname(dirname(dirname(__DIR__)));

require_once $elggRoot . '/vendor/autoload.php';

// Load Elgg test classes (UnitTestCase, IntegrationTestCase, etc.)
$testClassesDir = $elggRoot . '/vendor/elgg/elgg/engine/tests/classes';
spl_autoload_register(function ($class) use ($testClassesDir) {
    $file = $testClassesDir . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// ElggDiscussion is defined by the core discussions plugin — not in composer autoload
$coreModDir = $elggRoot . '/vendor/elgg/elgg/mod';
spl_autoload_register(function ($class) use ($coreModDir) {
    foreach (new \FilesystemIterator($coreModDir, \FilesystemIterator::SKIP_DOTS) as $pluginDir) {
        $file = $pluginDir->getRealPath() . '/classes/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

$pluginRoot = dirname(__DIR__);
spl_autoload_register(function ($class) use ($pluginRoot) {
    if (strncmp($class, 'hypeJunction\\', 13) !== 0) {
        return;
    }
    $file = $pluginRoot . '/classes/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

\Elgg\Application::loadCore();
