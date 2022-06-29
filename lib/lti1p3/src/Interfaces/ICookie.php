<?php

namespace Packback\Lti1p3\Interfaces;

interface ICookie
{
    public function getCookie(string $name): ?string;

    public function setCookie(string $name, string $value, $exp = 3600, $options = []): void;
}
