<?php

declare(strict_types=1);

namespace Phpml\Dataset;

use Phpml\Exception\InvalidArgumentException;

/**
 * MNIST dataset: http://yann.lecun.com/exdb/mnist/
 * original mnist dataset reader: https://github.com/AndrewCarterUK/mnist-neural-network-plain-php
 */
final class MnistDataset extends ArrayDataset
{
    private const MAGIC_IMAGE = 0x00000803;

    private const MAGIC_LABEL = 0x00000801;

    private const IMAGE_ROWS = 28;

    private const IMAGE_COLS = 28;

    public function __construct(string $imagePath, string $labelPath)
    {
        $this->samples = $this->readImages($imagePath);
        $this->targets = $this->readLabels($labelPath);

        if (count($this->samples) !== count($this->targets)) {
            throw new InvalidArgumentException('Must have the same number of images and labels');
        }
    }

    private function readImages(string $imagePath): array
    {
        $stream = fopen($imagePath, 'rb');

        if ($stream === false) {
            throw new InvalidArgumentException('Could not open file: '.$imagePath);
        }

        $images = [];

        try {
            $header = fread($stream, 16);

            $fields = unpack('Nmagic/Nsize/Nrows/Ncols', (string) $header);

            if ($fields['magic'] !== self::MAGIC_IMAGE) {
                throw new InvalidArgumentException('Invalid magic number: '.$imagePath);
            }

            if ($fields['rows'] != self::IMAGE_ROWS) {
                throw new InvalidArgumentException('Invalid number of image rows: '.$imagePath);
            }

            if ($fields['cols'] != self::IMAGE_COLS) {
                throw new InvalidArgumentException('Invalid number of image cols: '.$imagePath);
            }

            for ($i = 0; $i < $fields['size']; $i++) {
                $imageBytes = fread($stream, $fields['rows'] * $fields['cols']);

                // Convert to float between 0 and 1
                $images[] = array_map(function ($b) {
                    return $b / 255;
                }, array_values(unpack('C*', (string) $imageBytes)));
            }
        } finally {
            fclose($stream);
        }

        return $images;
    }

    private function readLabels(string $labelPath): array
    {
        $stream = fopen($labelPath, 'rb');

        if ($stream === false) {
            throw new InvalidArgumentException('Could not open file: '.$labelPath);
        }

        $labels = [];

        try {
            $header = fread($stream, 8);

            $fields = unpack('Nmagic/Nsize', (string) $header);

            if ($fields['magic'] !== self::MAGIC_LABEL) {
                throw new InvalidArgumentException('Invalid magic number: '.$labelPath);
            }

            $labels = fread($stream, $fields['size']);
        } finally {
            fclose($stream);
        }

        return array_values(unpack('C*', (string) $labels));
    }
}
