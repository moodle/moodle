<?php

namespace Gettext;

/**
 * Static class with merge contants.
 */
final class Merge
{
    const ADD = 1;
    const REMOVE = 2;

    const HEADERS_ADD = 4;
    const HEADERS_REMOVE = 8;
    const HEADERS_OVERRIDE = 16;

    const LANGUAGE_OVERRIDE = 32;
    const DOMAIN_OVERRIDE = 64;
    const TRANSLATION_OVERRIDE = 128;

    const COMMENTS_OURS = 256;
    const COMMENTS_THEIRS = 512;

    const EXTRACTED_COMMENTS_OURS = 1024;
    const EXTRACTED_COMMENTS_THEIRS = 2048;

    const FLAGS_OURS = 4096;
    const FLAGS_THEIRS = 8192;

    const REFERENCES_OURS = 16384;
    const REFERENCES_THEIRS = 32768;

    const DEFAULTS = 5; //1 + 4

    /**
     * Merge the flags of two translations.
     *
     * @param Translation $from
     * @param Translation $to
     * @param int         $options
     */
    public static function mergeFlags(Translation $from, Translation $to, $options = self::DEFAULTS)
    {
        if ($options & self::FLAGS_THEIRS) {
            $to->deleteFlags();
        }

        if (!($options & self::FLAGS_OURS)) {
            foreach ($from->getFlags() as $flag) {
                $to->addFlag($flag);
            }
        }
    }

    /**
     * Merge the extracted comments of two translations.
     *
     * @param Translation $from
     * @param Translation $to
     * @param int         $options
     */
    public static function mergeExtractedComments(Translation $from, Translation $to, $options = self::DEFAULTS)
    {
        if ($options & self::EXTRACTED_COMMENTS_THEIRS) {
            $to->deleteExtractedComments();
        }

        if (!($options & self::EXTRACTED_COMMENTS_OURS)) {
            foreach ($from->getExtractedComments() as $comment) {
                $to->addExtractedComment($comment);
            }
        }
    }

    /**
     * Merge the comments of two translations.
     *
     * @param Translation $from
     * @param Translation $to
     * @param int         $options
     */
    public static function mergeComments(Translation $from, Translation $to, $options = self::DEFAULTS)
    {
        if ($options & self::COMMENTS_THEIRS) {
            $to->deleteComments();
        }

        if (!($options & self::COMMENTS_OURS)) {
            foreach ($from->getComments() as $comment) {
                $to->addComment($comment);
            }
        }
    }

    /**
     * Merge the references of two translations.
     *
     * @param Translation $from
     * @param Translation $to
     * @param int         $options
     */
    public static function mergeReferences(Translation $from, Translation $to, $options = self::DEFAULTS)
    {
        if ($options & self::REFERENCES_THEIRS) {
            $to->deleteReferences();
        }

        if (!($options & self::REFERENCES_OURS)) {
            foreach ($from->getReferences() as $reference) {
                $to->addReference($reference[0], $reference[1]);
            }
        }
    }

    /**
     * Merge the translations of two translations.
     *
     * @param Translation $from
     * @param Translation $to
     * @param int         $options
     */
    public static function mergeTranslation(Translation $from, Translation $to, $options = self::DEFAULTS)
    {
        $override = (boolean) ($options & self::TRANSLATION_OVERRIDE);

        if (!$to->hasTranslation() || ($from->hasTranslation() && $override)) {
            $to->setTranslation($from->getTranslation());
        }

        if (!$to->hasPlural() || ($from->hasPlural() && $override)) {
            $to->setPlural($from->getPlural());
        }

        if (!$to->hasPluralTranslations() || ($from->hasPluralTranslations() && $override)) {
            $to->setPluralTranslations($from->getPluralTranslations());
        }
    }

    /**
     * Merge the translations of two translations.
     *
     * @param Translations $from
     * @param Translations $to
     * @param int          $options
     */
    public static function mergeTranslations(Translations $from, Translations $to, $options = self::DEFAULTS)
    {
        if ($options & self::REMOVE) {
            $filtered = [];

            foreach ($to as $entry) {
                if ($from->find($entry)) {
                    $filtered[$entry->getId()] = $entry;
                }
            }

            $to->exchangeArray($filtered);
        }

        foreach ($from as $entry) {
            if (($existing = $to->find($entry))) {
                $existing->mergeWith($entry, $options);
            } elseif ($options & self::ADD) {
                $to[] = $entry->getClone();
            }
        }
    }

    /**
     * Merge the headers of two translations.
     *
     * @param Translations $from
     * @param Translations $to
     * @param int          $options
     */
    public static function mergeHeaders(Translations $from, Translations $to, $options = self::DEFAULTS)
    {
        if ($options & self::HEADERS_REMOVE) {
            foreach (array_keys($to->getHeaders()) as $name) {
                if ($from->getHeader($name) === null) {
                    $to->deleteHeader($name);
                }
            }
        }

        foreach ($from->getHeaders() as $name => $value) {
            $current = $to->getHeader($name);

            if (empty($current)) {
                if ($options & self::HEADERS_ADD) {
                    $to->setHeader($name, $value);
                }
                continue;
            }

            if (empty($value)) {
                continue;
            }

            switch ($name) {
                case Translations::HEADER_LANGUAGE:
                case Translations::HEADER_PLURAL:
                    if ($options & self::LANGUAGE_OVERRIDE) {
                        $to->setHeader($name, $value);
                    }
                    break;

                case Translations::HEADER_DOMAIN:
                    if ($options & self::DOMAIN_OVERRIDE) {
                        $to->setHeader($name, $value);
                    }
                    break;

                default:
                    if ($options & self::HEADERS_OVERRIDE) {
                        $to->setHeader($name, $value);
                    }
            }
        }
    }
}
