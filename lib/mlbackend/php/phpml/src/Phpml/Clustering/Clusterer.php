<?php

declare(strict_types=1);

namespace Phpml\Clustering;

interface Clusterer
{
    /**
     * @param array $samples
     *
     * @return array
     */
    public function cluster(array $samples);
}
