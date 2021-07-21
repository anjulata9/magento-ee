<?php
namespace Magento\SharedCatalog\Controller\Adminhtml\SharedCatalog\Company\MassAssign;

/**
 * Interceptor class for @see \Magento\SharedCatalog\Controller\Adminhtml\SharedCatalog\Company\MassAssign
 */
class Interceptor extends \Magento\SharedCatalog\Controller\Adminhtml\SharedCatalog\Company\MassAssign implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Ui\Component\MassAction\Filter $filter, \Magento\SharedCatalog\Ui\DataProvider\Collection\Grid\CompanyFactory $collectionFactory, \Magento\SharedCatalog\Model\Form\Storage\CompanyFactory $companyStorageFactory, \Psr\Log\LoggerInterface $logger)
    {
        $this->___init();
        parent::__construct($context, $resultJsonFactory, $filter, $collectionFactory, $companyStorageFactory, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute();
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        return $pluginInfo ? $this->___callPlugins('dispatch', func_get_args(), $pluginInfo) : parent::dispatch($request);
    }
}