<?php

require_once 'HTMLPurifier/URIFilter/DisableExternal.php';

HTMLPurifier_ConfigSchema::define(
    'URI', 'DisableExternalResources', false, 'bool',
    'Disables the embedding of external resources, preventing users from '.
    'embedding things like images from other hosts. This prevents '.
    'access tracking (good for email viewers), bandwidth leeching, '.
    'cross-site request forging, goatse.cx posting, and '.
    'other nasties, but also results in '.
    'a loss of end-user functionality (they can\'t directly post a pic '.
    'they posted from Flickr anymore). Use it if you don\'t have a '.
    'robust user-content moderation team. This directive has been '.
    'available since 1.3.0.'
);

class HTMLPurifier_URIFilter_DisableExternalResources extends HTMLPurifier_URIFilter_DisableExternal
{
    var $name = 'DisableExternalResources';
    function filter(&$uri, $config, &$context) {
        if (!$context->get('EmbeddedURI', true)) return true;
        return parent::filter($uri, $config, $context);
    }
}

