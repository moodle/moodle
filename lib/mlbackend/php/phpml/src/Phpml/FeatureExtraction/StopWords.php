<?php

declare(strict_types=1);

namespace Phpml\FeatureExtraction;

use Phpml\Exception\InvalidArgumentException;

class StopWords
{
    /**
     * @var array
     */
    protected $stopWords;

    /**
     * @param array $stopWords
     */
    public function __construct(array $stopWords)
    {
        $this->stopWords = array_fill_keys($stopWords, true);
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    public function isStopWord(string $token): bool
    {
        return isset($this->stopWords[$token]);
    }

    /**
     * @param string $language
     *
     * @return StopWords
     *
     * @throws InvalidArgumentException
     */
    public static function factory($language = 'English'): StopWords
    {
        $className = __NAMESPACE__."\\StopWords\\$language";

        if (!class_exists($className)) {
            throw InvalidArgumentException::invalidStopWordsLanguage($language);
        }

        return new $className();
    }
}
