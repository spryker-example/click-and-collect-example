<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerExample\Zed\ClickAndCollectExample\Business\Replacer;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\ProductOfferServicePointCriteriaTransfer;
use Generated\Shared\Transfer\QuoteReplacementResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerExample\Zed\ClickAndCollectExample\Business\ErrorAdder\QuoteReplacementResponseErrorAdderInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementFinder\ProductOfferReplacementFinderInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\Reader\ProductOfferServicePointReaderInterface;
use SprykerExample\Zed\ClickAndCollectExample\ClickAndCollectExampleConfig;

class DeliveryItemProductOfferReplacer implements ItemProductOfferReplacerInterface
{
    /**
     * @var \SprykerExample\Zed\ClickAndCollectExample\Business\Reader\ProductOfferServicePointReaderInterface
     */
    protected ProductOfferServicePointReaderInterface $clickAndCollectExampleProductOfferServicePointReader;

    /**
     * @var \SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementFinder\ProductOfferReplacementFinderInterface
     */
    protected ProductOfferReplacementFinderInterface $clickAndCollectExampleProductOfferReplacementFinder;

    /**
     * @var \SprykerExample\Zed\ClickAndCollectExample\Business\ErrorAdder\QuoteReplacementResponseErrorAdderInterface
     */
    protected QuoteReplacementResponseErrorAdderInterface $quoteReplacementResponseErrorAdder;

    /**
     * @var \SprykerExample\Zed\ClickAndCollectExample\ClickAndCollectExampleConfig
     */
    protected ClickAndCollectExampleConfig $clickAndCollectExampleConfig;

    /**
     * @param \SprykerExample\Zed\ClickAndCollectExample\Business\Reader\ProductOfferServicePointReaderInterface $clickAndCollectExampleProductOfferServicePointReader
     * @param \SprykerExample\Zed\ClickAndCollectExample\Business\ProductOfferReplacementFinder\ProductOfferReplacementFinderInterface $clickAndCollectExampleProductOfferReplacementFinder
     * @param \SprykerExample\Zed\ClickAndCollectExample\Business\ErrorAdder\QuoteReplacementResponseErrorAdderInterface $quoteReplacementResponseErrorAdder
     * @param \SprykerExample\Zed\ClickAndCollectExample\ClickAndCollectExampleConfig $clickAndCollectExampleConfig
     */
    public function __construct(
        ProductOfferServicePointReaderInterface $clickAndCollectExampleProductOfferServicePointReader,
        ProductOfferReplacementFinderInterface $clickAndCollectExampleProductOfferReplacementFinder,
        QuoteReplacementResponseErrorAdderInterface $quoteReplacementResponseErrorAdder,
        ClickAndCollectExampleConfig $clickAndCollectExampleConfig
    ) {
        $this->clickAndCollectExampleProductOfferServicePointReader = $clickAndCollectExampleProductOfferServicePointReader;
        $this->clickAndCollectExampleProductOfferReplacementFinder = $clickAndCollectExampleProductOfferReplacementFinder;
        $this->quoteReplacementResponseErrorAdder = $quoteReplacementResponseErrorAdder;
        $this->clickAndCollectExampleConfig = $clickAndCollectExampleConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteReplacementResponseTransfer
     */
    public function replaceQuoteItemProductOffers(QuoteTransfer $quoteTransfer): QuoteReplacementResponseTransfer
    {
        $quoteReplacementResponseTransfer = (new QuoteReplacementResponseTransfer())->setQuote($quoteTransfer);
        $itemTransfersForReplacement = $this->getQuoteItemsAvailableForReplacement($quoteReplacementResponseTransfer);
        if (count($itemTransfersForReplacement) === 0) {
            return $quoteReplacementResponseTransfer;
        }
        $productOfferServicePointCriteriaTransfer = $this
            ->createProductOfferServicePointCriteriaTransfer($quoteTransfer, $itemTransfersForReplacement);

        $productOfferServicePointTransfers = $this->clickAndCollectExampleProductOfferServicePointReader
            ->getDeliveryProductOfferServicePoints($productOfferServicePointCriteriaTransfer);

        foreach ($itemTransfersForReplacement as $itemTransfer) {
            $replacementProductOfferTransfer = $this->clickAndCollectExampleProductOfferReplacementFinder->findSuitableProductOffer($itemTransfer, $productOfferServicePointTransfers);
            if (!$replacementProductOfferTransfer) {
                $quoteReplacementResponseTransfer->addFailedReplacementItem($itemTransfer);
                $this->quoteReplacementResponseErrorAdder->addError($quoteReplacementResponseTransfer, $itemTransfer);

                continue;
            }

            $itemTransfer->setProductOfferReference($replacementProductOfferTransfer->getProductOfferReference());
            $itemTransfer->setGroupKey(null);
        }

        return $quoteReplacementResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteReplacementResponseTransfer $quoteReplacementResponseTransfer
     *
     * @return list<\Generated\Shared\Transfer\ItemTransfer>
     */
    protected function getQuoteItemsAvailableForReplacement(QuoteReplacementResponseTransfer $quoteReplacementResponseTransfer): array
    {
        $quoteItemTransfersForReplacement = [];
        foreach ($quoteReplacementResponseTransfer->getQuoteOrFail()->getItems() as $itemTransfer) {
            if (!$this->isQuoteItemApplicable($itemTransfer)) {
                continue;
            }

            $quoteItemTransfersForReplacement[] = $itemTransfer;
        }

        return $quoteItemTransfersForReplacement;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return bool
     */
    protected function isQuoteItemApplicable(ItemTransfer $itemTransfer): bool
    {
        return $itemTransfer->getShipmentType()
            && !$this->isItemProductBundle($itemTransfer)
            && $itemTransfer->getProductOfferReference()
            && $itemTransfer->getShipmentTypeOrFail()->getKey() === $this->clickAndCollectExampleConfig->getDeliveryShipmentTypeKey();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param list<\Generated\Shared\Transfer\ItemTransfer> $quoteItemTransfersForReplacement
     *
     * @return \Generated\Shared\Transfer\ProductOfferServicePointCriteriaTransfer
     */
    protected function createProductOfferServicePointCriteriaTransfer(
        QuoteTransfer $quoteTransfer,
        array $quoteItemTransfersForReplacement
    ): ProductOfferServicePointCriteriaTransfer {
        $productOfferServicePointCriteriaTransfer = (new ProductOfferServicePointCriteriaTransfer())
            ->setStoreName($quoteTransfer->getStoreOrFail()->getNameOrFail())
            ->setShipmentTypeKey($this->clickAndCollectExampleConfig->getDeliveryShipmentTypeKey())
            ->setCurrencyCode($quoteTransfer->getCurrencyOrFail()->getCodeOrFail())
            ->setPriceMode($quoteTransfer->getPriceModeOrFail());

        foreach ($quoteItemTransfersForReplacement as $itemTransfer) {
            $productOfferServicePointCriteriaTransfer
                ->addConcreteSku($itemTransfer->getSkuOrFail());
        }

        return $productOfferServicePointCriteriaTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return bool
     */
    protected function isItemProductBundle(ItemTransfer $itemTransfer): bool
    {
        return $itemTransfer->getRelatedBundleItemIdentifier() || $itemTransfer->getBundleItemIdentifier();
    }
}
