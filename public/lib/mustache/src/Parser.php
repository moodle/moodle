<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2010-2025 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mustache;

use Mustache\Exception\SyntaxException;

/**
 * Mustache Parser class.
 *
 * This class is responsible for turning a set of Mustache tokens into a parse tree.
 */
class Parser
{
    private $lineNum;
    private $lineTokens;
    private $pragmas;
    private $defaultPragmas = [];

    // Optional Mustache specs
    private $dynamicNames = true;
    private $inheritance = true;

    private $pragmaFilters;

    /**
     * Process an array of Mustache tokens and convert them into a parse tree.
     *
     * @param array $tokens Set of Mustache tokens
     *
     * @return array Mustache token parse tree
     */
    public function parse(array $tokens = [])
    {
        $this->lineNum    = -1;
        $this->lineTokens = 0;
        $this->pragmas    = $this->defaultPragmas;

        $this->pragmaFilters = isset($this->pragmas[Engine::PRAGMA_FILTERS]);

        return $this->buildTree($tokens);
    }

    /**
     * Disable optional Mustache specs.
     *
     * @internal Users should set options in Mustache\Engine, not here :)
     *
     * @param bool[] $options
     */
    public function setOptions(array $options)
    {
        if (isset($options['dynamic_names'])) {
            $this->dynamicNames = $options['dynamic_names'] !== false;
        }

        if (isset($options['inheritance'])) {
            $this->inheritance = $options['inheritance'] !== false;
        }
    }

    /**
     * Enable pragmas across all templates, regardless of the presence of pragma
     * tags in the individual templates.
     *
     * @internal Users should set global pragmas in Mustache\Engine, not here :)
     *
     * @param string[] $pragmas
     */
    public function setPragmas(array $pragmas)
    {
        $this->pragmas = [];
        foreach ($pragmas as $pragma) {
            $this->enablePragma($pragma);
        }
        $this->defaultPragmas = $this->pragmas;
    }

    /**
     * Helper method for recursively building a parse tree.
     *
     * @throws SyntaxException when nesting errors or mismatched section tags are encountered
     *
     * @param array &$tokens Set of Mustache tokens
     * @param array $parent  Parent token (default: null)
     *
     * @return array Mustache Token parse tree
     */
    private function buildTree(array &$tokens, $parent = null)
    {
        $nodes = [];

        while (!empty($tokens)) {
            $token = array_shift($tokens);

            if ($token[Tokenizer::LINE] === $this->lineNum) {
                $this->lineTokens++;
            } else {
                $this->lineNum    = $token[Tokenizer::LINE];
                $this->lineTokens = 0;
            }

            if ($token[Tokenizer::TYPE] !== Tokenizer::T_COMMENT) {
                if (isset($token[Tokenizer::NAME])) {
                    list($name, $isDynamic) = $this->getDynamicName($token);
                    if ($isDynamic) {
                        $token[Tokenizer::NAME]    = $name;
                        $token[Tokenizer::DYNAMIC] = true;
                    }
                }

                if ($this->pragmaFilters && isset($token[Tokenizer::NAME])) {
                    list($name, $filters) = $this->getNameAndFilters($token[Tokenizer::NAME]);
                    if (!empty($filters)) {
                        $token[Tokenizer::NAME]    = $name;
                        $token[Tokenizer::FILTERS] = $filters;
                    }
                }
            }

            switch ($token[Tokenizer::TYPE]) {
                case Tokenizer::T_DELIM_CHANGE:
                    $this->checkIfTokenIsAllowedInParent($parent, $token);
                    $this->clearStandaloneLines($nodes, $tokens);
                    break;

                case Tokenizer::T_SECTION:
                case Tokenizer::T_INVERTED:
                    $this->checkIfTokenIsAllowedInParent($parent, $token);
                    $this->clearStandaloneLines($nodes, $tokens);
                    $nodes[] = $this->buildTree($tokens, $token);
                    break;

                case Tokenizer::T_END_SECTION:
                    if (!isset($parent)) {
                        $msg = sprintf(
                            'Unexpected closing tag: /%s on line %d',
                            $token[Tokenizer::NAME],
                            $token[Tokenizer::LINE]
                        );
                        throw new SyntaxException($msg, $token);
                    }

                    $sameName = $token[Tokenizer::NAME] !== $parent[Tokenizer::NAME];
                    $tokenDynamic = isset($token[Tokenizer::DYNAMIC]) && $token[Tokenizer::DYNAMIC];
                    $parentDynamic = isset($parent[Tokenizer::DYNAMIC]) && $parent[Tokenizer::DYNAMIC];

                    if ($sameName || ($tokenDynamic !== $parentDynamic)) {
                        $msg = sprintf(
                            'Nesting error: %s%s (on line %d) vs. %s%s (on line %d)',
                            $parentDynamic ? '*' : '',
                            $parent[Tokenizer::NAME],
                            $parent[Tokenizer::LINE],
                            $tokenDynamic ? '*' : '',
                            $token[Tokenizer::NAME],
                            $token[Tokenizer::LINE]
                        );
                        throw new SyntaxException($msg, $token);
                    }

                    $this->clearStandaloneLines($nodes, $tokens);
                    $parent[Tokenizer::END]   = $token[Tokenizer::INDEX];
                    $parent[Tokenizer::NODES] = $nodes;

                    return $parent;

                case Tokenizer::T_PARTIAL:
                    $this->checkIfTokenIsAllowedInParent($parent, $token);
                    //store the whitespace prefix for laters!
                    if ($indent = $this->clearStandaloneLines($nodes, $tokens)) {
                        $token[Tokenizer::INDENT] = $indent[Tokenizer::VALUE];
                    }
                    $nodes[] = $token;
                    break;

                case Tokenizer::T_PARENT:
                    $this->checkIfTokenIsAllowedInParent($parent, $token);
                    $nodes[] = $this->buildTree($tokens, $token);
                    break;

                case Tokenizer::T_BLOCK_VAR:
                    if ($this->inheritance) {
                        if (isset($parent) && $parent[Tokenizer::TYPE] === Tokenizer::T_PARENT) {
                            $token[Tokenizer::TYPE] = Tokenizer::T_BLOCK_ARG;
                        }
                        $this->clearStandaloneLines($nodes, $tokens);
                        $nodes[] = $this->buildTree($tokens, $token);
                    } else {
                        // pretend this was just a normal "escaped" token...
                        $token[Tokenizer::TYPE] = Tokenizer::T_ESCAPED;
                        // TODO: figure out how to figure out if there was a space after this dollar:
                        $token[Tokenizer::NAME] = '$' . $token[Tokenizer::NAME];
                        $nodes[] = $token;
                    }
                    break;

                case Tokenizer::T_PRAGMA:
                    $this->enablePragma($token[Tokenizer::NAME]);
                    // no break

                case Tokenizer::T_COMMENT:
                    $this->clearStandaloneLines($nodes, $tokens);
                    $nodes[] = $token;
                    break;

                default:
                    $nodes[] = $token;
                    break;
            }
        }

        if (isset($parent)) {
            $msg = sprintf(
                'Missing closing tag: %s opened on line %d',
                $parent[Tokenizer::NAME],
                $parent[Tokenizer::LINE]
            );
            throw new SyntaxException($msg, $parent);
        }

        return $nodes;
    }

    /**
     * Clear standalone line tokens.
     *
     * Returns a whitespace token for indenting partials, if applicable.
     *
     * @param array $nodes  Parsed nodes
     * @param array $tokens Tokens to be parsed
     *
     * @return array|null Resulting indent token, if any
     */
    private function clearStandaloneLines(array &$nodes, array &$tokens)
    {
        if ($this->lineTokens > 1) {
            // this is the third or later node on this line, so it can't be standalone
            return;
        }

        $prev = null;
        if ($this->lineTokens === 1) {
            // this is the second node on this line, so it can't be standalone
            // unless the previous node is whitespace.
            if ($prev = end($nodes)) {
                if (!$this->tokenIsWhitespace($prev)) {
                    return;
                }
            }
        }

        if ($next = reset($tokens)) {
            // If we're on a new line, bail.
            if ($next[Tokenizer::LINE] !== $this->lineNum) {
                return;
            }

            // If the next token isn't whitespace, bail.
            if (!$this->tokenIsWhitespace($next)) {
                return;
            }

            if (count($tokens) !== 1) {
                // Unless it's the last token in the template, the next token
                // must end in newline for this to be standalone.
                if (substr($next[Tokenizer::VALUE], -1) !== "\n") {
                    return;
                }
            }

            // Discard the whitespace suffix
            array_shift($tokens);
        }

        if ($prev) {
            // Return the whitespace prefix, if any
            return array_pop($nodes);
        }
    }

    /**
     * Check whether token is a whitespace token.
     *
     * True if token type is T_TEXT and value is all whitespace characters.
     *
     * @return bool True if token is a whitespace token
     */
    private function tokenIsWhitespace(array $token)
    {
        if ($token[Tokenizer::TYPE] === Tokenizer::T_TEXT) {
            return preg_match('/^\s*$/', $token[Tokenizer::VALUE]);
        }

        return false;
    }

    /**
     * Check whether a token is allowed inside a parent tag.
     *
     * @throws SyntaxException if an invalid token is found inside a parent tag
     *
     * @param array|null $parent
     */
    private function checkIfTokenIsAllowedInParent($parent, array $token)
    {
        if (isset($parent) && $parent[Tokenizer::TYPE] === Tokenizer::T_PARENT) {
            throw new SyntaxException('Illegal content in < parent tag', $token);
        }
    }

    /**
     * Parse dynamic names.
     *
     * @throws SyntaxException when a tag does not allow *
     * @throws SyntaxException on multiple *s, or dots or filters with *
     */
    private function getDynamicName(array $token)
    {
        $name = $token[Tokenizer::NAME];
        $isDynamic = false;

        if ($this->dynamicNames && preg_match('/^\s*\*\s*/', $name)) {
            $this->ensureTagAllowsDynamicNames($token);
            $name = preg_replace('/^\s*\*\s*/', '', $name);
            $isDynamic = true;
        }

        return [$name, $isDynamic];
    }

    /**
     * Check whether the given token supports dynamic tag names.
     *
     * @throws SyntaxException when a tag does not allow *
     */
    private function ensureTagAllowsDynamicNames(array $token)
    {
        switch ($token[Tokenizer::TYPE]) {
            case Tokenizer::T_PARTIAL:
            case Tokenizer::T_PARENT:
            case Tokenizer::T_END_SECTION:
                return;
        }

        $msg = sprintf(
            'Invalid dynamic name: %s in %s tag',
            $token[Tokenizer::NAME],
            Tokenizer::getTagName($token[Tokenizer::TYPE])
        );

        throw new SyntaxException($msg, $token);
    }

    /**
     * Split a tag name into name and filters.
     *
     * @param string $name
     *
     * @return array [Tag name, Array of filters]
     */
    private function getNameAndFilters($name)
    {
        $filters = array_map('trim', explode('|', $name));
        $name    = array_shift($filters);

        return [$name, $filters];
    }

    /**
     * Enable a pragma.
     *
     * @param string $name
     */
    private function enablePragma($name)
    {
        $this->pragmas[$name] = true;

        switch ($name) {
            case Engine::PRAGMA_FILTERS:
                $this->pragmaFilters = true;
                break;
        }
    }
}
