<?php

namespace PhpXmlRpc\Exception;

use PhpXmlRpc\Exception as BaseExtension;

/**
 * To be used for all errors related to the transport, which are not related to specifically to HTTP. Eg: can not open socket
 */
class TransportException extends BaseExtension
{
}
