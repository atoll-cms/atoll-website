#!/usr/bin/env php
<?php

declare(strict_types=1);

$outDir = $argv[1] ?? 'dist';
$host = '127.0.0.1';
$port = 8099;
$baseUrl = "http://{$host}:{$port}";

rrmdir($outDir);
mkdir($outDir, 0775, true);

$cmd = sprintf('php -S %s:%d -t . index.php', $host, $port);
$descriptors = [
    0 => ['pipe', 'r'],
    1 => ['file', '/tmp/atoll-export.log', 'a'],
    2 => ['file', '/tmp/atoll-export.log', 'a'],
];
$process = proc_open($cmd, $descriptors, $pipes);
if (!is_resource($process)) {
    fwrite(STDERR, "Could not start local server\n");
    exit(1);
}

try {
    waitForServer($baseUrl . '/');

    $queue = ['/'];
    $seen = [];

    while ($queue !== []) {
        $path = array_shift($queue);
        $path = normalizePath($path);

        if ($path === '' || isset($seen[$path])) {
            continue;
        }

        if (shouldSkipPath($path)) {
            $seen[$path] = true;
            continue;
        }

        $seen[$path] = true;

        $response = fetch($baseUrl . $path);
        if ($response['status'] >= 400) {
            continue;
        }

        saveResponse($outDir, $path, $response['body'], $response['content_type']);

        if (str_contains($response['content_type'], 'text/html')) {
            foreach (extractAssetAndLinkPaths($response['body']) as $next) {
                if (!isset($seen[$next])) {
                    $queue[] = $next;
                }
            }
        }

        if ($path === '/sitemap.xml') {
            foreach (extractSitemapPaths($response['body']) as $next) {
                if (!isset($seen[$next])) {
                    $queue[] = $next;
                }
            }
        }

        if ($path === '/') {
            foreach (['/sitemap.xml', '/robots.txt'] as $fixed) {
                if (!isset($seen[$fixed])) {
                    $queue[] = $fixed;
                }
            }
        }
    }

    fwrite(STDOUT, "Static export complete: {$outDir}\n");
} finally {
    proc_terminate($process);
}

function waitForServer(string $url): void
{
    $max = 50;
    for ($i = 0; $i < $max; $i++) {
        $response = fetch($url);
        if ($response['status'] > 0) {
            return;
        }
        usleep(100_000);
    }

    throw new RuntimeException('Server did not start in time.');
}

/** @return array{status:int,body:string,content_type:string} */
function fetch(string $url): array
{
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'ignore_errors' => true,
            'follow_location' => 1,
            'max_redirects' => 3,
        ],
    ]);

    $body = @file_get_contents($url, false, $context);
    $headers = function_exists('http_get_last_response_headers')
        ? http_get_last_response_headers()
        : ($http_response_header ?? []);

    $status = 0;
    $contentType = 'application/octet-stream';

    if (is_array($headers)) {
        foreach ($headers as $line) {
            if (preg_match('#^HTTP/\S+\s+(\d{3})#', (string) $line, $m) === 1) {
                $status = (int) $m[1];
            }
            if (stripos((string) $line, 'Content-Type:') === 0) {
                $contentType = trim(substr((string) $line, strlen('Content-Type:')));
            }
        }
    }

    return [
        'status' => $status,
        'body' => is_string($body) ? $body : '',
        'content_type' => strtolower($contentType),
    ];
}

function normalizePath(string $path): string
{
    if ($path === '') {
        return '/';
    }

    $path = parse_url($path, PHP_URL_PATH) ?: '/';
    return '/' . ltrim($path, '/');
}

function shouldSkipPath(string $path): bool
{
    $blockedPrefixes = [
        '/admin',
        '/forms',
        '/core/src',
        '/core/tools',
        '/core/installer',
        '/core/bin',
        '/backups',
        '/cache',
    ];

    foreach ($blockedPrefixes as $prefix) {
        if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
            return true;
        }
    }

    return false;
}

/** @return array<int, string> */
function extractAssetAndLinkPaths(string $html): array
{
    $paths = [];
    if (preg_match_all('/(?:href|src)="([^"#?]+)"/i', $html, $matches) < 1) {
        return [];
    }

    foreach ($matches[1] as $candidate) {
        if (!is_string($candidate) || !str_starts_with($candidate, '/')) {
            continue;
        }
        $paths[] = normalizePath($candidate);
    }

    return array_values(array_unique($paths));
}

/** @return array<int, string> */
function extractSitemapPaths(string $xml): array
{
    $paths = [];
    if (preg_match_all('/<loc>(.*?)<\/loc>/i', $xml, $matches) < 1) {
        return [];
    }

    foreach ($matches[1] as $url) {
        if (!is_string($url)) {
            continue;
        }
        $path = parse_url($url, PHP_URL_PATH);
        if (!is_string($path) || $path === '') {
            continue;
        }
        $paths[] = normalizePath($path);
    }

    return array_values(array_unique($paths));
}

function saveResponse(string $outDir, string $path, string $body, string $contentType): void
{
    $target = targetFileForPath($outDir, $path, $contentType);
    $dir = dirname($target);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    file_put_contents($target, $body);
}

function targetFileForPath(string $outDir, string $path, string $contentType): string
{
    $trimmed = ltrim($path, '/');
    if ($trimmed === '') {
        return $outDir . '/index.html';
    }

    $ext = pathinfo($trimmed, PATHINFO_EXTENSION);
    if ($ext !== '') {
        return $outDir . '/' . $trimmed;
    }

    if (str_contains($contentType, 'xml')) {
        return $outDir . '/' . $trimmed . '.xml';
    }

    if (str_contains($contentType, 'text/plain')) {
        return $outDir . '/' . $trimmed . '.txt';
    }

    return $outDir . '/' . $trimmed . '/index.html';
}

function rrmdir(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }

    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($it as $item) {
        if ($item->isDir()) {
            rmdir($item->getPathname());
        } else {
            unlink($item->getPathname());
        }
    }

    rmdir($dir);
}
