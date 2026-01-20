<?php
// Language switching logic
$available_langs = ['en', 'fr', 'zh-CN'];
$default_lang = 'zh-CN';

if (isset($_GET['lang']) && in_array($_GET['lang'], $available_langs)) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'] ?? $default_lang;

// Adjust path for inclusion from different directories
if (file_exists("languages/{$lang}.php")) {
    include_once "languages/{$lang}.php";
} elseif (file_exists("../languages/{$lang}.php")) {
    include_once "../languages/{$lang}.php";
} else {
    // Fallback to default language if file not found
    if (file_exists("languages/{$default_lang}.php")) {
        include_once "languages/{$default_lang}.php";
    } elseif (file_exists("../languages/{$default_lang}.php")) {
        include_once "../languages/{$default_lang}.php";
    }
}

// Function to get translation
if (!function_exists('t')) {
    function t($key) {
        global $translations;
        return $translations[$key] ?? $key;
    }
}
?>