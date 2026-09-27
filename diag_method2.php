<?php
require __DIR__.'/vendor/autoload.php';
$reflector = new ReflectionClass(\Filament\Auth\Pages\PasswordReset\RequestPasswordReset::class);
$method = $reflector->getMethod('getSentNotification');

$fileName = $method->getFileName();
$startLine = $method->getStartLine() - 1;
$endLine = $method->getEndLine();
$length = $endLine - $startLine;

$source = file($fileName);
$body = implode("", array_slice($source, $startLine, $length));

echo $body;
