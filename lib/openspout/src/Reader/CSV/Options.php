<?php

declare(strict_types=1);

namespace OpenSpout\Reader\CSV;

use OpenSpout\Common\Helper\EncodingHelper;

final class Options
{
    public bool $SHOULD_PRESERVE_EMPTY_ROWS = false;
    public string $FIELD_DELIMITER = ',';
    public string $FIELD_ENCLOSURE = '"';
    public string $ENCODING = EncodingHelper::ENCODING_UTF8;
}
