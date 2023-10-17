<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerExample\Client\ClickAndCollectExample\Dependency\Client;

use Generated\Shared\Transfer\ProductOfferStorageCollectionTransfer;
use Generated\Shared\Transfer\ProductOfferStorageCriteriaTransfer;

interface ClickAndCollectExampleToProductOfferStorageClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\ProductOfferStorageCriteriaTransfer $productOfferStorageCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ProductOfferStorageCollectionTransfer
     */
    public function getProductOfferStoragesBySkus(
        ProductOfferStorageCriteriaTransfer $productOfferStorageCriteriaTransfer
    ): ProductOfferStorageCollectionTransfer;
}
