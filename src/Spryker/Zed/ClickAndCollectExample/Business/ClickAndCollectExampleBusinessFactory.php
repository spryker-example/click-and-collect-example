<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerExample\Zed\ClickAndCollectExample\Business;

use SprykerExample\Zed\ClickAndCollectExample\Business\ErrorAdder\QuoteReplacementResponseErrorAdder;
use SprykerExample\Zed\ClickAndCollectExample\Business\ErrorAdder\QuoteReplacementResponseErrorAdderInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\Expander\ItemExpander;
use SprykerExample\Zed\ClickAndCollectExample\Business\Expander\ItemExpanderInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\Expander\ProductOfferServicePointExpander;
use SprykerExample\Zed\ClickAndCollectExample\Business\Expander\ProductOfferServicePointExpanderInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementChecker\DeliveryProductOfferReplacementChecker;
use SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementChecker\PickupProductOfferReplacementChecker;
use SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementChecker\ProductOfferReplacementCheckerInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementFinder\ProductOfferReplacementFinder;
use SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementFinder\ProductOfferReplacementFinderInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\Reader\ProductOfferServicePointReader;
use SprykerExample\Zed\ClickAndCollectExample\Business\Reader\ProductOfferServicePointReaderInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\Replacer\DeliveryItemProductOfferReplacer;
use SprykerExample\Zed\ClickAndCollectExample\Business\Replacer\ItemProductOfferReplacerInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\Replacer\PickupItemProductOfferReplacer;
use SprykerExample\Zed\ClickAndCollectExample\Business\Replacer\QuoteProductOfferReplacer;
use SprykerExample\Zed\ClickAndCollectExample\Business\Replacer\QuoteProductOfferReplacerInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\Validator\QuoteItemProductOfferReplacementValidator;
use SprykerExample\Zed\ClickAndCollectExample\Business\Validator\QuoteItemProductOfferReplacementValidatorInterface;
use SprykerExample\Zed\ClickAndCollectExample\ClickAndCollectExampleDependencyProvider;
use SprykerExample\Zed\ClickAndCollectExample\Dependency\Facade\ClickAndCollectExampleToAvailabilityFacadeInterface;
use SprykerExample\Zed\ClickAndCollectExample\Dependency\Facade\ClickAndCollectExampleToServicePointFacadeInterface;
use SprykerExample\Zed\ClickAndCollectExample\Dependency\Facade\ClickAndCollectExampleToShipmentFacadeInterface;
use SprykerExample\Zed\ClickAndCollectExample\Dependency\Facade\ClickAndCollectExampleToStoreFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \SprykerExample\Zed\ClickAndCollectExample\ClickAndCollectExampleConfig getConfig()
 * @method \SprykerExample\Zed\ClickAndCollectExample\Persistence\ClickAndCollectExampleRepositoryInterface getRepository()
 */
class ClickAndCollectExampleBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\Replacer\ItemProductOfferReplacerInterface
     */
    public function createPickupItemProductOfferReplacer(): ItemProductOfferReplacerInterface
    {
        return new PickupItemProductOfferReplacer(
            $this->createProductOfferServicePointReader(),
            $this->createPickupProductOfferReplacementFinder(),
            $this->createQuoteReplacementResponseErrorAdder(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\Replacer\ItemProductOfferReplacerInterface
     */
    public function createDeliveryItemProductOfferReplacer(): ItemProductOfferReplacerInterface
    {
        return new DeliveryItemProductOfferReplacer(
            $this->createProductOfferServicePointReader(),
            $this->createDeliveryProductOfferReplacementFinder(),
            $this->createQuoteReplacementResponseErrorAdder(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\Replacer\QuoteProductOfferReplacerInterface
     */
    public function createQuoteProductOfferReplacer(): QuoteProductOfferReplacerInterface
    {
        return new QuoteProductOfferReplacer(
            $this->getQuoteItemReplacers(),
        );
    }

    /**
     * @return list<\Spryker\Zed\ClickAndCollectExample\Business\Replacer\ItemProductOfferReplacerInterface>
     */
    public function getQuoteItemReplacers(): array
    {
        return [
            $this->createPickupItemProductOfferReplacer(),
            $this->createDeliveryItemProductOfferReplacer(),
        ];
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\Reader\ProductOfferServicePointReaderInterface
     */
    public function createProductOfferServicePointReader(): ProductOfferServicePointReaderInterface
    {
        return new ProductOfferServicePointReader(
            $this->getRepository(),
            $this->createProductOfferServicePointExpander(),
        );
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\Expander\ProductOfferServicePointExpanderInterface
     */
    public function createProductOfferServicePointExpander(): ProductOfferServicePointExpanderInterface
    {
        return new ProductOfferServicePointExpander(
            $this->getRepository(),
        );
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementFinder\ProductOfferReplacementFinderInterface
     */
    public function createPickupProductOfferReplacementFinder(): ProductOfferReplacementFinderInterface
    {
        return new ProductOfferReplacementFinder(
            $this->getStoreFacade(),
            $this->getAvailabilityFacade(),
            $this->createPickupProductOfferReplacementChecker(),
        );
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementFinder\ProductOfferReplacementFinderInterface
     */
    public function createDeliveryProductOfferReplacementFinder(): ProductOfferReplacementFinderInterface
    {
        return new ProductOfferReplacementFinder(
            $this->getStoreFacade(),
            $this->getAvailabilityFacade(),
            $this->createDeliveryProductOfferReplacementChecker(),
        );
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\ErrorAdder\QuoteReplacementResponseErrorAdderInterface
     */
    public function createQuoteReplacementResponseErrorAdder(): QuoteReplacementResponseErrorAdderInterface
    {
        return new QuoteReplacementResponseErrorAdder();
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementChecker\ProductOfferReplacementCheckerInterface
     */
    public function createPickupProductOfferReplacementChecker(): ProductOfferReplacementCheckerInterface
    {
        return new PickupProductOfferReplacementChecker();
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementChecker\ProductOfferReplacementCheckerInterface
     */
    public function createDeliveryProductOfferReplacementChecker(): ProductOfferReplacementCheckerInterface
    {
        return new DeliveryProductOfferReplacementChecker();
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\Validator\QuoteItemProductOfferReplacementValidatorInterface
     */
    public function createQuoteItemProductOfferReplacementValidator(): QuoteItemProductOfferReplacementValidatorInterface
    {
        return new QuoteItemProductOfferReplacementValidator(
            $this->createItemExpander(),
            $this->createQuoteProductOfferReplacer(),
        );
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\Expander\ItemExpanderInterface
     */
    public function createItemExpander(): ItemExpanderInterface
    {
        return new ItemExpander(
            $this->getServicePointFacade(),
            $this->getShipmentFacade(),
        );
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Dependency\Facade\ClickAndCollectExampleToServicePointFacadeInterface
     */
    public function getServicePointFacade(): ClickAndCollectExampleToServicePointFacadeInterface
    {
        return $this->getProvidedDependency(ClickAndCollectExampleDependencyProvider::FACADE_SERVICE_POINT);
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Dependency\Facade\ClickAndCollectExampleToShipmentFacadeInterface
     */
    public function getShipmentFacade(): ClickAndCollectExampleToShipmentFacadeInterface
    {
        return $this->getProvidedDependency(ClickAndCollectExampleDependencyProvider::FACADE_SHIPMENT);
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Dependency\Facade\ClickAndCollectExampleToStoreFacadeInterface
     */
    public function getStoreFacade(): ClickAndCollectExampleToStoreFacadeInterface
    {
        return $this->getProvidedDependency(ClickAndCollectExampleDependencyProvider::FACADE_STORE);
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Dependency\Facade\ClickAndCollectExampleToAvailabilityFacadeInterface
     */
    public function getAvailabilityFacade(): ClickAndCollectExampleToAvailabilityFacadeInterface
    {
        return $this->getProvidedDependency(ClickAndCollectExampleDependencyProvider::FACADE_AVAILABILITY);
    }
}
