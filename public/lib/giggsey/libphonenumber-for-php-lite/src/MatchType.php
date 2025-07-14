<?php

namespace libphonenumber;

/**
 * Types of phone number matches
 * See detailed description beside the isNumberMatch() method
 */
class MatchType
{
    public const NOT_A_NUMBER = 0;
    public const NO_MATCH = 1;
    public const SHORT_NSN_MATCH = 2;
    public const NSN_MATCH = 3;
    public const EXACT_MATCH = 4;
}
