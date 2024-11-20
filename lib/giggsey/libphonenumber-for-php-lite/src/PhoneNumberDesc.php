<?php

declare(strict_types=1);

namespace libphonenumber;

/**
 * Phone Number Description
 * @interal
 * @phpstan-type PhoneNumberDescArray array{NationalNumberPattern?:string,ExampleNumber?:string,PossibleLength?:int[],PossibleLengthLocalOnly?:int[]}
 */
class PhoneNumberDesc
{
    protected bool $hasNationalNumberPattern = false;
    protected string $nationalNumberPattern = '';
    protected bool $hasExampleNumber = false;
    protected string $exampleNumber = '';
    /**
     * @var int[]
     */
    protected array $possibleLength = [];
    /**
     * @var int[]
     */
    protected array $possibleLengthLocalOnly = [];

    public function __construct()
    {
        $this->clear();
    }

    public function clear(): PhoneNumberDesc
    {
        $this->clearNationalNumberPattern();
        $this->clearPossibleLength();
        $this->clearPossibleLengthLocalOnly();
        $this->clearExampleNumber();

        return $this;
    }

    /**
     * @return int[]
     */
    public function getPossibleLength(): array
    {
        return $this->possibleLength;
    }

    /**
     * @param int[] $possibleLength
     */
    public function setPossibleLength(array $possibleLength): void
    {
        $this->possibleLength = $possibleLength;
    }

    public function addPossibleLength(int $possibleLength): void
    {
        if (!in_array($possibleLength, $this->possibleLength)) {
            $this->possibleLength[] = $possibleLength;
        }
    }

    public function clearPossibleLength(): void
    {
        $this->possibleLength = [];
    }

    /**
     * @return int[]
     */
    public function getPossibleLengthLocalOnly(): array
    {
        return $this->possibleLengthLocalOnly;
    }

    /**
     * @param int[] $possibleLengthLocalOnly
     */
    public function setPossibleLengthLocalOnly(array $possibleLengthLocalOnly): void
    {
        $this->possibleLengthLocalOnly = $possibleLengthLocalOnly;
    }

    public function addPossibleLengthLocalOnly(int $possibleLengthLocalOnly): void
    {
        if (!in_array($possibleLengthLocalOnly, $this->possibleLengthLocalOnly)) {
            $this->possibleLengthLocalOnly[] = $possibleLengthLocalOnly;
        }
    }

    public function clearPossibleLengthLocalOnly(): void
    {
        $this->possibleLengthLocalOnly = [];
    }

    public function hasNationalNumberPattern(): bool
    {
        return $this->hasNationalNumberPattern;
    }

    public function getNationalNumberPattern(): string
    {
        return $this->nationalNumberPattern;
    }

    public function setNationalNumberPattern(string $value): PhoneNumberDesc
    {
        $this->hasNationalNumberPattern = true;
        $this->nationalNumberPattern = $value;

        return $this;
    }

    public function clearNationalNumberPattern(): PhoneNumberDesc
    {
        $this->hasNationalNumberPattern = false;
        $this->nationalNumberPattern = '';
        return $this;
    }

    public function hasExampleNumber(): bool
    {
        return $this->hasExampleNumber;
    }

    public function getExampleNumber(): string
    {
        return $this->exampleNumber;
    }

    public function setExampleNumber(string $value): PhoneNumberDesc
    {
        $this->hasExampleNumber = true;
        $this->exampleNumber = $value;

        return $this;
    }

    public function clearExampleNumber(): self
    {
        $this->hasExampleNumber = false;
        $this->exampleNumber = '';

        return $this;
    }

    public function mergeFrom(PhoneNumberDesc $other): self
    {
        if ($other->hasNationalNumberPattern()) {
            $this->setNationalNumberPattern($other->getNationalNumberPattern());
        }
        if ($other->hasExampleNumber()) {
            $this->setExampleNumber($other->getExampleNumber());
        }
        $this->setPossibleLength($other->getPossibleLength());
        $this->setPossibleLengthLocalOnly($other->getPossibleLengthLocalOnly());

        return $this;
    }

    public function exactlySameAs(PhoneNumberDesc $other): bool
    {
        return $this->nationalNumberPattern === $other->nationalNumberPattern &&
        $this->exampleNumber === $other->exampleNumber;
    }

    /**
     * @return PhoneNumberDescArray
     */
    public function toArray(): array
    {
        $data = [];
        if ($this->hasNationalNumberPattern()) {
            $data['NationalNumberPattern'] = $this->getNationalNumberPattern();
        }
        if ($this->hasExampleNumber()) {
            $data['ExampleNumber'] = $this->getExampleNumber();
        }

        $possibleLength = $this->getPossibleLength();
        if (!empty($possibleLength)) {
            $data['PossibleLength'] = $possibleLength;
        }

        $possibleLengthLocalOnly = $this->getPossibleLengthLocalOnly();
        if (!empty($possibleLengthLocalOnly)) {
            $data['PossibleLengthLocalOnly'] = $possibleLengthLocalOnly;
        }

        return $data;
    }

    /**
     * @param PhoneNumberDescArray $input
     * @return $this
     */
    public function fromArray(array $input): static
    {
        if (isset($input['NationalNumberPattern']) && $input['NationalNumberPattern'] !== '') {
            $this->setNationalNumberPattern($input['NationalNumberPattern']);
        }
        if (isset($input['ExampleNumber']) && $input['ExampleNumber'] !== '') {
            $this->setExampleNumber($input['ExampleNumber']);
        }
        if (isset($input['PossibleLength'])) {
            $this->setPossibleLength($input['PossibleLength']);
        }
        if (isset($input['PossibleLengthLocalOnly'])) {
            $this->setPossibleLengthLocalOnly($input['PossibleLengthLocalOnly']);
        }

        return $this;
    }
}
