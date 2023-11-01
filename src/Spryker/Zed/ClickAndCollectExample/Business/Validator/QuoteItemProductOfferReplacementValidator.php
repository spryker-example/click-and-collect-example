<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerExample\Zed\ClickAndCollectExample\Business\Validator;

use Generated\Shared\Transfer\CheckoutDataTransfer;
use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\QuoteReplacementResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerExample\Zed\ClickAndCollectExample\Business\Expander\ItemExpanderInterface;
use SprykerExample\Zed\ClickAndCollectExample\Business\Replacer\QuoteProductOfferReplacerInterface;

class QuoteItemProductOfferReplacementValidator implements QuoteItemProductOfferReplacementValidatorInterface
{
    /**
     * @var \SprykerExample\Zed\ClickAndCollectExample\Business\Replacer\QuoteProductOfferReplacerInterface
     */
    protected QuoteProductOfferReplacerInterface $quoteProductOfferReplacer;

    /**
     * @var \SprykerExample\Zed\ClickAndCollectExample\Business\Expander\ItemExpanderInterface
     */
    protected ItemExpanderInterface $itemExpander;

    /**
     * @param \SprykerExample\Zed\ClickAndCollectExample\Business\Expander\ItemExpanderInterface $itemExpander
     * @param \SprykerExample\Zed\ClickAndCollectExample\Business\Replacer\QuoteProductOfferReplacerInterface $quoteProductOfferReplacer
     */
    public function __construct(
        ItemExpanderInterface $itemExpander,
        QuoteProductOfferReplacerInterface $quoteProductOfferReplacer
    ) {
        $this->itemExpander = $itemExpander;
        $this->quoteProductOfferReplacer = $quoteProductOfferReplacer;
    }

    /**
     * @param \Generated\Shared\Transfer\CheckoutDataTransfer $checkoutDataTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function validate(CheckoutDataTransfer $checkoutDataTransfer): CheckoutResponseTransfer
    {
        $checkoutResponseTransfer = (new CheckoutResponseTransfer())->setIsSuccess(true);
        $itemCollectionTransfer = (new ItemCollectionTransfer())->fromArray($checkoutDataTransfer->getQuoteOrFail()->toArray(), true);
        $itemCollectionTransfer = $this->itemExpander->expandItemCollectionWithServicePoint($itemCollectionTransfer, $checkoutDataTransfer);
        $itemCollectionTransfer = $this->itemExpander->expandItemCollectionWithShipment($itemCollectionTransfer, $checkoutDataTransfer);
        $itemCollectionTransfer = $this->getValidItems($itemCollectionTransfer);

        /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\ItemTransfer> $itemTransfers */
        $itemTransfers = $itemCollectionTransfer->getItems();

        if (!$itemTransfers->count()) {
            return $checkoutResponseTransfer;
        }

        $quoteTransfer = (new QuoteTransfer())
            ->fromArray($checkoutDataTransfer->getQuoteOrFail()->toArray(), true)
            ->setItems($itemTransfers);

        $quoteReplacementResponseTransfer = $this->quoteProductOfferReplacer->replaceQuoteItemProductOffers($quoteTransfer);

        return $this->mapQuoteReplacementResponseToCheckoutResponse($quoteReplacementResponseTransfer, $checkoutResponseTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteReplacementResponseTransfer $quoteReplacementResponseTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    protected function mapQuoteReplacementResponseToCheckoutResponse(
        QuoteReplacementResponseTransfer $quoteReplacementResponseTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): CheckoutResponseTransfer {
        /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\QuoteErrorTransfer> $quoteErrorTransfers */
        $quoteErrorTransfers = $quoteReplacementResponseTransfer->getErrors();

        if (!$quoteErrorTransfers->count()) {
            return $checkoutResponseTransfer;
        }

        foreach ($quoteErrorTransfers as $errorTransfer) {
            $checkoutResponseTransfer->addError((new CheckoutErrorTransfer())->fromArray($errorTransfer->toArray()));
        }

        return $checkoutResponseTransfer->setIsSuccess(false);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemCollectionTransfer $itemCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\ItemCollectionTransfer
     */
    protected function getValidItems(ItemCollectionTransfer $itemCollectionTransfer): ItemCollectionTransfer
    {
        $validItemCollectionTransfer = new ItemCollectionTransfer();

        foreach ($itemCollectionTransfer->getItems() as $itemTransfer) {
            if (!$itemTransfer->getShipment() || !$itemTransfer->getShipmentType()) {
                continue;
            }

            $validItemCollectionTransfer->addItem($itemTransfer);
        }

        return $validItemCollectionTransfer;
    }
}
