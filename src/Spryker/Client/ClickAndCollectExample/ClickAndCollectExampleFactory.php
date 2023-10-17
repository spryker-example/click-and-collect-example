<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerExample\Client\ClickAndCollectExample;

use SprykerExample\Client\ClickAndCollectExample\Calculator\ProductOfferServicePointAvailabilityCalculator;
use SprykerExample\Client\ClickAndCollectExample\Calculator\ProductOfferServicePointAvailabilityCalculatorInterface;
use SprykerExample\Client\ClickAndCollectExample\Dependency\Client\ClickAndCollectExampleToProductOfferStorageClientInterface;
use SprykerExample\Client\ClickAndCollectExample\Filter\ShipmentTypeFilter;
use SprykerExample\Client\ClickAndCollectExample\Filter\ShipmentTypeFilterInterface;
use SprykerExample\Client\ClickAndCollectExample\Sorter\ProductOfferServicePointAvailabilityResponseItemSorter;
use SprykerExample\Client\ClickAndCollectExample\Sorter\ProductOfferServicePointAvailabilityResponseItemSorterInterface;
use Spryker\Client\Kernel\AbstractFactory;

class ClickAndCollectExampleFactory extends AbstractFactory
{
    /**
     * @return \SprykerExample\Client\ClickAndCollectExample\Calculator\ProductOfferServicePointAvailabilityCalculatorInterface
     */
    public function createProductOfferServicePointAvailabilityCalculator(): ProductOfferServicePointAvailabilityCalculatorInterface
    {
        return new ProductOfferServicePointAvailabilityCalculator(
            $this->createProductOfferServicePointAvailabilityResponseItemSorter(),
        );
    }

    /**
     * @return \SprykerExample\Client\ClickAndCollectExample\Sorter\ProductOfferServicePointAvailabilityResponseItemSorterInterface
     */
    public function createProductOfferServicePointAvailabilityResponseItemSorter(): ProductOfferServicePointAvailabilityResponseItemSorterInterface
    {
        return new ProductOfferServicePointAvailabilityResponseItemSorter();
    }

    /**
     * @return \SprykerExample\Client\ClickAndCollectExample\Filter\ShipmentTypeFilterInterface
     */
    public function createShipmentTypeFilter(): ShipmentTypeFilterInterface
    {
        return new ShipmentTypeFilter(
            $this->getProductOfferStorageClient(),
        );
    }

    /**
     * @return \SprykerExample\Client\ClickAndCollectExample\Dependency\Client\ClickAndCollectExampleToProductOfferStorageClientInterface
     */
    public function getProductOfferStorageClient(): ClickAndCollectExampleToProductOfferStorageClientInterface
    {
        return $this->getProvidedDependency(ClickAndCollectExampleDependencyProvider::CLIENT_PRODUCT_OFFER_STORAGE);
    }
}
