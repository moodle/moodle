<?php

namespace Gettext\Utils;

class JsFunctionsScanner extends FunctionsScanner
{
    protected $code;
    protected $status = [];

    /**
     * Constructor.
     *
     * @param string $code The php code to scan
     */
    public function __construct($code)
    {
        // Normalize newline characters
        $this->code = str_replace(["\r\n", "\n\r", "\r"], "\n", $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(array $constants = [])
    {
        $length = strlen($this->code);
        $line = 1;
        $buffer = '';
        $functions = [];
        $bufferFunctions = [];
        $char = null;

        for ($pos = 0; $pos < $length; ++$pos) {
            $prev = $char;
            $char = $this->code[$pos];
            $next = isset($this->code[$pos + 1]) ? $this->code[$pos + 1] : null;

            switch ($char) {
                case '\\':
                    switch ($this->status()) {
                        case 'simple-quote':
                            if ($next !== "'") {
                                break 2;
                            }
                            break;

                        case 'double-quote':
                            if ($next !== '"') {
                                break 2;
                            }
                            break;

                        case 'back-tick':
                            if ($next !== '`') {
                                break 2;
                            }
                            break;
                    }

                    $prev = $char;
                    $char = $next;
                    $pos++;
                    $next = isset($this->code[$pos]) ? $this->code[$pos] : null;
                    break;

                case "\n":
                    ++$line;

                    if ($this->status('line-comment')) {
                        $this->upStatus();
                    }
                    break;

                case '/':
                    switch ($this->status()) {
                        case 'simple-quote':
                        case 'double-quote':
                        case 'back-tick':
                        case 'line-comment':
                            break;

                        case 'block-comment':
                            if ($prev === '*') {
                                $this->upStatus();
                            }
                            break;

                        default:
                            if ($next === '/') {
                                $this->downStatus('line-comment');
                            } elseif ($next === '*') {
                                $this->downStatus('block-comment');
                            }
                            break;
                    }
                    break;

                case "'":
                    switch ($this->status()) {
                        case 'simple-quote':
                            $this->upStatus();
                            break;

                        case 'line-comment':
                        case 'block-comment':
                        case 'double-quote':
                        case 'back-tick':
                            break;

                        default:
                            $this->downStatus('simple-quote');
                            break;
                    }
                    break;

                case '"':
                    switch ($this->status()) {
                        case 'double-quote':
                            $this->upStatus();
                            break;

                        case 'line-comment':
                        case 'block-comment':
                        case 'simple-quote':
                        case 'back-tick':
                            break;

                        default:
                            $this->downStatus('double-quote');
                            break;
                    }
                    break;

                case '`':
                    switch ($this->status()) {
                        case 'back-tick':
                            $this->upStatus();
                            break;

                        case 'line-comment':
                        case 'block-comment':
                        case 'simple-quote':
                        case 'double-quote':
                            break;

                        default:
                            $this->downStatus('back-tick');
                            break;
                    }
                    break;

                case '(':
                    switch ($this->status()) {
                        case 'simple-quote':
                        case 'double-quote':
                        case 'back-tick':
                        case 'line-comment':
                        case 'block-comment':
                            break;

                        default:
                            if ($buffer && preg_match('/(\w+)$/', $buffer, $matches)) {
                                $this->downStatus('function');
                                array_unshift($bufferFunctions, [$matches[1], $line, []]);
                                $buffer = '';
                                continue 3;
                            }
                            break;
                    }
                    break;

                case ')':
                    switch ($this->status()) {
                        case 'function':
                            if (($argument = static::prepareArgument($buffer))) {
                                $bufferFunctions[0][2][] = $argument;
                            }

                            if (!empty($bufferFunctions)) {
                                $functions[] = array_shift($bufferFunctions);
                            }

                            $this->upStatus();
                            $buffer = '';
                            continue 3;
                    }
                    break;

                case ',':
                    switch ($this->status()) {
                        case 'function':
                            if (($argument = static::prepareArgument($buffer))) {
                                $bufferFunctions[0][2][] = $argument;
                            }

                            $buffer = '';
                            continue 3;
                    }
                    break;

                case ' ':
                case '\t':
                    switch ($this->status()) {
                        case 'double-quote':
                        case 'simple-quote':
                        case 'back-tick':
                            break;

                        default:
                            $buffer = '';
                            continue 3;
                    }
                    break;
            }

            switch ($this->status()) {
                case 'line-comment':
                case 'block-comment':
                    break;

                default:
                    $buffer .= $char;
                    break;
            }
        }

        return $functions;
    }

    /**
     * Get the current context of the scan.
     *
     * @param null|string $match To check whether the current status is this value
     *
     * @return string|bool
     */
    protected function status($match = null)
    {
        $status = isset($this->status[0]) ? $this->status[0] : null;

        if ($match !== null) {
            return $status === $match;
        }

        return $status;
    }

    /**
     * Add a new status to the stack.
     *
     * @param string $status
     */
    protected function downStatus($status)
    {
        array_unshift($this->status, $status);
    }

    /**
     * Removes and return the current status.
     *
     * @return string|null
     */
    protected function upStatus()
    {
        return array_shift($this->status);
    }

    /**
     * Prepares the arguments found in functions.
     *
     * @param string $argument
     *
     * @return string
     */
    protected static function prepareArgument($argument)
    {
        if ($argument && in_array($argument[0], ['"', "'", '`'], true)) {
            return static::convertString(substr($argument, 1, -1));
        }
    }

    /**
     * Decodes a string with an argument.
     *
     * @param string $value
     *
     * @return string
     */
    protected static function convertString($value)
    {
        if (strpos($value, '\\') === false) {
            return $value;
        }

        return preg_replace_callback(
            '/\\\(n|r|t|v|e|f|"|\\\)/',
            function ($match) {
                switch ($match[1][0]) {
                    case 'n':
                        return "\n";
                    case 'r':
                        return "\r";
                    case 't':
                        return "\t";
                    case 'v':
                        return "\v";
                    case 'e':
                        return "\e";
                    case 'f':
                        return "\f";
                    case '"':
                        return '"';
                    case '\\':
                        return '\\';
                }
            },
            $value
        );
    }
}
