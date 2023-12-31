<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerExample\Zed\ClickAndCollectExample\Business\ErrorAdder;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\QuoteReplacementResponseTransfer;

class QuoteReplacementResponseErrorAdder implements QuoteReplacementResponseErrorAdderInterface
{
    /**
     * @var string
     */
    protected const GLOSSARY_KEY_ITEM_REPLACEMENT_NOT_FOUND = 'click_and_collect_example.error.item_replacement_not_found';

    /**
     * @var string
     */
    protected const GLOSSARY_KEY_PARAMETER_ITEM = '%item%';

    /**
     * @var int
     */
    protected const ITEM_NAME_TRUNCATED_SYMBOLS = 30;

    /**
     * @var string
     */
    protected const ITEM_NAME_POSTFIX = '...';

    /**
     * @param \Generated\Shared\Transfer\QuoteReplacementResponseTransfer $quoteReplacementResponseTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteReplacementResponseTransfer
     */
    public function addError(QuoteReplacementResponseTransfer $quoteReplacementResponseTransfer, ItemTransfer $itemTransfer): QuoteReplacementResponseTransfer
    {
        $quoteReplacementResponseTransfer->addError($this->createQuoteErrorTransfer(
            static::GLOSSARY_KEY_ITEM_REPLACEMENT_NOT_FOUND,
            [
                static::GLOSSARY_KEY_PARAMETER_ITEM => $this->getTruncatedItemTransferName($itemTransfer),
            ],
        ));

        return $quoteReplacementResponseTransfer;
    }

    /**
     * @param string $error
     * @param array<string, mixed> $parameters
     *
     * @return \Generated\Shared\Transfer\QuoteErrorTransfer
     */
    protected function createQuoteErrorTransfer(string $error, array $parameters = []): QuoteErrorTransfer
    {
        return (new QuoteErrorTransfer())
            ->setMessage($error)
            ->setParameters($parameters);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return string
     */
    protected function getTruncatedItemTransferName(ItemTransfer $itemTransfer): string
    {
        return substr($itemTransfer->getNameOrFail(), 0, static::ITEM_NAME_TRUNCATED_SYMBOLS) . static::ITEM_NAME_POSTFIX;
    }
}
