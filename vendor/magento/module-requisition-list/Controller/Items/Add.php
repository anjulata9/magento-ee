<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\RequisitionList\Controller\Items;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\RequisitionList\Api\RequisitionListRepositoryInterface;
use Magento\RequisitionList\Model\Action\RequestValidator;
use Magento\RequisitionList\Model\RequisitionListItem\Options\Builder\ConfigurationException;
use Magento\RequisitionList\Model\RequisitionListItem\SaveHandler;
use Magento\RequisitionList\Model\RequisitionListProduct;
use Psr\Log\LoggerInterface;

/**
 * Add products to the requisition list.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class Add implements HttpPostActionInterface
{
    /**
     * @var RequestValidator
     */
    private $requestValidator;

    /**
     * @var SaveHandler
     */
    private $requisitionListItemSaveHandler;

    /**
     * @var RequisitionListProduct
     */
    private $requisitionListProduct;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var RequisitionListRepositoryInterface
     */
    private $requisitionListRepository;

    /**
     * @var Json
     */
    private $jsonHelper;

    /**
     * @var Redirect
     */
    private $resultRedirect;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param ResultFactory $resultFactory
     * @param RequestValidator $requestValidator
     * @param SaveHandler $requisitionListItemSaveHandler
     * @param RequisitionListProduct $requisitionListProduct
     * @param LoggerInterface $logger
     * @param RequestInterface $request
     * @param MessageManagerInterface $messageManager
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param Json $jsonHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ResultFactory $resultFactory,
        RequestValidator $requestValidator,
        SaveHandler $requisitionListItemSaveHandler,
        RequisitionListProduct $requisitionListProduct,
        LoggerInterface $logger,
        RequestInterface $request,
        MessageManagerInterface $messageManager,
        RequisitionListRepositoryInterface $requisitionListRepository,
        Json $jsonHelper,
        UrlInterface $urlBuilder
    ) {
        $this->resultFactory = $resultFactory;
        $this->requestValidator = $requestValidator;
        $this->requisitionListItemSaveHandler = $requisitionListItemSaveHandler;
        $this->requisitionListProduct = $requisitionListProduct;
        $this->logger = $logger;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->requisitionListRepository = $requisitionListRepository;
        $this->jsonHelper = $jsonHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->isRequestValid()) {
            return $this->resultRedirect;
        }

        $listId = $this->request->getParam('list_id');
        $listName = $this->request->getParam('list_name');

        if (!$listId) {
            $this->messageManager->addErrorMessage(__('The requisition list does not exist.'));
            $this->resultRedirect->setPath('requisition_list/requisition/index');
            return $this->resultRedirect;
        }

        try {
            $requisitionList = $this->requisitionListRepository->get($listId);

            $this->processMultipleProductData($this->getPreparedMultipleProductDataFromRequest(), $listId);

            $requisitionListUrl = $this->urlBuilder->getUrl(
                'requisition_list/requisition/view',
                ['requisition_id' => $requisitionList->getId()]
            );

            $this->messageManager->addComplexSuccessMessage(
                'addShoppingCartToRequisitionListSuccessMessage',
                [
                    'requisition_list_url' => $requisitionListUrl,
                    'requisition_list_name' => $listName
                ]
            );
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->critical($e);
        } catch (ConfigurationException $e) {
            $this->messageManager->addWarningMessage($e->getMessage());
            $this->resultRedirect->setPath('checkout/cart');
            return $this->resultRedirect;
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __(
                    'All items in your Shopping Cart could not be added to the "%1" requisition list.',
                    $listName
                )
            );
            $this->logger->critical($e);
        }

        return $this->resultRedirect->setRefererUrl();
    }

    /**
     * Check if request is valid
     *
     * @return bool
     */
    private function isRequestValid()
    {
        $resultRedirect = $this->requestValidator->getResult($this->request);
        $isValid = !$resultRedirect;

        if (!$isValid) {
            $this->resultRedirect = $resultRedirect;
            return false;
        }

        if (!$this->validateProductData()) {
            $this->messageManager->addErrorMessage(__('One or more products in your Shopping Cart is invalid.'));
            $this->resultRedirect->setPath('requisition_list/requisition/index');
            return false;
        }

        return true;
    }

    /**
     * Get prepared multiple product data provided in the request.
     *
     * @return DataObject[]
     */
    private function getPreparedMultipleProductDataFromRequest()
    {
        return $this->requisitionListProduct->prepareMultipleProductData(
            $this->jsonHelper->unserialize($this->request->getParam('product_data'))
        );
    }

    /**
     * Process product data and individually save each item to requisition list
     *
     * @param DataObject[] $productData
     * @param int $listId
     */
    private function processMultipleProductData($productData, $listId)
    {
        foreach ($productData as $product) {
            $options = is_array($product->getOptions()) ? $product->getOptions() : [];
            $this->requisitionListItemSaveHandler->saveItem($product, $options, 0, $listId);
        }
    }

    /**
     * Check if all products in specified product data are valid.
     *
     * @return bool
     */
    private function validateProductData()
    {
        $isValid = true;
        $productData = $this->requisitionListProduct->prepareMultipleProductData(
            $this->jsonHelper->unserialize($this->request->getParam('product_data'))
        );

        foreach ($productData as $product) {
            $product = $this->requisitionListProduct->getProduct($product->getSku());
            if (empty($product)) {
                $isValid = false;
                break;
            }
        }

        return $isValid;
    }
}
