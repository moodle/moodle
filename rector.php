<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

declare(strict_types=1);

use Rector\Config\RectorConfig;

/**
 * Rector configuration.
 *
 * @package    core
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.MoodleInternal.MoodleInternalGlobalState

/**
 * Get the list of third party paths to exclude, based on all thirdpartylibs.xml files.
 *
 * @return string[]
 */
function get_third_party_paths(): array {
    $paths = [
        __DIR__ . '/node_modules/',
        __DIR__ . '/vendor/',
        __DIR__ . '/vendor-bin/',
    ];
    $skipdirs = ['node_modules', 'vendor', 'vendor-bin', '.git'];

    $iterator = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator(__DIR__, FilesystemIterator::SKIP_DOTS),
            fn (SplFileInfo $file) => !$file->isDir() || !in_array($file->getFilename(), $skipdirs, true),
        ),
    );

    foreach ($iterator as $file) {
        if ($file->getFilename() !== 'thirdpartylibs.xml') {
            continue;
        }
        $xml = simplexml_load_file($file->getPathname());
        if ($xml === false) {
            continue;
        }
        foreach ($xml->xpath('/libraries/library/location') as $location) {
            $path = $file->getPath() . '/' . trim((string) $location);
            if (!str_contains($path, '*') && is_dir($path)) {
                $path = rtrim($path, '/') . '/';
            }
            $paths[] = $path;
        }
    }

    return array_values(array_unique($paths));
}

$exclude = get_third_party_paths();

// Fallback to 8 locally if RECTOR_MAX_PROCS isn't passed.
$maxprocs = (int) (getenv('RECTOR_MAX_PROCS') ?: 8);

return RectorConfig::configure()
    ->withCache(
        cacheDirectory: __DIR__ . '/.rector/cache',
    )
    ->withParallel(
        // Increase timeout (default is 120).
        timeoutSeconds: 360,
        // Reduce concurrent processes (default is 32).
        maxNumberOfProcess: $maxprocs,
        // Reduce number of files per job chunk (default is 16).
        jobSize: 10,
    )
    ->withBootstrapFiles([
        __DIR__ . '/.rector/bootstrap.php',
    ])
    ->withPaths([
        __DIR__,
    ])
    ->withRootFiles()
    ->withSkip([
        __DIR__ . '/coverage',
        __DIR__ . '/.git',
        __DIR__ . '/.rector',
        ...$exclude,
    ])
    ->withRules([
        // Annotations to Attributes.
        \Rector\PHPUnit\AnnotationsToAttributes\Rector\Class_\AnnotationWithValueToAttributeRector::class,
        \Rector\PHPUnit\AnnotationsToAttributes\Rector\Class_\CoversAnnotationWithValueToAttributeRector::class,
        \Rector\PHPUnit\AnnotationsToAttributes\Rector\Class_\RequiresAnnotationWithValueToAttributeRector::class,
        \Rector\PHPUnit\AnnotationsToAttributes\Rector\ClassMethod\DataProviderAnnotationToAttributeRector::class,
        \Rector\PHPUnit\AnnotationsToAttributes\Rector\ClassMethod\DependsAnnotationWithValueToAttributeRector::class,
        \Rector\PHPUnit\AnnotationsToAttributes\Rector\ClassMethod\TestWithAnnotationToAttributeRector::class,

        // Code Quality improvements for PHPUnit tests for PHPUnit 12.
        \Rector\PHPUnit\CodeQuality\Rector\MethodCall\UseSpecificWillMethodRector::class,
        \Rector\PHPUnit\CodeQuality\Rector\Class_\VoidMethodWithCallbackToWillReturnCallbackRector::class,
    ]);
