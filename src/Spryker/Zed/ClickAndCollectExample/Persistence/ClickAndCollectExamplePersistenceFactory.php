<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerExample\Zed\ClickAndCollectExample\Persistence;

use Orm\Zed\PriceProductOffer\Persistence\SpyPriceProductOfferQuery;
use Orm\Zed\ProductOffer\Persistence\SpyProductOfferQuery;
use Orm\Zed\ProductOfferStock\Persistence\SpyProductOfferStockQuery;
use SprykerExample\Zed\ClickAndCollectExample\ClickAndCollectExampleDependencyProvider;
use SprykerExample\Zed\ClickAndCollectExample\Persistence\Propel\Mapper\PriceProductOfferMapper;
use SprykerExample\Zed\ClickAndCollectExample\Persistence\Propel\Mapper\ProductOfferMapper;
use SprykerExample\Zed\ClickAndCollectExample\Persistence\Propel\Mapper\ProductOfferStockMapper;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \SprykerExample\Zed\ClickAndCollectExample\ClickAndCollectExampleConfig getConfig()
 * @method \SprykerExample\Zed\ClickAndCollectExample\Persistence\ClickAndCollectExampleRepositoryInterface getRepository()
 */
class ClickAndCollectExamplePersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Persistence\Propel\Mapper\ProductOfferMapper
     */
    public function createProductOfferMapper(): ProductOfferMapper
    {
        return new ProductOfferMapper();
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Persistence\Propel\Mapper\ProductOfferStockMapper
     */
    public function createProductOfferStockMapper(): ProductOfferStockMapper
    {
        return new ProductOfferStockMapper();
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Persistence\Propel\Mapper\PriceProductOfferMapper
     */
    public function createPriceProductOfferMapper(): PriceProductOfferMapper
    {
        return new PriceProductOfferMapper();
    }

    /**
     * @return \Orm\Zed\ProductOffer\Persistence\SpyProductOfferQuery
     */
    public function getProductOfferQuery(): SpyProductOfferQuery
    {
        return $this->getProvidedDependency(ClickAndCollectExampleDependencyProvider::PROPEL_QUERY_PRODUCT_OFFER);
    }

    /**
     * @return \Orm\Zed\ProductOfferStock\Persistence\SpyProductOfferStockQuery
     */
    public function getProductOfferStockQuery(): SpyProductOfferStockQuery
    {
        return $this->getProvidedDependency(ClickAndCollectExampleDependencyProvider::PROPEL_QUERY_PRODUCT_OFFER_STOCK);
    }

    /**
     * @return \Orm\Zed\PriceProductOffer\Persistence\SpyPriceProductOfferQuery
     */
    public function getPriceProductOfferQuery(): SpyPriceProductOfferQuery
    {
        return $this->getProvidedDependency(ClickAndCollectExampleDependencyProvider::PROPEL_QUERY_PRICE_PRODUCT_OFFER);
    }
}
