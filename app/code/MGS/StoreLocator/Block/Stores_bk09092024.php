<?php

namespace MGS\StoreLocator\Block;

use MGS\StoreLocator\Model\StoreFactory;
use Magento\Framework\Session\SessionManager as SessionManager;

class Stores extends \Magento\Framework\View\Element\Template
{
    protected $_countryFactory;
    protected $_storeFactory;
    protected $_sessionManager;
    protected $_helperImage;
    protected $_storeCollection = null;
    protected $_perPage = 10;
    protected $_curl;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        \Magento\Directory\Model\Country $country,
        \Magento\Catalog\Helper\Image $helperImage,
        StoreFactory $storeFactory,
        SessionManager $sessionManager,
        \Magento\Framework\HTTP\Client\Curl $curl,
        array $data = []
    )
    {
        $this->_countryFactory = $countryFactory;
        $this->country = $country;
        $this->_storeFactory = $storeFactory;
        $this->_helperImage = $helperImage;
        $this->_sessionManager = $sessionManager;
        $this->_curl = $curl;
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getCountries()
    {
        return $this->_countryFactory->toOptionArray();
    }

    public function getRegionByCountry($countryCode)
    {
        $regionCollection = $this->country->loadByCode($countryCode)->getRegions();
        $regions = $regionCollection->loadData()->toOptionArray(false);
        return $regions;
    }

    protected function _getStoreCollection()
    {
        $collection = $this->_storeFactory->create()->getCollection();
        $collection->addFieldToFilter('status', 1)
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('name', 'asc');
        return $collection;
    }

    public function getStoreCollectionForSiteMap()
    {
        $collection = $this->_storeFactory->create()->getCollection();
        $collection->addFieldToFilter('status', 1)
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('name', 'asc');
        return $collection;
    }

    public function getStoreCollectionWithStateFilter()
    {
        $col = [];
        if (!is_null($this->_storeCollection)) {
            $strCollection = $this->_getStoreCollection();
            foreach($strCollection as $strCol){
                $arr['store_id'] = $strCol->getId();
                $arr['name'] = $strCol->getName();
                $arr['subtitle'] = $strCol->getSubtitle();
                $arr['street_address'] = $strCol->getStreetAddress();
                $arr['city'] = $strCol->getCity();
                $arr['state'] = $strCol->getState();
                $arr['zipcode'] = $strCol->getZipcode();
                $arr['country'] = $strCol->getCountry();
                $arr['phone_number'] = $strCol->getPhoneNumber();
                $arr['latitude'] = $strCol->getLat();
                $arr['longitude'] = $strCol->getLng();
                $arr['display_url'] = $strCol->getDisplayUrl();
                $col[$strCol->getState()][] = $arr;
            }
            ksort($col);
        }
        return $col;
    }

    public function getStateStore($state)
    {
        $data = $this->getRequest()->getParams();
        if (is_null($this->_storeCollection)) {
            $this->_storeCollection = $this->_getStoreCollection();
            $this->_storeCollection->setPageSize($this->_perPage)
                ->setCurPage($this->getCurrentPage());
            if(!empty($state)) {
                $this->_storeCollection->addFieldToFilter('state', ['like' => '%' .$state. '%']);
            }
        }
        return $this->_storeCollection;   
    }

    public function getStoreCollection()
    {
        $data = $this->getRequest()->getParams();
        $builderName = $builderCity = array();
        if (!empty($data['location'])) {
                $tokenData = $this->salesForceLogin();
                if ($tokenData['status'] === true) {
                    $token = $tokenData['data']['access_token'];
                    $builderData = $this->getBuilderDetailsFromSalesForce($data['location'], $token);
                    if ($builderData != false) {
                        $dataSales = json_decode($builderData, true);
                        if ($dataSales['totalSize'] > 0) {
                            foreach ($dataSales['records'] as $r) {
                                if (!empty($r['Builder__r']['Name'])) {
                                    $builderName[] = $r['Builder__r']['Name'];
                                    $builderCity[] = $r['Builder__r']['Design_Studio_City__c'];
                                }
                            }
                        }
                    }
                }
        }
        if (is_null($this->_storeCollection)) {
            $this->_storeCollection = $this->_getStoreCollection();
            if (count($builderName) > 0 || count($builderCity) > 0) {
                $data['location'] = '';
                $bName = implode('|', $builderName);
                $bCity = implode('|', $builderCity);
                $field = $value = [];
                if (!empty($bName) && !empty($bCity)) {
                    $field = ['name', 'subtitle', 'city'];
                    $value = [['regexp' => $bName], ['regexp' => $bName], ['regexp' => $bCity]];
                } else {
                    if (!empty($bName)) {
                        $field = ['name', 'subtitle'];
                        $value = [['regexp' => $bName], ['regexp' => $bName]];
                    }
                    if (!empty($bCity)) {
                        $field = ['city'];
                        $value = [['regexp' => $bCity]];
                    }
                }
                if ($field && $value) {
                    $this->_storeCollection->addFieldToFilter($field, $value);
                }
            }
            if(!empty($data['state'])) {
                $this->_storeCollection->addFieldToFilter('state', ['like' => '%' .str_replace('-', ' ', $data['state']). '%']);
            }
            if(!empty($data['location'])){
                $this->_storeCollection->addFieldToFilter(['street_address', 'city', 'zipcode'], [
                        ['like' => '%' .$data['location']. '%'],
                        ['like' => '%' .$data['location']. '%'],
                        ['like' => '%' .$data['location']. '%']
                    ]);
            }
        }
        return $this->_storeCollection;
    }

    public function getImageUrl($image = null) {
        if(empty($image)) {
            return $this->_helperImage->getDefaultPlaceholderUrl('image');
        }
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$image;
    }

    /**
     * Fetch the current page for the stores list
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getRequest()->getParam('p', false) ? $this->getRequest()->getParam('p') : 1;
    }

    /**
     * Get a pager
     *
     * @return string|null
     */
    public function getPager()
    {
        $pager = $this->getChildBlock('store.list.pager');
        if ($pager) {
            $storesPerPage = $this->_perPage;
            $pager->setAvailableLimit(array($storesPerPage => $storesPerPage));
            $pager->setTotalNum($this->getStoreCollection()->getSize());
            $pager->setCollection($this->getStoreCollection());
            $pager->setShowPerPage(true);
            $pager->setShowAmounts(true);
            return $pager->toHtml();
        }

        return null;
    }

    /**
     * Return store radius/default radius
     *
     * @param MGS_Storelocator_Model_Store $storelocator
     * @return int
     */
    public function getRadius($storelocator = NULL)
    {
        //Return store radius
        if (!is_null($storelocator)) {
            if ($storelocator->getRadius()) {
                return $storelocator->getRadius();
            } else {
                // Return default config radius
                return 100;
            }
        } else {
            // Return default config radius
            return 100;
        }
        return;
    }

    /**
     * Return store zoom level/default zoom level
     *
     * @param MGS_Storelocator_Model_Storelocator $storelocator
     * @return int
     */
    public function getZoomLevel($storelocator=NULL)
    {
        //Return store zoom level
        if(!is_null($storelocator)) {
            if($storelocator->getZoomLevel()){
                return $storelocator->getZoomLevel();
            } else {
                // Return default config zoom level
                return 14;
            }
        } else {
            // Return default config zoom level
            return 14;
        }
        return;
    }

    private function getBuilderDetailsFromSalesForce($location, $token)
    {
        $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);

        $request = new \Zend\Http\Request();
        $request->setHeaders($httpHeaders);
        $request->setUri('https://wausauhomes.my.salesforce.com/services/data/v50.0/query');
        $request->setMethod(\Zend\Http\Request::METHOD_GET);
        if(is_numeric($location)){
            $params = new \Zend\Stdlib\Parameters([
                'q' => "SELECT Id, Zip__c, Builder__c, Builder__r.Name, Builder__r.Design_Studio_City__c FROM Terr_Zip__c where Zip__c = '" . $location . "'"
            ]);
        }else{
            $params = new \Zend\Stdlib\Parameters([
                'q' => "SELECT Id, Zip__c,City__c, Builder__c, Builder__r.Name, Builder__r.Design_Studio_City__c FROM Terr_Zip__c where City__c = '" . $location . "'"
            ]);
        }
        $request->setQuery($params);

        $client = new \Zend\Http\Client();
        $options = [
            'adapter'   => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
            'maxredirects' => 0,
            'timeout' => 30
        ];
        $client->setOptions($options);

        $response = $client->send($request);

        if ($response->getStatusCode() == 200) {
            return $response->getBody();
        }
        return false;
    }

    private function salesForceLogin()
    {
        $response = array();
        $url = 'https://login.salesforce.com/services/oauth2/token';
        $params = [
            'grant_type' => 'refresh_token',
            'client_id' => '3MVG9LBJLApeX_PCQjziKoun0Vu5HW_KDi2KvCijVCh_85dL9pvGsTgEYaaVrY_hXioC9SPe1EoyMLUFSWa7h',
            'client_secret' => '10FA4E71B471F649958E0D2A730250CB529E128517AA1DF9BC44E0C457C824AC',
            'format' => 'json',
            'refresh_token' => '5Aep8615B7Psrq3qbm04OJw9ZOlccRo9m7tnxGCK5saLDBFmj.lXOBuggxQTsFgHMLjAb4D42lLYjJWbJHfzrRZ'
        ];
        $this->_curl->post($url, $params);
        $result = $this->_curl->getBody();
        if ($this->_curl->getStatus() != 200) {
            $response['status'] = false;
            $response['message'] = 'No result Found';
            $response['data'] = '';
        } else {
            $response['status'] = true;
            $response['message'] = 'Result Found';
            $response['data'] = json_decode($result, true);
        }
        return $response;
    }

}
