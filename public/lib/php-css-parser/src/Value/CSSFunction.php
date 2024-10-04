<?php

namespace Sabberworm\CSS\Value;

use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parsing\ParserState;

/**
 * A `CSSFunction` represents a special kind of value that also contains a function name and where the values are the
 * function’s arguments. It also handles equals-sign-separated argument lists like `filter: alpha(opacity=90);`.
 */
class CSSFunction extends ValueList
{
    /**
     * @var string
     */
    protected $sName;

    /**
     * @param string $sName
     * @param RuleValueList|array<int, RuleValueList|CSSFunction|CSSString|LineName|Size|URL|string> $aArguments
     * @param string $sSeparator
     * @param int $iLineNo
     */
    public function __construct($sName, $aArguments, $sSeparator = ',', $iLineNo = 0)
    {
        if ($aArguments instanceof RuleValueList) {
            $sSeparator = $aArguments->getListSeparator();
            $aArguments = $aArguments->getListComponents();
        }
        $this->sName = $sName;
        $this->iLineNo = $iLineNo;
        parent::__construct($aArguments, $sSeparator, $iLineNo);
    }

    /**
     * @param ParserState $oParserState
     * @param bool $bIgnoreCase
     *
     * @return CSSFunction
     *
     * @throws SourceException
     * @throws UnexpectedEOFException
     * @throws UnexpectedTokenException
     */
    public static function parse(ParserState $oParserState, $bIgnoreCase = false)
    {
        $mResult = $oParserState->parseIdentifier($bIgnoreCase);
        $oParserState->consume('(');
        $aArguments = Value::parseValue($oParserState, ['=', ' ', ',']);
        $mResult = new CSSFunction($mResult, $aArguments, ',', $oParserState->currentLine());
        $oParserState->consume(')');
        return $mResult;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->sName;
    }

    /**
     * @param string $sName
     *
     * @return void
     */
    public function setName($sName)
    {
        $this->sName = $sName;
    }

    /**
     * @return array<int, RuleValueList|CSSFunction|CSSString|LineName|Size|URL|string>
     */
    public function getArguments()
    {
        return $this->aComponents;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render(new OutputFormat());
    }

    /**
     * @param OutputFormat|null $oOutputFormat
     *
     * @return string
     */
    public function render($oOutputFormat)
    {
        $aArguments = parent::render($oOutputFormat);
        return "{$this->sName}({$aArguments})";
    }
}
