<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX;

use OpenSpout\Common\TempFolderOptionTrait;

final class Options
{
    use TempFolderOptionTrait;

    public bool $SHOULD_FORMAT_DATES = false;
    public bool $SHOULD_PRESERVE_EMPTY_ROWS = false;
    public bool $SHOULD_USE_1904_DATES = false;
    public bool $SHOULD_LOAD_MERGE_CELLS = false;
}
