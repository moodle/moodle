<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2010-2025 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class_alias(\Mustache\Cache::class, \Mustache_Cache::class);
class_alias(\Mustache\Cache\AbstractCache::class, \Mustache_Cache_AbstractCache::class);
class_alias(\Mustache\Cache\FilesystemCache::class, \Mustache_Cache_FilesystemCache::class);
class_alias(\Mustache\Cache\NoopCache::class, \Mustache_Cache_NoopCache::class);
class_alias(\Mustache\Compiler::class, \Mustache_Compiler::class);
class_alias(\Mustache\Context::class, \Mustache_Context::class);
class_alias(\Mustache\Engine::class, \Mustache_Engine::class);
class_alias(\Mustache\Exception::class, \Mustache_Exception::class);
class_alias(\Mustache\Exception\InvalidArgumentException::class, \Mustache_Exception_InvalidArgumentException::class);
class_alias(\Mustache\Exception\LogicException::class, \Mustache_Exception_LogicException::class);
class_alias(\Mustache\Exception\RuntimeException::class, \Mustache_Exception_RuntimeException::class);
class_alias(\Mustache\Exception\SyntaxException::class, \Mustache_Exception_SyntaxException::class);
class_alias(\Mustache\Exception\UnknownFilterException::class, \Mustache_Exception_UnknownFilterException::class);
class_alias(\Mustache\Exception\UnknownHelperException::class, \Mustache_Exception_UnknownHelperException::class);
class_alias(\Mustache\Exception\UnknownTemplateException::class, \Mustache_Exception_UnknownTemplateException::class);
class_alias(\Mustache\HelperCollection::class, \Mustache_HelperCollection::class);
class_alias(\Mustache\LambdaHelper::class, \Mustache_LambdaHelper::class);
class_alias(\Mustache\Loader::class, \Mustache_Loader::class);
class_alias(\Mustache\Loader\ArrayLoader::class, \Mustache_Loader_ArrayLoader::class);
class_alias(\Mustache\Loader\CascadingLoader::class, \Mustache_Loader_CascadingLoader::class);
class_alias(\Mustache\Loader\FilesystemLoader::class, \Mustache_Loader_FilesystemLoader::class);
class_alias(\Mustache\Loader\InlineLoader::class, \Mustache_Loader_InlineLoader::class);
class_alias(\Mustache\Loader\MutableLoader::class, \Mustache_Loader_MutableLoader::class);
class_alias(\Mustache\Loader\ProductionFilesystemLoader::class, \Mustache_Loader_ProductionFilesystemLoader::class);
class_alias(\Mustache\Loader\StringLoader::class, \Mustache_Loader_StringLoader::class);
class_alias(\Mustache\Logger::class, \Mustache_Logger::class);
class_alias(\Mustache\Logger\AbstractLogger::class, \Mustache_Logger_AbstractLogger::class);
class_alias(\Mustache\Logger\StreamLogger::class, \Mustache_Logger_StreamLogger::class);
class_alias(\Mustache\Parser::class, \Mustache_Parser::class);
class_alias(\Mustache\Source::class, \Mustache_Source::class);
class_alias(\Mustache\Source\FilesystemSource::class, \Mustache_Source_FilesystemSource::class);
class_alias(\Mustache\Template::class, \Mustache_Template::class);
class_alias(\Mustache\Tokenizer::class, \Mustache_Tokenizer::class);

if (!class_exists(\Mustache_Engine::class)) {
    /** @deprecated use Mustache\Engine */
    class Mustache_Engine extends \Mustache\Engine
    {
    }
}

if (!interface_exists(\Mustache_Cache::class)) {
    /** @deprecated use Mustache\Cache */
    interface Mustache_Cache extends \Mustache\Cache
    {
    }
}

if (!class_exists(\Mustache_Cache_AbstractCache::class)) {
    /** @deprecated use Mustache\Cache\AbstractCache */
    abstract class Mustache_Cache_AbstractCache extends \Mustache\Cache\AbstractCache
    {
    }
}

if (!class_exists(\Mustache_Cache_FilesystemCache::class)) {
    /** @deprecated use Mustache\Cache\FilesystemCache */
    class Mustache_Cache_FilesystemCache extends \Mustache\Cache\FilesystemCache
    {
    }
}

if (!class_exists(\Mustache_Cache_NoopCache::class)) {
    /** @deprecated use Mustache\Cache\NoopCache */
    class Mustache_Cache_NoopCache extends \Mustache\Cache\NoopCache
    {
    }
}

if (!class_exists(\Mustache_Compiler::class)) {
    /** @deprecated use Mustache\Compiler */
    class Mustache_Compiler extends \Mustache\Compiler
    {
    }
}

if (!class_exists(\Mustache_Context::class)) {
    /** @deprecated use Mustache\Context */
    class Mustache_Context extends \Mustache\Context
    {
    }
}

if (!class_exists(\Mustache_Engine::class)) {
    /** @deprecated use Mustache\Engine */
    class Mustache_Engine extends \Mustache\Engine
    {
    }
}

if (!interface_exists(\Mustache_Exception::class)) {
    /** @deprecated use Mustache\Exception */
    interface Mustache_Exception extends \Mustache\Exception
    {
    }
}

if (!class_exists(\Mustache_Exception_InvalidArgumentException::class)) {
    /** @deprecated use Mustache\Exception\InvalidArgumentException */
    class Mustache_Exception_InvalidArgumentException extends \Mustache\Exception\InvalidArgumentException
    {
    }
}

if (!class_exists(\Mustache_Exception_LogicException::class)) {
    /** @deprecated use Mustache\Exception\LogicException */
    class Mustache_Exception_LogicException extends \Mustache\Exception\LogicException
    {
    }
}

if (!class_exists(\Mustache_Exception_RuntimeException::class)) {
    /** @deprecated use Mustache\Exception\RuntimeException */
    class Mustache_Exception_RuntimeException extends \Mustache\Exception\RuntimeException
    {
    }
}

if (!class_exists(\Mustache_Exception_SyntaxException::class)) {
    /** @deprecated use Mustache\Exception\SyntaxException */
    class Mustache_Exception_SyntaxException extends \Mustache\Exception\SyntaxException
    {
    }
}

if (!class_exists(\Mustache_Exception_UnknownFilterException::class)) {
    /** @deprecated use Mustache\Exception\UnknownFilterException */
    class Mustache_Exception_UnknownFilterException extends \Mustache\Exception\UnknownFilterException
    {
    }
}

if (!class_exists(\Mustache_Exception_UnknownHelperException::class)) {
    /** @deprecated use Mustache\Exception\UnknownHelperException */
    class Mustache_Exception_UnknownHelperException extends \Mustache\Exception\UnknownHelperException
    {
    }
}

if (!class_exists(\Mustache_Exception_UnknownTemplateException::class)) {
    /** @deprecated use Mustache\Exception\UnknownTemplateException */
    class Mustache_Exception_UnknownTemplateException extends \Mustache\Exception\UnknownTemplateException
    {
    }
}

if (!class_exists(\Mustache_HelperCollection::class)) {
    /** @deprecated use Mustache\HelperCollection */
    class Mustache_HelperCollection extends \Mustache\HelperCollection
    {
    }
}

if (!class_exists(\Mustache_LambdaHelper::class)) {
    /** @deprecated use Mustache\LambdaHelper */
    class Mustache_LambdaHelper extends \Mustache\LambdaHelper
    {
    }
}

if (!interface_exists(\Mustache_Loader::class)) {
    /** @deprecated use Mustache\Loader */
    interface Mustache_Loader extends \Mustache\Loader
    {
    }
}

if (!class_exists(\Mustache_Loader_ArrayLoader::class)) {
    /** @deprecated use Mustache\Loader\ArrayLoader */
    class Mustache_Loader_ArrayLoader extends \Mustache\Loader\ArrayLoader
    {
    }
}

if (!class_exists(\Mustache_Loader_CascadingLoader::class)) {
    /** @deprecated use Mustache\Loader\CascadingLoader */
    class Mustache_Loader_CascadingLoader extends \Mustache\Loader\CascadingLoader
    {
    }
}

if (!class_exists(\Mustache_Loader_FilesystemLoader::class)) {
    /** @deprecated use Mustache\Loader\FilesystemLoader */
    class Mustache_Loader_FilesystemLoader extends \Mustache\Loader\FilesystemLoader
    {
    }
}

if (!class_exists(\Mustache_Loader_InlineLoader::class)) {
    /** @deprecated use Mustache\Loader\InlineLoader */
    class Mustache_Loader_InlineLoader extends \Mustache\Loader\InlineLoader
    {
    }
}

if (!interface_exists(\Mustache_Loader_MutableLoader::class)) {
    /** @deprecated use Mustache\Loader\MutableLoader */
    interface Mustache_Loader_MutableLoader extends \Mustache\Loader\MutableLoader
    {
    }
}

if (!class_exists(\Mustache_Loader_ProductionFilesystemLoader::class)) {
    /** @deprecated use Mustache\Loader\ProductionFilesystemLoader */
    class Mustache_Loader_ProductionFilesystemLoader extends \Mustache\Loader\ProductionFilesystemLoader
    {
    }
}

if (!class_exists(\Mustache_Loader_StringLoader::class)) {
    /** @deprecated use Mustache\Loader\StringLoader */
    class Mustache_Loader_StringLoader extends \Mustache\Loader\StringLoader
    {
    }
}

if (!interface_exists(\Mustache_Logger::class)) {
    /** @deprecated use Mustache\Logger */
    interface Mustache_Logger extends \Mustache\Logger
    {
    }
}

if (!class_exists(\Mustache_Logger_AbstractLogger::class)) {
    /** @deprecated use Mustache\Logger\AbstractLogger */
    abstract class Mustache_Logger_AbstractLogger extends \Mustache\Logger\AbstractLogger
    {
    }
}

if (!class_exists(\Mustache_Logger_StreamLogger::class)) {
    /** @deprecated use Mustache\Logger\StreamLogger */
    class Mustache_Logger_StreamLogger extends \Mustache\Logger\StreamLogger
    {
    }
}

if (!class_exists(\Mustache_Parser::class)) {
    /** @deprecated use Mustache\Parser */
    class Mustache_Parser extends \Mustache\Parser
    {
    }
}

if (!interface_exists(\Mustache_Source::class)) {
    /** @deprecated use Mustache\Source */
    interface Mustache_Source extends \Mustache\Source
    {
    }
}

if (!class_exists(\Mustache_Source_FilesystemSource::class)) {
    /** @deprecated use Mustache\Source\FilesystemSource */
    class Mustache_Source_FilesystemSource extends \Mustache\Source\FilesystemSource
    {
    }
}

if (!class_exists(\Mustache_Template::class)) {
    /** @deprecated use Mustache\Template */
    abstract class Mustache_Template extends \Mustache\Template
    {
    }
}

if (!class_exists(\Mustache_Tokenizer::class)) {
    /** @deprecated use Mustache\Tokenizer */
    class Mustache_Tokenizer extends \Mustache\Tokenizer
    {
    }
}
