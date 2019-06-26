<?php

namespace Ceres\Contexts;

use IO\Helper\ContextInterface;
use IO\Services\CustomerService;
use IO\Services\ItemService;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;

use Plenty\Plugin\Log\Loggable;

use IO\Services\ItemSearch\Services\ItemSearchService;
use IO\Services\ItemSearch\SearchPresets\CrossSellingItems;
use IO\Services\ItemSearch\SearchPresets\SingleItem;

class SingleItemContext extends GlobalContext implements ContextInterface
{
    public $item;

    public $variations;
    public $attributeNameMap;
    public $customerShowNetPrices;
    public $ETCItemData;

    use Loggable;
    public $accessory;
    public $similar;
    public $replacementPart;

    public function init($params)
    {
        parent::init($params);

        /** @var CustomerService $customerService */
        $customerService = pluginApp(CustomerService::class);

        $this->item = $params['item'];
        $itemData = $this->item['documents'][0]['data'];

        /** @var ItemService $itemService */
        $itemService = pluginApp(ItemService::class);

        $this->variations = $itemService->getVariationAttributeMap($itemData['item']['id']);
        $this->attributeNameMap = $itemService->getAttributeNameMap($itemData['item']['id']);
        $this->customerShowNetPrices = $customerService->showNetPrices();

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
