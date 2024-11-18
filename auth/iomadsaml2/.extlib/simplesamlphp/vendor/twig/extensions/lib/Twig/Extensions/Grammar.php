<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @deprecated since version 1.5
 */
abstract class Twig_Extensions_Grammar implements Twig_Extensions_GrammarInterface
{
    protected $name;
    protected $parser;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param Twig_Parser $parser
     */
    public function setParser(Twig_Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
