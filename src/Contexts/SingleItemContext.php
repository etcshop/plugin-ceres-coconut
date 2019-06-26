<?php

namespace CeresCoconut\Contexts;

use IO\Helper\ContextInterface;
use Ceres\Contexts\SingleItemContext;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;

use IO\Services\ItemSearch\Services\ItemSearchService;
use IO\Services\ItemSearch\SearchPresets\CrossSellingItems;

class CoconutSingleItemContext extends SingleItemContext implements ContextInterface
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
