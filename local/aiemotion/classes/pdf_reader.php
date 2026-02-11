<?php
namespace local_aiemotion\classes;

defined('MOODLE_INTERNAL') || die();

use Smalot\PdfParser\Parser;

class pdf_reader {

    /**
     * Read text from a PDF file
     *
     * @param string $filepath Absolute path to PDF
     * @return string
     */
    public static function read_pdf(string $filepath): string {
        if (!file_exists($filepath)) {
            throw new \moodle_exception('PDF file not found');
        }

        $parser = new Parser();
        $pdf = $parser->parseFile($filepath);

        return trim($pdf->getText());
    }
}
