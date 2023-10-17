<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerExample\Zed\ClickAndCollectExample\Business;

use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \SprykerExample\Zed\ClickAndCollectExample\Business\ClickAndCollectExampleBusinessFactory getFactory()
 * @method \SprykerExample\Zed\ClickAndCollectExample\Persistence\ClickAndCollectExampleRepositoryInterface getRepository()
 */
class ClickAndCollectExampleFacade extends AbstractFacade implements ClickAndCollectExampleFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function replacePickupQuoteItemProductOffers(QuoteTransfer $quoteTransfer): QuoteResponseTransfer
    {
        return $this->getFactory()->createPickupQuoteItemReplacer()->replaceQuoteItemProductOffers($quoteTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function replaceDeliveryQuoteItemProductOffers(QuoteTransfer $quoteTransfer): QuoteResponseTransfer
    {
        return $this->getFactory()->createDeliveryQuoteItemReplacer()->replaceQuoteItemProductOffers($quoteTransfer);
    }
}
