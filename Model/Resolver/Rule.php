<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CountdownTimerGraphQl
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

declare(strict_types=1);

namespace Mageplaza\CountdownTimerGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\CountdownTimer\Api\RuleRepositoryInterface;
use Mageplaza\CountdownTimer\Helper\Data;
use Mageplaza\CountdownTimer\Model\Config\Source\PageView;

/**
 * Class Rules
 * @package Mageplaza\CountdownTimerGraphQl\Model\Resolver
 */
class Rule implements ResolverInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * Rule constructor.
     *
     * @param Data $helperData
     * @param RuleRepositoryInterface $ruleRepository
     */
    public function __construct(
        Data $helperData,
        RuleRepositoryInterface $ruleRepository
    ) {
        $this->helperData     = $helperData;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!$this->helperData->isEnabled()) {
            return [];
        }

        $pageType = $field->getName() === 'mp_countdown_timer_catalog_rule'
            ? PageView::CATALOG_VIEW : PageView::PRODUCT_VIEW;

        try {
            return $this->ruleRepository->getByProduct($context->getUserId(), $pageType, $value['model']->getId());
        } catch (\Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
