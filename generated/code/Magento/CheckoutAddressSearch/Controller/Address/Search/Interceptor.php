<?php
namespace Magento\CheckoutAddressSearch\Controller\Address\Search;

/**
 * Interceptor class for @see \Magento\CheckoutAddressSearch\Controller\Address\Search
 */
class Interceptor extends \Magento\CheckoutAddressSearch\Controller\Address\Search implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Customer\Model\Metadata\FormFactory $formFactory, \Magento\Customer\Api\AddressRepositoryInterface $addressRepository, \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory, \Magento\Customer\Api\Data\RegionInterfaceFactory $regionDataFactory, \Magento\Framework\Reflection\DataObjectProcessor $dataProcessor, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Controller\Result\JsonFactory $resultFactory, \Magento\CheckoutAddressSearch\Model\AddressSearch $addressSearch, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Authorization\Model\UserContextInterface $userContext, \Psr\Log\LoggerInterface $logger, \Magento\Customer\Model\Address\CustomAttributesProcessor $customAttributesProcessor)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $formKeyValidator, $formFactory, $addressRepository, $addressDataFactory, $regionDataFactory, $dataProcessor, $dataObjectHelper, $resultForwardFactory, $resultPageFactory, $resultFactory, $addressSearch, $customerRepository, $userContext, $logger, $customAttributesProcessor);
    }

    /**
     * {@inheritdoc}
     */
    public function execute() : \Magento\Framework\Controller\ResultInterface
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
