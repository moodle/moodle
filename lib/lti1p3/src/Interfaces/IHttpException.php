<?php

namespace Packback\Lti1p3\Interfaces;

interface IHttpException extends \Throwable
{
    public function getResponse(): IHttpResponse;
}
