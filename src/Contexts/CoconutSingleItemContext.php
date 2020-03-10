<?php

namespace CeresCoconut\Contexts;

use IO\Helper\ContextInterface;
use Ceres\Contexts\SingleItemContext;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;
use Plenty\Plugin\Log\Loggable;

use IO\Services\ItemSearch\Services\ItemSearchService;
use IO\Services\ItemSearch\SearchPresets\CrossSellingItems;

class CoconutSingleItemContext extends SingleItemContext implements ContextInterface
{
    public $item;

    public $variations;
    public $attributeNameMap;
    public $customerShowNetPrices;
    public $ETCItemData;
    public $itemData;

    public $accessory;
    public $similar;
    public $replacementPart;

    use Loggable;

    public function init($params)
    {
        parent::init($params);
        $this->getLogger(__CLASS__)->error("Coconut Context is Calling", [
            "assetName" => $this->assetName,
            "buildHash" => $this->buildHash
        ] );
        $this->item = $params['item'];
        $itemData = $this->item['documents'][0]['data'];

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
