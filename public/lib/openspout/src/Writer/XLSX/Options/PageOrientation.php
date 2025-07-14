<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Options;

enum PageOrientation: string
{
    case PORTRAIT = 'portrait';
    case LANDSCAPE = 'landscape';
}
