<?php

namespace Kunstmaan\TranslatorBundle\Cache;

use Kunstmaan\TranslatorBundle\Service\Translator\ResourceCacher;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;

/**
 * Cache clearer for translations
 */
class Clearer implements CacheClearerInterface
{
    /**
     * @var ResourceCacher
     */
    protected $cacher;

    public function __construct(ResourceCacher $cacher)
    {
        $this->cacher = $cacher;
    }

    /**
     * {@inheritDoc}
     */
    public function clear($cacheDir)
    {
        // kuma_translator.cache_dir or $cacheDir?
        // e.g. $this->cache->setCacheDir($cacheDir);

        $this->cacher->flushCache();
    }
}
