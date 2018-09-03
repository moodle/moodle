<?php

declare(strict_types=1);

namespace Phpml;

interface Transformer
{
    /**
     * @param array $samples
     */
    public function fit(array $samples);

    /**
     * @param array $samples
     */
    public function transform(array &$samples);
}
