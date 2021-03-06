<?php
/**
 * Tribugs_QuickView
 *
 * @category    Tribugs
 * @package     Tribugs_QuickView
 * @Date        07/2021
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @author      @diazwatson
 */

declare(strict_types=1);

namespace Tribugs\QuickView\Block\Catalog\Category\View;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\ProductList\Item\Block as ProductListBlock;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Registry;

class QuickView extends ProductListBlock
{

    const SYSTEM_CONFIG_PATH = 'tribugs_quickview/general/';

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @param Context  $context
     * @param Registry $coreRegistry
     */

    public function __construct(
        Context $context,
        Registry $coreRegistry
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getButtonText()
    {
        return $this->getConfig('btn_text');
    }

     /**
     * @return string
     */
    public function checkEnable()
    {
        return $this->getConfig('enabled');
    }

   /**
     * @param $field string
     *
     * @return \Magento\Framework\App\Config
     */
    public function getConfig($field)
    {
        return $this->_scopeConfig->getValue(
            self::SYSTEM_CONFIG_PATH . $field,
            ScopeInterface::SCOPE_STORE
        );
    }

    
}
