<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Manager;

use OpenSpout\Common\Entity\Comment\Comment;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Helper\Escaper;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Helper\CellHelper;

/**
 * @internal
 *
 * This manager takes care of comments: writing them into two files:
 *  - commentsX.xml, containing the actual (rich) text of the comment
 *  - drawings/drawingX.vml, containing the layout of the panel showing the comment
 *
 * Each worksheet gets its unique set of 2 files, this class will make sure that these
 * files are created, closed and filled with the required data.
 */
final class CommentsManager
{
    public const COMMENTS_XML_FILE_HEADER = <<<'EOD'
        <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <comments xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
            <authors><author>Unknown</author></authors>
            <commentList>
        EOD;

    public const COMMENTS_XML_FILE_FOOTER = <<<'EOD'
            </commentList>
        </comments>
        EOD;

    public const DRAWINGS_VML_FILE_HEADER = <<<'EOD'
        <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <xml xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
          <o:shapelayout v:ext="edit">
            <o:idmap v:ext="edit" data="1"/>
          </o:shapelayout>
          <v:shapetype id="_x0000_t202" coordsize="21600,21600" o:spt="202" path="m,l,21600r21600,l21600,xe">
            <v:stroke joinstyle="miter"/>
            <v:path gradientshapeok="t" o:connecttype="rect"/>
          </v:shapetype>
        EOD;

    public const DRAWINGS_VML_FILE_FOOTER = <<<'EOD'
        </xml>
        EOD;

    /**
     * File-pointers to the commentsX.xml files, where the index is the id of the worksheet.
     *
     * @var resource[]
     */
    private array $commentsFilePointers = [];

    /**
     * File-pointers to the vmlDrawingX.vml files, where the index is the id of the worksheet.
     *
     * @var resource[]
     */
    private array $drawingFilePointers = [];

    private string $xlFolder;

    private int $shapeId = 1024;

    private Escaper\XLSX $stringsEscaper;

    /**
     * @param string $xlFolder Path to the "xl" folder
     */
    public function __construct(string $xlFolder, Escaper\XLSX $stringsEscaper)
    {
        $this->xlFolder = $xlFolder;
        $this->stringsEscaper = $stringsEscaper;
    }

    /**
     * Create the two comment-files for the given worksheet.
     */
    public function createWorksheetCommentFiles(Worksheet $sheet): void
    {
        $sheetId = $sheet->getId();
        $commentFp = fopen($this->getCommentsFilePath($sheet), 'w');
        \assert(false !== $commentFp);

        $drawingFp = fopen($this->getDrawingFilePath($sheet), 'w');
        \assert(false !== $drawingFp);

        fwrite($commentFp, self::COMMENTS_XML_FILE_HEADER);
        fwrite($drawingFp, self::DRAWINGS_VML_FILE_HEADER);

        $this->commentsFilePointers[$sheetId] = $commentFp;
        $this->drawingFilePointers[$sheetId] = $drawingFp;
    }

    /**
     * Close the two comment-files for the given worksheet.
     */
    public function closeWorksheetCommentFiles(Worksheet $sheet): void
    {
        $sheetId = $sheet->getId();

        $commentFp = $this->commentsFilePointers[$sheetId];
        $drawingFp = $this->drawingFilePointers[$sheetId];

        fwrite($commentFp, self::COMMENTS_XML_FILE_FOOTER);
        fwrite($drawingFp, self::DRAWINGS_VML_FILE_FOOTER);

        fclose($commentFp);
        fclose($drawingFp);
    }

    public function addComments(Worksheet $worksheet, Row $row): void
    {
        $rowIndexZeroBased = 0 + $worksheet->getLastWrittenRowIndex();
        foreach ($row->getCells() as $columnIndexZeroBased => $cell) {
            if (null === $cell->comment) {
                continue;
            }

            $this->addXmlComment($worksheet->getId(), $rowIndexZeroBased, $columnIndexZeroBased, $cell->comment);
            $this->addVmlComment($worksheet->getId(), $rowIndexZeroBased, $columnIndexZeroBased, $cell->comment);
        }
    }

    /**
     * @return string The file path where the comments for the given sheet will be stored
     */
    private function getCommentsFilePath(Worksheet $sheet): string
    {
        return $this->xlFolder.\DIRECTORY_SEPARATOR.'comments'.$sheet->getId().'.xml';
    }

    /**
     * @return string The file path where the VML comments for the given sheet will be stored
     */
    private function getDrawingFilePath(Worksheet $sheet): string
    {
        return $this->xlFolder.\DIRECTORY_SEPARATOR.'drawings'.\DIRECTORY_SEPARATOR.'vmlDrawing'.$sheet->getId().'.vml';
    }

    /**
     * Add a comment to the commentsX.xml file.
     *
     * @param int     $sheetId              The id of the sheet (starting with 1)
     * @param int     $rowIndexZeroBased    The row index, starting at 0, of the cell with the comment
     * @param int     $columnIndexZeroBased The column index, starting at 0, of the cell with the comment
     * @param Comment $comment              The actual comment
     */
    private function addXmlComment(int $sheetId, int $rowIndexZeroBased, int $columnIndexZeroBased, Comment $comment): void
    {
        $commentsFilePointer = $this->commentsFilePointers[$sheetId];
        $rowIndexOneBased = $rowIndexZeroBased + 1;
        $columnLetters = CellHelper::getColumnLettersFromColumnIndex($columnIndexZeroBased);

        $commentxml = '<comment ref="'.$columnLetters.$rowIndexOneBased.'" authorId="0"><text>';
        foreach ($comment->getTextRuns() as $line) {
            $commentxml .= '<r>';
            $commentxml .= '  <rPr>';
            if ($line->bold) {
                $commentxml .= '    <b/>';
            }
            if ($line->italic) {
                $commentxml .= '    <i/>';
            }
            $commentxml .= '    <sz val="'.$line->fontSize.'"/>';
            $commentxml .= '    <color rgb="'.$line->fontColor.'"/>';
            $commentxml .= '    <rFont val="'.$line->fontName.'"/>';
            $commentxml .= '    <family val="2"/>';
            $commentxml .= '  </rPr>';
            $commentxml .= '  <t xml:space="preserve">'.$this->stringsEscaper->escape($line->text).'</t>';
            $commentxml .= '</r>';
        }
        $commentxml .= '</text></comment>';

        fwrite($commentsFilePointer, $commentxml);
    }

    /**
     * Add a comment to the vmlDrawingX.vml file.
     *
     * @param int     $sheetId              The id of the sheet (starting with 1)
     * @param int     $rowIndexZeroBased    The row index, starting at 0, of the cell with the comment
     * @param int     $columnIndexZeroBased The column index, starting at 0, of the cell with the comment
     * @param Comment $comment              The actual comment
     */
    private function addVmlComment(int $sheetId, int $rowIndexZeroBased, int $columnIndexZeroBased, Comment $comment): void
    {
        $drawingFilePointer = $this->drawingFilePointers[$sheetId];
        ++$this->shapeId;

        $style = 'position:absolute;z-index:1';
        $style .= ';margin-left:'.$comment->marginLeft;
        $style .= ';margin-top:'.$comment->marginTop;
        $style .= ';width:'.$comment->width;
        $style .= ';height:'.$comment->height;
        if (!$comment->visible) {
            $style .= ';visibility:hidden';
        }

        $drawingVml = '<v:shape id="_x0000_s'.$this->shapeId.'"';
        $drawingVml .= ' type="#_x0000_t202" style="'.$style.'" fillcolor="'.$comment->fillColor.'" o:insetmode="auto">';
        $drawingVml .= '<v:fill color2="'.$comment->fillColor.'"/>';
        $drawingVml .= '<v:shadow on="t" color="black" obscured="t"/>';
        $drawingVml .= '<v:path o:connecttype="none"/>';
        $drawingVml .= '<v:textbox style="mso-direction-alt:auto">';
        $drawingVml .= '  <div style="text-align:left"/>';
        $drawingVml .= '</v:textbox>';
        $drawingVml .= '<x:ClientData ObjectType="Note">';
        $drawingVml .= '  <x:MoveWithCells/>';
        $drawingVml .= '  <x:SizeWithCells/>';
        $drawingVml .= '  <x:AutoFill>False</x:AutoFill>';
        $drawingVml .= '  <x:Row>'.$rowIndexZeroBased.'</x:Row>';
        $drawingVml .= '  <x:Column>'.$columnIndexZeroBased.'</x:Column>';
        $drawingVml .= '</x:ClientData>';
        $drawingVml .= '</v:shape>';

        fwrite($drawingFilePointer, $drawingVml);
    }
}
