<?php

namespace Packback\Lti1p3\Helpers;

class Helpers
{
    public static function filterOutNulls(array $array): array
    {
        return array_filter($array, fn ($value) => !is_null($value));
    }

    public static function buildUrlWithQueryParams(string $url, array $params = []): string
    {
        if (empty($params)) {
            return $url;
        }

        if (parse_url($url, PHP_URL_QUERY)) {
            $separator = '&';
        } else {
            $separator = '?';
        }

        return $url.$separator.http_build_query($params, '');
    }
}
