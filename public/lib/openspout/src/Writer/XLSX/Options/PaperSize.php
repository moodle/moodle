<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Options;

enum PaperSize: int
{
    case Letter = 1;
    case Tabloid = 3;
    case Ledger = 4;
    case Legal = 5;
    case Statement = 6;
    case Executive = 7;
    case A3 = 8;
    case A4 = 9;
    case A5 = 11;
    case B4 = 12;
    case B5 = 13;
    case Folio = 14;
    case Quarto = 15;
    case Standard = 16;
    case Note = 18;
    case C = 24;
    case D = 25;
    case E = 26;
    case DL = 27;
    case C5 = 28;
    case C3 = 29;
    case C4 = 30;
    case C6 = 31;
    case C65 = 32;
    case B6 = 35;
}
