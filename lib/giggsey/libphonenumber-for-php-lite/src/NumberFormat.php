<?php

declare(strict_types=1);

namespace libphonenumber;

/**
 * Number Format
 * @interal
 * @phpstan-type NumberFormatArray array{pattern:string|null,format:string|null,leadingDigitsPatterns:array<string>,nationalPrefixFormattingRule?:string,domesticCarrierCodeFormattingRule?:string,nationalPrefixOptionalWhenFormatting?:bool}
 */
class NumberFormat
{
    protected ?string $pattern;
    protected bool $hasPattern = false;
    protected ?string $format;
    protected bool $hasFormat = false;
    /**
     * @var array<string>
     */
    protected array $leadingDigitsPattern = [];
    protected string $nationalPrefixFormattingRule = '';
    protected bool $hasNationalPrefixFormattingRule = false;
    protected bool $nationalPrefixOptionalWhenFormatting = false;
    protected bool $hasNationalPrefixOptionalWhenFormatting = false;
    protected string $domesticCarrierCodeFormattingRule = '';
    protected bool $hasDomesticCarrierCodeFormattingRule = false;

    public function __construct()
    {
        $this->clear();
    }

    /**
     */
    public function clear(): NumberFormat
    {
        $this->hasPattern = false;
        $this->pattern = null;

        $this->hasFormat = false;
        $this->format = null;

        $this->leadingDigitsPattern = [];

        $this->hasNationalPrefixFormattingRule = false;
        $this->nationalPrefixFormattingRule = '';

        $this->hasNationalPrefixOptionalWhenFormatting = false;
        $this->nationalPrefixOptionalWhenFormatting = false;

        $this->hasDomesticCarrierCodeFormattingRule = false;
        $this->domesticCarrierCodeFormattingRule = '';

        return $this;
    }

    public function hasPattern(): bool
    {
        return $this->hasPattern;
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    public function setPattern(string $value): NumberFormat
    {
        $this->hasPattern = true;
        $this->pattern = $value;

        return $this;
    }

    public function hasNationalPrefixOptionalWhenFormatting(): bool
    {
        return $this->hasNationalPrefixOptionalWhenFormatting;
    }

    public function getNationalPrefixOptionalWhenFormatting(): bool
    {
        return $this->nationalPrefixOptionalWhenFormatting;
    }

    public function setNationalPrefixOptionalWhenFormatting(bool $nationalPrefixOptionalWhenFormatting): void
    {
        $this->hasNationalPrefixOptionalWhenFormatting = true;
        $this->nationalPrefixOptionalWhenFormatting = $nationalPrefixOptionalWhenFormatting;
    }

    public function hasFormat(): bool
    {
        return $this->hasFormat;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $value): NumberFormat
    {
        $this->hasFormat = true;
        $this->format = $value;

        return $this;
    }

    /**
     * @return string[]
     */
    public function leadingDigitPatterns(): array
    {
        return $this->leadingDigitsPattern;
    }

    public function leadingDigitsPatternSize(): int
    {
        return count($this->leadingDigitsPattern);
    }

    public function getLeadingDigitsPattern(int $index): string
    {
        return $this->leadingDigitsPattern[$index];
    }

    public function addLeadingDigitsPattern(string $value): NumberFormat
    {
        $this->leadingDigitsPattern[] = $value;

        return $this;
    }

    public function hasNationalPrefixFormattingRule(): bool
    {
        return $this->hasNationalPrefixFormattingRule;
    }

    public function getNationalPrefixFormattingRule(): string
    {
        return $this->nationalPrefixFormattingRule;
    }

    public function setNationalPrefixFormattingRule(string $value): NumberFormat
    {
        $this->hasNationalPrefixFormattingRule = true;
        $this->nationalPrefixFormattingRule = $value;

        return $this;
    }

    public function clearNationalPrefixFormattingRule(): NumberFormat
    {
        $this->nationalPrefixFormattingRule = '';

        return $this;
    }

    public function hasDomesticCarrierCodeFormattingRule(): bool
    {
        return $this->hasDomesticCarrierCodeFormattingRule;
    }

    public function getDomesticCarrierCodeFormattingRule(): string
    {
        return $this->domesticCarrierCodeFormattingRule;
    }

    public function setDomesticCarrierCodeFormattingRule(string $value): NumberFormat
    {
        $this->hasDomesticCarrierCodeFormattingRule = true;
        $this->domesticCarrierCodeFormattingRule = $value;

        return $this;
    }

    public function mergeFrom(NumberFormat $other): NumberFormat
    {
        if ($other->hasPattern()) {
            $this->setPattern($other->getPattern());
        }
        if ($other->hasFormat()) {
            $this->setFormat($other->getFormat());
        }
        $leadingDigitsPatternSize = $other->leadingDigitsPatternSize();
        for ($i = 0; $i < $leadingDigitsPatternSize; $i++) {
            $this->addLeadingDigitsPattern($other->getLeadingDigitsPattern($i));
        }
        if ($other->hasNationalPrefixFormattingRule()) {
            $this->setNationalPrefixFormattingRule($other->getNationalPrefixFormattingRule());
        }
        if ($other->hasDomesticCarrierCodeFormattingRule()) {
            $this->setDomesticCarrierCodeFormattingRule($other->getDomesticCarrierCodeFormattingRule());
        }
        if ($other->hasNationalPrefixOptionalWhenFormatting()) {
            $this->setNationalPrefixOptionalWhenFormatting($other->getNationalPrefixOptionalWhenFormatting());
        }

        return $this;
    }

    /**
     * @internal
     * @return NumberFormatArray
     */
    public function toArray(): array
    {
        $output = [];
        $output['pattern'] = $this->getPattern();
        $output['format'] = $this->getFormat();

        $output['leadingDigitsPatterns'] = $this->leadingDigitPatterns();

        if ($this->hasNationalPrefixFormattingRule()) {
            $output['nationalPrefixFormattingRule'] = $this->getNationalPrefixFormattingRule();
        }

        if ($this->hasDomesticCarrierCodeFormattingRule()) {
            $output['domesticCarrierCodeFormattingRule'] = $this->getDomesticCarrierCodeFormattingRule();
        }

        if ($this->hasNationalPrefixOptionalWhenFormatting() && $this->getNationalPrefixOptionalWhenFormatting() !== false) {
            $output['nationalPrefixOptionalWhenFormatting'] = $this->getNationalPrefixOptionalWhenFormatting();
        }

        return $output;
    }

    /**
     * @internal
     * @param NumberFormatArray $input
     */
    public function fromArray(array $input): void
    {
        $this->setPattern($input['pattern']);
        $this->setFormat($input['format']);
        foreach ($input['leadingDigitsPatterns'] as $leadingDigitsPattern) {
            $this->addLeadingDigitsPattern($leadingDigitsPattern);
        }

        if (isset($input['nationalPrefixFormattingRule']) && $input['nationalPrefixFormattingRule'] !== '') {
            $this->setNationalPrefixFormattingRule($input['nationalPrefixFormattingRule']);
        }
        if (isset($input['domesticCarrierCodeFormattingRule']) && $input['domesticCarrierCodeFormattingRule'] !== '') {
            $this->setDomesticCarrierCodeFormattingRule($input['domesticCarrierCodeFormattingRule']);
        }

        if (isset($input['nationalPrefixOptionalWhenFormatting'])) {
            $this->setNationalPrefixOptionalWhenFormatting($input['nationalPrefixOptionalWhenFormatting']);
        }
    }
}
