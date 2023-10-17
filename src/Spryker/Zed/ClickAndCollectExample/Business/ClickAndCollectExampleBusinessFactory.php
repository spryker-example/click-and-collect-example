<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerExample\Zed\ClickAndCollectExample\Business;

use SprykerExample\Zed\ClickAndCollectExample\Business\ErrorAdder\QuoteResponseErrorAdder;
use SprykerExample\Zed\ClickAndCollectExample\Business\ErrorAdder\QuoteResponseErrorAdderInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\Expander\ProductOfferServicePointExpander;
use SprykerExample\Zed\ClickAndCollectExample\Business\Expander\ProductOfferServicePointExpanderInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementChecker\DeliveryProductOfferReplacementChecker;
use SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementChecker\PickupProductOfferReplacementChecker;
use SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementChecker\ProductOfferReplacementCheckerInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementFinder\ProductOfferReplacementFinder;
use SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementFinder\ProductOfferReplacementFinderInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\QuoteItemReplacer\DeliveryQuoteItemReplacer;
use SprykerExample\Zed\ClickAndCollectExample\Business\QuoteItemReplacer\PickupQuoteItemReplacer;
use SprykerExample\Zed\ClickAndCollectExample\Business\QuoteItemReplacer\QuoteItemReplacerInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\Reader\ProductOfferServicePointReader;
use SprykerExample\Zed\ClickAndCollectExample\Business\Reader\ProductOfferServicePointReaderInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \SprykerExample\Zed\ClickAndCollectExample\ClickAndCollectExampleConfig getConfig()
 * @method \SprykerExample\Zed\ClickAndCollectExample\Persistence\ClickAndCollectExampleRepositoryInterface getRepository()
 */
class ClickAndCollectExampleBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\QuoteItemReplacer\QuoteItemReplacerInterface
     */
    public function createPickupQuoteItemReplacer(): QuoteItemReplacerInterface
    {
        return new PickupQuoteItemReplacer(
            $this->createProductOfferServicePointReader(),
            $this->createPickupProductOfferReplacementFinder(),
            $this->createQuoteResponseErrorAdder(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\QuoteItemReplacer\QuoteItemReplacerInterface
     */
    public function createDeliveryQuoteItemReplacer(): QuoteItemReplacerInterface
    {
        return new DeliveryQuoteItemReplacer(
            $this->createProductOfferServicePointReader(),
            $this->createDeliveryProductOfferReplacementFinder(),
            $this->createQuoteResponseErrorAdder(),
            $this->getConfig(),
        );
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
            $this->createPickupProductOfferReplacementChecker(),
        );
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementFinder\ProductOfferReplacementFinderInterface
     */
    public function createDeliveryProductOfferReplacementFinder(): ProductOfferReplacementFinderInterface
    {
        return new ProductOfferReplacementFinder(
            $this->createDeliveryProductOfferReplacementChecker(),
        );
    }

    /**
     * @return \SprykerExample\Zed\ClickAndCollectExample\Business\ErrorAdder\QuoteResponseErrorAdderInterface
     */
    public function createQuoteResponseErrorAdder(): QuoteResponseErrorAdderInterface
    {
        return new QuoteResponseErrorAdder();
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
}
