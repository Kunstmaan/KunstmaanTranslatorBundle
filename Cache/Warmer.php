<?php

namespace Kunstmaan\TranslatorBundle\Cache;

use Kunstmaan\TranslatorBundle\Service\Translator\Translator;
use Kunstmaan\TranslatorBundle\Service\Translator\ResourceCacher;
use Kunstmaan\TranslatorBundle\Service\Translator\CacheValidator;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Cache warmer for translations
 */
class Warmer implements CacheWarmerInterface
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var ResourceCacher
     */
    protected $cacher;

    /**
     * @var CacheValidator
     */
    protected $validator;

    public function __construct(Translator $translator, ResourceCacher $cacher, CacheValidator $validator)
    {
        $this->translator = $translator;
        $this->cacher = $cacher;
        $this->validator = $validator;
    }

    /**
     * {@inheritDoc}
     */
    public function warmUp($cacheDir)
    {
        if ($this->validator->isCacheFresh()) {
            return; // Cache is already fresh
        }

        // kuma_translator.cache_dir or $cacheDir?
        // e.g. $this->cacher->setCacheDir($cacheDir);

        // Remove cache functionality from translator?

        $this->translator->addDatabaseResources();
    }

    /**
     * {@inheritDoc}
     */
    public function isOptional()
    {
        return false;
    }
}
