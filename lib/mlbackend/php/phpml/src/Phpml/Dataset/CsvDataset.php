<?php

declare(strict_types=1);

namespace Phpml\Dataset;

use Phpml\Exception\FileException;

class CsvDataset extends ArrayDataset
{
    /**
     * @var array
     */
    protected $columnNames = [];

    /**
     * @throws FileException
     */
    public function __construct(string $filepath, int $features, bool $headingRow = true, string $delimiter = ',', int $maxLineLength = 0)
    {
        if (!file_exists($filepath)) {
            throw new FileException(sprintf('File "%s" missing.', basename($filepath)));
        }

        $handle = fopen($filepath, 'rb');
        if ($handle === false) {
            throw new FileException(sprintf('File "%s" can\'t be open.', basename($filepath)));
        }

        if ($headingRow) {
            $data = fgetcsv($handle, $maxLineLength, $delimiter);
            $this->columnNames = array_slice((array) $data, 0, $features);
        } else {
            $this->columnNames = range(0, $features - 1);
        }

        $samples = $targets = [];
        while ($data = fgetcsv($handle, $maxLineLength, $delimiter)) {
            $samples[] = array_slice($data, 0, $features);
            $targets[] = $data[$features];
        }

        fclose($handle);

        parent::__construct($samples, $targets);
    }

    public function getColumnNames(): array
    {
        return $this->columnNames;
    }
}
