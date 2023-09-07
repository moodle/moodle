<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace OTPHP;

use Assert\Assertion;
use ParagonIE\ConstantTime\Base32;

trait ParameterTrait
{
    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var string|null
     */
    private $issuer = null;

    /**
     * @var string|null
     */
    private $label = null;

    /**
     * @var bool
     */
    private $issuer_included_as_parameter = true;

    /**
     * @return array
     */
    public function getParameters(): array
    {
        $parameters = $this->parameters;

        if (null !== $this->getIssuer() && $this->isIssuerIncludedAsParameter() === true) {
            $parameters['issuer'] = $this->getIssuer();
        }

        return $parameters;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->getParameter('secret');
    }

    /**
     * @param string|null $secret
     */
    private function setSecret($secret)
    {
        $this->setParameter('secret', $secret);
    }

    /**
     * @return string|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label)
    {
        $this->setParameter('label', $label);
    }

    /**
     * @return string|null
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * @param string $issuer
     */
    public function setIssuer(string $issuer)
    {
        $this->setParameter('issuer', $issuer);
    }

    /**
     * @return bool
     */
    public function isIssuerIncludedAsParameter(): bool
    {
        return $this->issuer_included_as_parameter;
    }

    /**
     * @param bool $issuer_included_as_parameter
     */
    public function setIssuerIncludedAsParameter(bool $issuer_included_as_parameter)
    {
        $this->issuer_included_as_parameter = $issuer_included_as_parameter;
    }

    /**
     * @return int
     */
    public function getDigits(): int
    {
        return $this->getParameter('digits');
    }

    /**
     * @param int $digits
     */
    private function setDigits(int $digits)
    {
        $this->setParameter('digits', $digits);
    }

    /**
     * @return string
     */
    public function getDigest(): string
    {
        return $this->getParameter('algorithm');
    }

    /**
     * @param string $digest
     */
    private function setDigest(string $digest)
    {
        $this->setParameter('algorithm', $digest);
    }

    /**
     * @param string $parameter
     *
     * @return bool
     */
    public function hasParameter(string $parameter): bool
    {
        return array_key_exists($parameter, $this->parameters);
    }

    /**
     * @param string $parameter
     *
     * @return mixed
     */
    public function getParameter(string $parameter)
    {
        if ($this->hasParameter($parameter)) {
            return $this->getParameters()[$parameter];
        }

        throw new \InvalidArgumentException(sprintf('Parameter "%s" does not exist', $parameter));
    }

    /**
     * @param string $parameter
     * @param mixed  $value
     */
    public function setParameter(string $parameter, $value)
    {
        $map = $this->getParameterMap();

        if (true === array_key_exists($parameter, $map)) {
            $callback = $map[$parameter];
            $value = $callback($value);
        }

        if (property_exists($this, $parameter)) {
            $this->$parameter = $value;
        } else {
            $this->parameters[$parameter] = $value;
        }
    }

    /**
     * @return array
     */
    protected function getParameterMap(): array
    {
        return [
            'label'     => function ($value) {
                Assertion::false($this->hasColon($value), 'Label must not contain a colon.');

                return $value;
            },
            'secret'    => function ($value) {
                if (null === $value) {
                    $value = Base32::encodeUpper(random_bytes(64));
                }
                $value = trim(strtoupper($value), '=');

                return $value;
            },
            'algorithm' => function ($value) {
                $value = strtolower($value);
                Assertion::inArray($value, hash_algos(), sprintf('The "%s" digest is not supported.', $value));

                return $value;
            },
            'digits'    => function ($value) {
                Assertion::greaterThan($value, 0, 'Digits must be at least 1.');

                return (int) $value;
            },
            'issuer'    => function ($value) {
                Assertion::false($this->hasColon($value), 'Issuer must not contain a colon.');

                return $value;
            },
        ];
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function hasColon($value)
    {
        $colons = [':', '%3A', '%3a'];
        foreach ($colons as $colon) {
            if (false !== strpos($value, $colon)) {
                return true;
            }
        }

        return false;
    }
}
