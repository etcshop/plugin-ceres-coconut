<?php

namespace CeresCoconut\Contexts;

use IO\Helper\Utils;
use IO\Helper\ContextInterface;
use IO\Services\CategoryService;
use IO\Services\CustomerService;
use Plenty\Plugin\ConfigRepository;
use Ceres\Contexts\SingleItemContext;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;

use IO\Services\ItemSearch\Services\ItemSearchService;
use IO\Services\ItemSearch\SearchPresets\CrossSellingItems;

class CoconutSingleItemContext extends SingleItemContext implements ContextInterface
{
    public $item;
    public $attributes;
    public $variations;
    public $attributeNameMap;
    public $customerShowNetPrices;
    public $defaultCategory;
    public $assetName = "ceres-item";
    public $dynamicVariationId;
    public $initPleaseSelectOption;
    public $isItemSet;
    public $ETCItemData;
    public $itemData;

    public $accessory;
    public $similar;
    public $replacementPart;

    public function init($params)
    {
        parent::init($params);

        /** @var CustomerService $customerService */
        $customerService = pluginApp(CustomerService::class);
        /** @var ConfigRepository $configRepository */
        $configRepository = pluginApp(ConfigRepository::class);

        $this->dynamicVariationId = intval($params['dynamic']['documents'][0]['id'] ?? 0);
        $this->initPleaseSelectOption = $this->getParam('initPleaseSelectOption', false);

        $this->item = $params['item'];
        $itemData = $this->item['documents'][0]['data'];

        $availabiltyId = $itemData['variation']['availability']['id'];
        $mappedAvailability = $configRepository->get('Ceres.availability.mapping.availability' . $availabiltyId);
        $this->item['documents'][0]['data']['variation']['availability']['mappedAvailability'] = $mappedAvailability;

        $this->isItemSet = $params['isItemSet'];

        $this->attributes = $params['variationAttributeMap']['attributes'];
        $this->variations = $params['variationAttributeMap']['variations'];
        $this->customerShowNetPrices = $customerService->showNetPrices();

        $defaultCategoryId = 0;
        $plentyId = Utils::getPlentyId();
        foreach ($this->item['documents'][0]['data']['defaultCategories'] as $category) {
            if ($category['plentyId'] == $plentyId) {
                $defaultCategoryId = $category['id'];
                break;
            }
        }

        if ($defaultCategoryId > 0) {
            /** @var CategoryService $categoryService */
            $categoryService = pluginApp(CategoryService::class);
            $this->defaultCategory = $categoryService->get($defaultCategoryId);
        }

        $this->bodyClasses[] = "item-" . $itemData['item']['id'];
        $this->bodyClasses[] = "variation-" . $itemData['variation']['id'];

//ETC eigene Änderungen
        $itemRep =  pluginApp(ItemRepositoryContract::class);
        $this->ETCItemData = $itemRep->show($itemData['item']['id']);

        //Weitere CrossSelling-Liste (Ersatzteil)
        $options = array(
            "itemId" => $this->item['documents'][0]['data']['item']['id'],
            "relation" => "ReplacementPart"      // Nutze die Liste Zubehoer
        );
        $searchfactory = CrossSellingItems::getSearchFactory( $options );
        $searchfactory->setPage(1, 4); // Begrenze auf 4 Artikel
        $result = pluginApp(ItemSearchService::class)->getResult($searchfactory);
        $this->replacementPart = $result['documents'];

        //Weitere CrossSelling-Liste (Ähnlich)
        $options = array(
            "itemId" => $this->item['documents'][0]['data']['item']['id'],
            "relation" => "Similar"      // Nutze die Liste Zubehoer
        );
        $searchfactory = CrossSellingItems::getSearchFactory( $options );
        $searchfactory->setPage(1, 3); // Begrenze auf 4 Artikel
        $result = pluginApp(ItemSearchService::class)->getResult($searchfactory);
        $this->similar = $result['documents'];

        //Weitere CrossSelling-Liste (Ähnlich)
        $options = array(
            "itemId" => $this->item['documents'][0]['data']['item']['id'],
            "relation" => "Accessory"      // Nutze die Liste Zubehoer
        );
        $searchfactory = CrossSellingItems::getSearchFactory( $options );
        $searchfactory->setPage(1, 4); // Begrenze auf 4 Artikel
        $result = pluginApp(ItemSearchService::class)->getResult($searchfactory);
        $this->accessory = $result['documents'];
    }
}
