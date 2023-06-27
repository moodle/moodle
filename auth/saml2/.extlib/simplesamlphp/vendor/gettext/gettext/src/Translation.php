<?php

namespace Gettext;

/**
 * Class to manage a translation string.
 */
class Translation
{
    protected $id;
    protected $context;
    protected $original;
    protected $translation = '';
    protected $plural;
    protected $pluralTranslation = [];
    protected $references = [];
    protected $comments = [];
    protected $extractedComments = [];
    protected $flags = [];
    protected $disabled = false;

    /**
     * Generates the id of a translation (context + glue + original).
     *
     * @param string $context
     * @param string $original
     *
     * @return string
     */
    public static function generateId($context, $original)
    {
        return "{$context}\004{$original}";
    }

    /**
     * Create a new instance of a Translation object.
     *
     * This is a factory method that will work even when Translation is extended.
     *
     * @param string $context  The context of the translation
     * @param string $original The original string
     * @param string $plural   The original plural string
     * @return static New Translation instance
     */
    public static function create($context, $original, $plural = '')
    {
        return new static($context, $original, $plural);
    }

    /**
     * Construct.
     *
     * @param string $context  The context of the translation
     * @param string $original The original string
     * @param string $plural   The original plural string
     */
    public function __construct($context, $original, $plural = '')
    {
        $this->context = (string) $context;
        $this->original = (string) $original;

        $this->setPlural($plural);
    }

    /**
     * Clones this translation.
     *
     * @param null|string $context  Optional new context
     * @param null|string $original Optional new original
     *
     * @return Translation
     */
    public function getClone($context = null, $original = null)
    {
        $new = clone $this;

        if ($context !== null) {
            $new->context = (string) $context;
        }

        if ($original !== null) {
            $new->original = (string) $original;
        }

        return $new;
    }

    /**
     * Sets the id of this translation.
     * @warning The use of this function to set a custom ID will prevent
     *  Translations::find from matching this translation.
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * Returns the id of this translation.
     *
     * @return string
     */
    public function getId()
    {
        if ($this->id === null) {
            return static::generateId($this->context, $this->original);
        }
        return $this->id;
    }

    /**
     * Checks whether the translation matches with the arguments.
     *
     * @param string $context
     * @param string $original
     *
     * @return bool
     */
    public function is($context, $original = '')
    {
        return (($this->context === $context) && ($this->original === $original)) ? true : false;
    }

    /**
     * Enable or disable the translation
     *
     * @param bool $disabled
     *
     * @return self
     */
    public function setDisabled($disabled)
    {
        $this->disabled = (bool) $disabled;

        return $this;
    }

    /**
     * Returns whether the translation is disabled
     *
     * @return bool
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * Gets the original string.
     *
     * @return string
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Checks if the original string is empty or not.
     *
     * @return bool
     */
    public function hasOriginal()
    {
        return ($this->original !== '') ? true : false;
    }

    /**
     * Sets the translation string.
     *
     * @param string $translation
     *
     * @return self
     */
    public function setTranslation($translation)
    {
        $this->translation = (string) $translation;

        return $this;
    }

    /**
     * Gets the translation string.
     *
     * @return string
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * Checks if the translation string is empty or not.
     *
     * @return bool
     */
    public function hasTranslation()
    {
        return ($this->translation !== '') ? true : false;
    }

    /**
     * Sets the plural translation string.
     *
     * @param string $plural
     *
     * @return self
     */
    public function setPlural($plural)
    {
        $this->plural = (string) $plural;

        return $this;
    }

    /**
     * Gets the plural translation string.
     *
     * @return string
     */
    public function getPlural()
    {
        return $this->plural;
    }

    /**
     * Checks if the plural translation string is empty or not.
     *
     * @return bool
     */
    public function hasPlural()
    {
        return ($this->plural !== '') ? true : false;
    }

    /**
     * Set a new plural translation.
     *
     * @param array $plural
     *
     * @return self
     */
    public function setPluralTranslations(array $plural)
    {
        $this->pluralTranslation = $plural;

        return $this;
    }

    /**
     * Gets all plural translations.
     *
     * @param int $size
     *
     * @return array
     */
    public function getPluralTranslations($size = null)
    {
        if ($size === null) {
            return $this->pluralTranslation;
        }

        $current = count($this->pluralTranslation);

        if ($size > $current) {
            return $this->pluralTranslation + array_fill(0, $size, '');
        }

        if ($size < $current) {
            return array_slice($this->pluralTranslation, 0, $size);
        }

        return $this->pluralTranslation;
    }

    /**
     * Checks if there are any plural translation.
     *
     * @param bool $checkContent
     *
     * @return bool
     */
    public function hasPluralTranslations($checkContent = false)
    {
        if ($checkContent) {
            return implode('', $this->pluralTranslation) !== '';
        }

        return !empty($this->pluralTranslation);
    }

    /**
     * Removes all plural translations.
     *
     * @return self
     */
    public function deletePluralTranslation()
    {
        $this->pluralTranslation = [];

        return $this;
    }

    /**
     * Gets the context of this translation.
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Checks if the context is empty or not.
     *
     * @return bool
     */
    public function hasContext()
    {
        return (isset($this->context) && ($this->context !== '')) ? true : false;
    }

    /**
     * Adds a new reference for this translation.
     *
     * @param string   $filename The file path where the translation has been found
     * @param null|int $line     The line number where the translation has been found
     *
     * @return self
     */
    public function addReference($filename, $line = null)
    {
        $key = "{$filename}:{$line}";
        $this->references[$key] = [$filename, $line];

        return $this;
    }

    /**
     * Checks if the translation has any reference.
     *
     * @return bool
     */
    public function hasReferences()
    {
        return !empty($this->references);
    }

    /**
     * Return all references for this translation.
     *
     * @return array
     */
    public function getReferences()
    {
        return array_values($this->references);
    }

    /**
     * Removes all references.
     *
     * @return self
     */
    public function deleteReferences()
    {
        $this->references = [];

        return $this;
    }

    /**
     * Adds a new comment for this translation.
     *
     * @param string $comment
     *
     * @return self
     */
    public function addComment($comment)
    {
        if (!in_array($comment, $this->comments, true)) {
            $this->comments[] = $comment;
        }

        return $this;
    }

    /**
     * Checks if the translation has any comment.
     *
     * @return bool
     */
    public function hasComments()
    {
        return isset($this->comments[0]);
    }

    /**
     * Returns all comments for this translation.
     *
     * @return array
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Removes all comments.
     *
     * @return self
     */
    public function deleteComments()
    {
        $this->comments = [];

        return $this;
    }

    /**
     * Adds a new extracted comment for this translation.
     *
     * @param string $comment
     *
     * @return self
     */
    public function addExtractedComment($comment)
    {
        if (!in_array($comment, $this->extractedComments, true)) {
            $this->extractedComments[] = $comment;
        }

        return $this;
    }

    /**
     * Checks if the translation has any extracted comment.
     *
     * @return bool
     */
    public function hasExtractedComments()
    {
        return isset($this->extractedComments[0]);
    }

    /**
     * Returns all extracted comments for this translation.
     *
     * @return array
     */
    public function getExtractedComments()
    {
        return $this->extractedComments;
    }

    /**
     * Removes all extracted comments.
     *
     * @return self
     */
    public function deleteExtractedComments()
    {
        $this->extractedComments = [];

        return $this;
    }

    /**
     * Adds a new flag for this translation.
     *
     * @param string $flag
     *
     * @return self
     */
    public function addFlag($flag)
    {
        if (!in_array($flag, $this->flags, true)) {
            $this->flags[] = $flag;
        }

        return $this;
    }

    /**
     * Checks if the translation has any flag.
     *
     * @return bool
     */
    public function hasFlags()
    {
        return isset($this->flags[0]);
    }

    /**
     * Returns all extracted flags for this translation.
     *
     * @return array
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Removes all flags.
     *
     * @return self
     */
    public function deleteFlags()
    {
        $this->flags = [];

        return $this;
    }

    /**
     * Merges this translation with other translation.
     *
     * @param Translation $translation The translation to merge with
     * @param int         $options
     *
     * @return self
     */
    public function mergeWith(Translation $translation, $options = Merge::DEFAULTS)
    {
        Merge::mergeTranslation($translation, $this, $options);
        Merge::mergeReferences($translation, $this, $options);
        Merge::mergeComments($translation, $this, $options);
        Merge::mergeExtractedComments($translation, $this, $options);
        Merge::mergeFlags($translation, $this, $options);

        return $this;
    }
}
