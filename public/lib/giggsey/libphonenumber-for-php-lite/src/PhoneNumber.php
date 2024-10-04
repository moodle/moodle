<?php

declare(strict_types=1);

namespace libphonenumber;

class PhoneNumber implements \Serializable, \Stringable
{
    /**
     * The country calling code for this number, as defined by the International Telecommunication Union
     * (ITU). For example, this would be 1 for NANPA countries, and 33 for France.
     */
    protected int $countryCode = 0;
    /**
     * National (significant) Number is defined in International Telecommunication Union (ITU)
     * Recommendation E.164. It is a language/country-neutral representation of a phone number at a
     * country level. For countries which have the concept of an "area code" or "national destination
     * code", this is included in the National (significant) Number. Although the ITU says the maximum
     * length should be 15, we have found longer numbers in some countries e.g. Germany.
     *
     * Note that the National (significant) Number does not contain the National(trunk) prefix.
     */
    protected ?string $nationalNumber = null;
    /**
     * Extension is not standardized in ITU recommendations, except for being defined as a series of
     * numbers with a maximum length of 40 digits. It is defined as a string here to accommodate for the
     * possible use of a leading zero in the extension (organizations have complete freedom to do so,
     * as there is no standard defined). However, only ASCII digits should be stored here.
     */
    protected ?string $extension = null;
    /**
     * In some countries, the national (significant) number starts with one or more "0"s without this
     * being a national prefix or trunk code of some kind. For example, the leading zero in the national
     * (significant) number of an Italian phone number indicates the number is a fixed-line number.
     * There have been plans to migrate fixed-line numbers to start with the digit two since December
     * 2000, but it has not happened yet. See http://en.wikipedia.org/wiki/%2B39 for more details.
     *
     * These fields can be safely ignored (there is no need to set them) for most countries. Some
     * limited number of countries behave like Italy - for these cases, if the leading zero(s) of a
     * number would be retained even when dialling internationally, set this flag to true, and also
     * set the number of leading zeros.
     *
     * Clients who use the parsing functionality of the i18n phone number libraries
     * will have these fields set if necessary automatically.
     */
    protected ?bool $italianLeadingZero = null;
    /**
     * This field is used to store the raw input string containing phone numbers before it was
     * canonicalized by the library. For example, it could be used to store alphanumerical numbers
     * such as "1-800-GOOG-411".
     */
    protected ?string $rawInput = null;
    /**
     * The source from which the country_code is derived. This is not set in the general parsing method,
     * but in the method that parses and keeps raw_input. New fields could be added upon request.
     *
     * @see CountryCodeSource
     *
     * This must be one of the CountryCodeSource constants.
     */
    protected int $countryCodeSource = CountryCodeSource::UNSPECIFIED;
    /**
     * The carrier selection code that is preferred when calling this phone number domestically. This
     * also includes codes that need to be dialed in some countries when calling from landlines to
     * mobiles or vice versa. For example, in Columbia, a "3" needs to be dialed before the phone number
     * itself when calling from a mobile phone to a domestic landline phone and vice versa.
     *
     * Note this is the "preferred" code, which means other codes may work as well.
     */
    protected ?string $preferredDomesticCarrierCode = null;
    /**
     * Whether this phone number has a number of leading zeros set.
     */
    protected bool $hasNumberOfLeadingZeros = false;
    /**
     * The number of leading zeros of this phone number.
     */
    protected int $numberOfLeadingZeros = 1;
    private bool $hasCountryCode = false;

    /**
     * Clears this phone number.
     *
     * This effectively resets this phone number to the state of a new instance.
     *
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function clear(): PhoneNumber
    {
        $this->clearCountryCode();
        $this->clearNationalNumber();
        $this->clearExtension();
        $this->clearItalianLeadingZero();
        $this->clearNumberOfLeadingZeros();
        $this->clearRawInput();
        $this->clearCountryCodeSource();
        $this->clearPreferredDomesticCarrierCode();
        return $this;
    }

    /**
     * Clears the country code of this phone number.
     *
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function clearCountryCode(): PhoneNumber
    {
        $this->countryCode = 0;
        $this->hasCountryCode = false;
        return $this;
    }

    /**
     * Clears the national number of this phone number.
     *
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function clearNationalNumber(): PhoneNumber
    {
        $this->nationalNumber = null;
        return $this;
    }

    /**
     * Clears the extension of this phone number.
     *
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function clearExtension(): PhoneNumber
    {
        $this->extension = null;
        return $this;
    }

    /**
     * Clears the italian leading zero information of this phone number.
     *
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function clearItalianLeadingZero(): PhoneNumber
    {
        $this->italianLeadingZero = null;
        return $this;
    }

    /**
     * Clears the number of leading zeros of this phone number.
     *
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function clearNumberOfLeadingZeros(): PhoneNumber
    {
        $this->hasNumberOfLeadingZeros = false;
        $this->numberOfLeadingZeros = 1;
        return $this;
    }

    /**
     * Clears the raw input of this phone number.
     *
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function clearRawInput(): PhoneNumber
    {
        $this->rawInput = null;
        return $this;
    }

    /**
     * Clears the country code source of this phone number.
     *
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function clearCountryCodeSource(): PhoneNumber
    {
        $this->countryCodeSource = CountryCodeSource::UNSPECIFIED;
        return $this;
    }

    /**
     * Clears the preferred domestic carrier code of this phone number.
     *
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function clearPreferredDomesticCarrierCode(): PhoneNumber
    {
        $this->preferredDomesticCarrierCode = null;
        return $this;
    }

    /**
     * Merges the information from another phone number into this phone number.
     *
     * @param PhoneNumber $other The phone number to copy.
     *
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function mergeFrom(PhoneNumber $other): PhoneNumber
    {
        if ($other->hasCountryCode()) {
            $this->setCountryCode($other->getCountryCode());
        }
        if ($other->hasNationalNumber()) {
            $this->setNationalNumber($other->getNationalNumber());
        }
        if ($other->hasExtension()) {
            $this->setExtension($other->getExtension());
        }
        if ($other->hasItalianLeadingZero()) {
            $this->setItalianLeadingZero($other->isItalianLeadingZero());
        }
        if ($other->hasNumberOfLeadingZeros()) {
            $this->setNumberOfLeadingZeros($other->getNumberOfLeadingZeros());
        }
        if ($other->hasRawInput()) {
            $this->setRawInput($other->getRawInput());
        }
        if ($other->hasCountryCodeSource()) {
            $this->setCountryCodeSource($other->getCountryCodeSource());
        }
        if ($other->hasPreferredDomesticCarrierCode()) {
            $this->setPreferredDomesticCarrierCode($other->getPreferredDomesticCarrierCode());
        }
        return $this;
    }

    /**
     * Returns whether this phone number has a country code set.
     *
     * @return bool True if a country code is set, false otherwise.
     */
    public function hasCountryCode(): bool
    {
        return $this->hasCountryCode;
    }

    public function getCountryCode(): int
    {
        return $this->countryCode;
    }

    /**
     * Sets the country code of this phone number.
     *
     * @param int $value The country code.
     *
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function setCountryCode(int $value): PhoneNumber
    {
        $this->hasCountryCode = true;
        $this->countryCode = $value;
        return $this;
    }

    /**
     * Returns whether this phone number has a national number set.
     *
     * @return bool True if a national number is set, false otherwise.
     */
    public function hasNationalNumber(): bool
    {
        return $this->nationalNumber !== null;
    }

    /**
     * Returns the national number of this phone number.
     *
     * @return string|null The national number, or null if not set.
     */
    public function getNationalNumber(): ?string
    {
        return $this->nationalNumber;
    }

    /**
     * Sets the national number of this phone number.
     *
     * @param string $value The national number.
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function setNationalNumber(string $value): PhoneNumber
    {
        $this->nationalNumber = $value;
        return $this;
    }

    /**
     * Returns whether this phone number has an extension set.
     *
     * @return bool True if an extension is set, false otherwise.
     */
    public function hasExtension(): bool
    {
        return $this->extension !== null;
    }

    /**
     * Returns the extension of this phone number.
     *
     * @return string|null The extension, or null if not set.
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * Sets the extension of this phone number.
     *
     * @param string $value The extension.
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function setExtension(string $value): PhoneNumber
    {
        $this->extension = $value;
        return $this;
    }

    /**
     * Returns whether this phone number has the italian leading zero information set.
     */
    public function hasItalianLeadingZero(): bool
    {
        return $this->italianLeadingZero !== null;
    }

    /**
     * Sets whether this phone number uses an italian leading zero.
     *
     * @param bool $value True to use italian leading zero, false otherwise.
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function setItalianLeadingZero(bool $value): PhoneNumber
    {
        $this->italianLeadingZero = $value;
        return $this;
    }

    /**
     * Returns whether this phone number uses an italian leading zero.
     *
     * @return bool|null True if it uses an italian leading zero, false if it does not, null if not set.
     */
    public function isItalianLeadingZero(): ?bool
    {
        return $this->italianLeadingZero;
    }

    /**
     * Returns whether this phone number has a number of leading zeros set.
     *
     * @return bool True if a number of leading zeros is set, false otherwise.
     */
    public function hasNumberOfLeadingZeros(): bool
    {
        return $this->hasNumberOfLeadingZeros;
    }

    /**
     * Returns the number of leading zeros of this phone number.
     *
     * @return int The number of leading zeros.
     */
    public function getNumberOfLeadingZeros(): int
    {
        return $this->numberOfLeadingZeros;
    }

    /**
     * Sets the number of leading zeros of this phone number.
     *
     * @param int $value The number of leading zeros.
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function setNumberOfLeadingZeros(int $value): PhoneNumber
    {
        $this->hasNumberOfLeadingZeros = true;
        $this->numberOfLeadingZeros = $value;
        return $this;
    }

    /**
     * Returns whether this phone number has a raw input.
     *
     * @return bool True if a raw input is set, false otherwise.
     */
    public function hasRawInput(): bool
    {
        return $this->rawInput !== null;
    }

    /**
     * Returns the raw input of this phone number.
     *
     * @return string|null The raw input, or null if not set.
     */
    public function getRawInput(): ?string
    {
        return $this->rawInput;
    }

    /**
     * Sets the raw input of this phone number.
     *
     * @param string $value The raw input.
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function setRawInput(string $value): PhoneNumber
    {
        $this->rawInput = $value;
        return $this;
    }

    /**
     * Returns whether this phone number has a country code source.
     *
     * @return bool True if a country code source is set, false otherwise.
     */
    public function hasCountryCodeSource(): bool
    {
        return $this->countryCodeSource !== CountryCodeSource::UNSPECIFIED;
    }

    /**
     * Returns the country code source of this phone number.
     *
     * @return int|null A CountryCodeSource constant, or null if not set.
     */
    public function getCountryCodeSource(): ?int
    {
        return $this->countryCodeSource;
    }

    /**
     * Sets the country code source of this phone number.
     *
     * @param int $value A CountryCodeSource constant.
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function setCountryCodeSource(int $value): PhoneNumber
    {
        $this->countryCodeSource = $value;
        return $this;
    }

    /**
     * Returns whether this phone number has a preferred domestic carrier code.
     *
     * @return bool True if a preferred domestic carrier code is set, false otherwise.
     */
    public function hasPreferredDomesticCarrierCode(): bool
    {
        return $this->preferredDomesticCarrierCode !== null;
    }

    /**
     * Returns the preferred domestic carrier code of this phone number.
     *
     * @return string|null The preferred domestic carrier code, or null if not set.
     */
    public function getPreferredDomesticCarrierCode(): ?string
    {
        return $this->preferredDomesticCarrierCode;
    }

    /**
     * Sets the preferred domestic carrier code of this phone number.
     *
     * @param string $value The preferred domestic carrier code.
     * @return PhoneNumber This PhoneNumber instance, for chaining method calls.
     */
    public function setPreferredDomesticCarrierCode(string $value): PhoneNumber
    {
        $this->preferredDomesticCarrierCode = $value;
        return $this;
    }

    /**
     * Returns whether this phone number is equal to another.
     *
     * @param PhoneNumber $other The phone number to compare.
     *
     * @return bool True if the phone numbers are equal, false otherwise.
     */
    public function equals(PhoneNumber $other): bool
    {
        if ($this === $other) {
            return true;
        }

        return $this->countryCode === $other->countryCode
            && $this->nationalNumber === $other->nationalNumber
            && $this->extension === $other->extension
            && $this->italianLeadingZero === $other->italianLeadingZero
            && $this->numberOfLeadingZeros === $other->numberOfLeadingZeros
            && $this->rawInput === $other->rawInput
            && $this->countryCodeSource === $other->countryCodeSource
            && $this->preferredDomesticCarrierCode === $other->preferredDomesticCarrierCode;
    }

    /**
     * Returns a string representation of this phone number.
     */
    public function __toString(): string
    {
        $outputString = 'Country Code: ' . $this->countryCode;
        $outputString .= ' National Number: ' . $this->nationalNumber;
        if ($this->hasItalianLeadingZero()) {
            $outputString .= ' Leading Zero(s): true';
        }
        if ($this->hasNumberOfLeadingZeros()) {
            $outputString .= ' Number of leading zeros: ' . $this->numberOfLeadingZeros;
        }
        if ($this->hasExtension()) {
            $outputString .= ' Extension: ' . $this->extension;
        }
        if ($this->hasCountryCodeSource()) {
            $outputString .= ' Country Code Source: ' . $this->countryCodeSource;
        }
        if ($this->hasPreferredDomesticCarrierCode()) {
            $outputString .= ' Preferred Domestic Carrier Code: ' . $this->preferredDomesticCarrierCode;
        }
        return $outputString;
    }

    /**
     *
     */
    public function serialize(): ?string
    {
        return serialize($this->__serialize());
    }

    /**
     * @return array{?int,?string,?string,?bool,int,?string,int,?string}
     */
    public function __serialize(): array
    {
        return [
            $this->countryCode,
            $this->nationalNumber,
            $this->extension,
            $this->italianLeadingZero,
            $this->numberOfLeadingZeros,
            $this->rawInput,
            $this->countryCodeSource,
            $this->preferredDomesticCarrierCode,
        ];
    }

    /**
     *
     */
    public function unserialize($data)
    {
        $this->__unserialize(unserialize($data, ['allowed_classes' => [__CLASS__]]));
    }

    /**
     * @param array{int,?string,?string,?bool,int,?string,int,?string} $data
     */
    public function __unserialize(array $data): void
    {
        [
            $this->countryCode,
            $this->nationalNumber,
            $this->extension,
            $this->italianLeadingZero,
            $this->numberOfLeadingZeros,
            $this->rawInput,
            $this->countryCodeSource,
            $this->preferredDomesticCarrierCode
        ] = $data;

        if ($this->numberOfLeadingZeros > 1) {
            $this->hasNumberOfLeadingZeros = true;
        }
    }
}
