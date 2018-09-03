<?php

declare(strict_types=1);

namespace Phpml\Dataset\Demo;

use Phpml\Dataset\CsvDataset;

/**
 * Classes: 3
 * Samples per class: class 1 59; class 2 71; class 3 48
 * Samples total: 178
 * Features per sample: 13.
 */
class WineDataset extends CsvDataset
{
    public function __construct()
    {
        $filepath = __DIR__.'/../../../../data/wine.csv';
        parent::__construct($filepath, 13, true);
    }
}
