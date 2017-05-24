<?php

declare(strict_types=1);

namespace Phpml\Dataset;

use Phpml\Exception\FileException;

class CsvDataset extends ArrayDataset
{
    /**
     * @var array
     */
    protected $columnNames;

    /**
     * @param string $filepath
     * @param int    $features
     * @param bool   $headingRow
     * @param string $delimiter
     *
     * @throws FileException
     */
    public function __construct(string $filepath, int $features, bool $headingRow = true, string $delimiter = ',')
    {
        if (!file_exists($filepath)) {
            throw FileException::missingFile(basename($filepath));
        }

        if (false === $handle = fopen($filepath, 'rb')) {
            throw FileException::cantOpenFile(basename($filepath));
        }

        if ($headingRow) {
            $data = fgetcsv($handle, 1000, $delimiter);
            $this->columnNames = array_slice($data, 0, $features);
        } else {
            $this->columnNames = range(0, $features - 1);
        }

        $samples = $targets = [];
        while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
            $samples[] = array_slice($data, 0, $features);
            $targets[] = $data[$features];
        }

        fclose($handle);

        parent::__construct($samples, $targets);
    }

    /**
     * @return array
     */
    public function getColumnNames()
    {
        return $this->columnNames;
    }
}
