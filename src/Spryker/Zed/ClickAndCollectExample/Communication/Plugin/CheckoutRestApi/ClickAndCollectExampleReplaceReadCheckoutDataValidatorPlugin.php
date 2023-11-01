<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerExample\Zed\ClickAndCollectExample\Communication\Plugin\CheckoutRestApi;

use Generated\Shared\Transfer\CheckoutDataTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Spryker\Zed\CheckoutRestApiExtension\Dependency\Plugin\ReadCheckoutDataValidatorPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \SprykerExample\Zed\ClickAndCollectExample\ClickAndCollectExampleConfig getConfig()
 * @method \SprykerExample\Zed\ClickAndCollectExample\Persistence\ClickAndCollectExampleRepositoryInterface getRepository()
 * @method \SprykerExample\Zed\ClickAndCollectExample\Business\ClickAndCollectExampleFacadeInterface getFacade()
 * @method \SprykerExample\Zed\ClickAndCollectExample\Communication\ClickAndCollectExampleCommunicationFactory getFactory()
 */
class ClickAndCollectExampleReplaceReadCheckoutDataValidatorPlugin extends AbstractPlugin implements ReadCheckoutDataValidatorPluginInterface
{
    /**
     * {@inheritDoc}
     * - Requires `CheckoutDataTransfer.quote` to be set.
     * - Collects product offers with service point, shipment, shipment type and shipment method.
     * - Replaces filtered product offers with suitable product offers from Persistence.
     * - Does not modify original quote.
     * - Returns errors in case any of items can not be replaced.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CheckoutDataTransfer $checkoutDataTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function validateCheckoutData(CheckoutDataTransfer $checkoutDataTransfer): CheckoutResponseTransfer
    {
        return $this->getFacade()->validateQuoteItemProductOfferReplacement($checkoutDataTransfer);
    }
}
