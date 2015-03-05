<?php

require_once __DIR__.'/AppKernel.php';

use Network\CacheBundle\Subscriber\PurgeSubscriber;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Network\CacheBundle\Service\ReverseProxyCache;

class AppCache extends ReverseProxyCache
{
    protected function getOptions()
    {
        return array(
            'debug'                  => false,
            'default_ttl'            => 0,
            'private_headers'        => array('Authorization', 'Cookie'),
            'allow_reload'           => false,
            'allow_revalidate'       => false,
            'stale_while_revalidate' => 2,
            'stale_if_error'         => 60,
        );
    }

    public function __construct(HttpKernelInterface $kernel, $cacheDir = null)
    {
        if (null === $cacheDir) {
            $cacheDir = new Store(__DIR__.'/../cache');
        }
        parent::__construct($kernel, $cacheDir);
        $this->addSubscriber(new PurgeSubscriber());
    }
}
