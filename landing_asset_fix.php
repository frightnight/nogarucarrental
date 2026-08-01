<?php

$path = __DIR__.'/resources/views/landing.blade.php';
$text = file_get_contents($path);
$text = preg_replace_callback('#(href|src)="assets/([^"]*)"#', function ($matches) {
    return sprintf('%s="{{ asset(\'assets/%s\') }}"', $matches[1], $matches[2]);
}, $text);
$text = preg_replace_callback('#url\(assets/([^\)]+)\)#', function ($matches) {
    return sprintf('url({{ asset(\'assets/%s\') }})', $matches[1]);
}, $text);
$text = str_replace('href="index.html"', 'href="{{ url(\'/\') }}"', $text);
$text = str_replace('href="auth-sign-in.html"', 'href="{{ route(\'login\') }}"', $text);
$text = str_replace('href="auth-sign-up.html"', 'href="{{ route(\'register\') }}"', $text);
$text = file_put_contents($path, $text);
if ($text === false) {
    echo "failed\n";
} else {
    echo "ok\n";
}
