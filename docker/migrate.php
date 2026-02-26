<?php

declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $class = ltrim($class, '\\');

    $map = [
        'App\\Core\\'        => dirname(__DIR__) . '/core/',
        'App\\Models\\'      => dirname(__DIR__) . '/app/models/',
    ];

    foreach ($map as $prefix => $baseDir) {
        if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
            continue;
        }

        $relative = substr($class, strlen($prefix));
        $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
        $file = rtrim($baseDir, "/\\") . DIRECTORY_SEPARATOR . $relativePath;

        if (is_file($file)) {
            require_once $file;
        }
        return;
    }
});

use App\Core\Database\DB;

function connectWithRetry(int $maxAttempts = 30, int $sleepSeconds = 2): \PDO
{
    $attempt = 0;

    while ($attempt < $maxAttempts) {
        try {
            return DB::connect();
        } catch (\PDOException $e) {
            $attempt++;

            if ($attempt >= $maxAttempts) {
                throw $e;
            }

            fwrite(STDERR, sprintf(
                'Database not ready (attempt %d/%d). Retrying in %d seconds...%s',
                $attempt,
                $maxAttempts,
                $sleepSeconds,
                PHP_EOL
            ));

            sleep($sleepSeconds);
        }
    }

    throw new RuntimeException('Unable to connect to database.');
}

$pdo = connectWithRetry();

$migrationsPath = dirname(__DIR__) . '/migrations';
$bootstrapFile = $migrationsPath . '/0000_create_migrations_table.sql';

if (!is_file($bootstrapFile)) {
    throw new RuntimeException('Missing migrations table SQL file.');
}

$tableCheck = $pdo->prepare('SHOW TABLES LIKE ?');
$tableCheck->execute(['migrations']);
$tableExists = (bool) $tableCheck->fetchColumn();

if (!$tableExists) {
    $sql = file_get_contents($bootstrapFile);
    if ($sql === false || trim($sql) === '') {
        throw new RuntimeException('Migrations table SQL file is empty.');
    }

    $pdo->exec($sql);
}

$files = glob($migrationsPath . '/*.sql') ?: [];
sort($files, SORT_STRING);

$stmt = $pdo->prepare('SELECT COUNT(*) FROM migrations WHERE filename = ?');
$insert = $pdo->prepare('INSERT INTO migrations (filename) VALUES (?)');

if (!$tableExists) {
    $insert->execute([basename($bootstrapFile)]);
}

foreach ($files as $file) {
    $filename = basename($file);
    $stmt->execute([$filename]);
    $already = (int) $stmt->fetchColumn();
    if ($already > 0) {
        continue;
    }

    $sql = file_get_contents($file);
    if ($sql === false || trim($sql) === '') {
        continue;
    }

    $pdo->beginTransaction();
    try {
        $pdo->exec($sql);
        $insert->execute([$filename]);
        if ($pdo->inTransaction()) {
            $pdo->commit();
        }
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        fwrite(STDERR, 'Migration failed: ' . $filename . PHP_EOL);
        throw $e;
    }
}
