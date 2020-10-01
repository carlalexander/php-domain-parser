<?php

/**
 * PHP Domain Parser: Public Suffix List based URL parsing.
 *
 * @see http://github.com/jeremykendall/php-domain-parser for the canonical source repository
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pdp\Storage;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\SimpleCache\CacheInterface;

final class PsrStorageFactory implements
    RemoteStorageURL,
    PublicSuffixListStorageFactory,
    RootZoneDatabaseStorageFactory
{
    private ClientInterface $client;

    private RequestFactoryInterface $requestFactory;

    private CacheInterface $cache;
    
    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        CacheInterface $cache
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->cache = $cache;
    }

    /**
     * @param mixed $cacheTtl The cache TTL
     */
    public function createPublicSuffixListStorage(string $cachePrefix = '', $cacheTtl = null): PublicSuffixListStorage
    {
        return new RulesStorage(
            new RulesPsr18Client($this->client, $this->requestFactory),
            new RulesPsr16Cache($this->cache, $cachePrefix, $cacheTtl)
        );
    }

    /**
     * @param mixed $cacheTtl The cache TTL
     */
    public function createRootZoneDatabaseStorage(string $cachePrefix = '', $cacheTtl = null): RootZoneDatabaseStorage
    {
        return new TopLevelDomainsStorage(
            new TopLevelDomainsPsr18Client($this->client, $this->requestFactory),
            new TopLevelDomainsPsr16Cache($this->cache, $cachePrefix, $cacheTtl)
        );
    }
}
