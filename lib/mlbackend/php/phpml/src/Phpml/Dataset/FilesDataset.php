<?php

declare(strict_types=1);

namespace Phpml\Dataset;

use Phpml\Exception\DatasetException;

class FilesDataset extends ArrayDataset
{
    public function __construct(string $rootPath)
    {
        if (!is_dir($rootPath)) {
            throw new DatasetException(sprintf('Dataset root folder "%s" missing.', $rootPath));
        }

        $this->scanRootPath($rootPath);
    }

    private function scanRootPath(string $rootPath): void
    {
        $dirs = glob($rootPath.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);

        if ($dirs === false) {
            throw new DatasetException(sprintf('An error occurred during directory "%s" scan', $rootPath));
        }

        foreach ($dirs as $dir) {
            $this->scanDir($dir);
        }
    }

    private function scanDir(string $dir): void
    {
        $target = basename($dir);

        $files = glob($dir.DIRECTORY_SEPARATOR.'*');
        if ($files === false) {
            return;
        }

        foreach (array_filter($files, 'is_file') as $file) {
            $this->samples[] = file_get_contents($file);
            $this->targets[] = $target;
        }
    }
}
