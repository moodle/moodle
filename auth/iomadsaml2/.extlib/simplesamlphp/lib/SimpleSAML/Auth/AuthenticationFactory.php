<?php

declare(strict_types=1);

namespace SimpleSAML\Auth;

use SimpleSAML\Configuration;
use SimpleSAML\Session;

/**
 * Factory class to get instances of \SimpleSAML\Auth\Simple for a given authentication source.
 */
class AuthenticationFactory
{
    /** @var \SimpleSAML\Configuration */
    protected $config;

    /** @var \SimpleSAML\Session */
    protected $session;


    public function __construct(Configuration $config, Session $session)
    {
        $this->config = $config;
        $this->session = $session;
    }


    /**
     * Create a new instance of \SimpleSAML\Auth\Simple for the given authentication source.
     *
     * @param string $as The identifier of the authentication source, as indexed in the authsources.php configuration
     * file.
     *
     * @return \SimpleSAML\Auth\Simple
     */
    public function create($as)
    {
        return new Simple($as, $this->config, $this->session);
    }
}
