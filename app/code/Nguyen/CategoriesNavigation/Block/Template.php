<?php
namespace Nguyen\CategoriesNavigation\Block;

use Magento\Framework\App\Filesystem\DirectoryList;

class Template extends \Magento\Framework\View\Element\Template {

    protected $_coreRegistry;
    
    protected $_catView;

    protected $_cat;

    protected $_catColFactory;

    protected $_prodColFactory;

    protected $_fileSystem;

    protected $_imageFactory;

    protected $_storeManager;

    protected $_imageHelper;

    protected $_catFactory;

    const CAT_NAV_MEDIA_PATH = 'catalog/category/navigation_element/';

    const CAT_MEDIA_PATH = 'catalog/category/';

    const PRODUCT_PLH_PATH = 'catalog/product/placeholder/';

    const PRODUCT_THUMB_PLH_CONFIG_PATH = 'catalog/placeholder/thumbnail_placeholder';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Block\Category\View $catView,
        \Magento\Catalog\Model\Category $cat,
        \Magento\Catalog\Model\CategoryFactory $catFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $catColFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $prodColFactory,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Catalog\Helper\Image $imageHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_catView = $catView;
        $this->_cat      = $cat;
        $this->_catColFactory = $catColFactory;
        $this->_prodColFactory = $prodColFactory;

        $this->_fileSystem   = $fileSystem;
        $this->_imageFactory = $imageFactory; 
        $this->_catFactory   = $catFactory; 
        $this->_storeManager = $storeManager;
        $this->_imageHelper  = $imageHelper;

        parent::__construct($context, $data);
    }

    public function loadNavEls(){

        $curCat = $this->getCurCat();

        $navElStr = $curCat->getHorizontalNavigationEl();

        if ($navElStr && $this->isJson($navElStr)){

            $navEl = $this->handleNavManual();

        }else{
            $navEl = $this->handleNavAuto($curCat);
        }

        return $navEl;
    }

    public function handleNavManual($navElStr){
        // json example

        $navEl = json_decode('[' . $navElStr .']', true);

        if (isset($navEl)){

            foreach($navEl as &$element){

                $subCat = $this->getCat($element['id']);

                $this->makeCatNavImage($subCat->getData('image'), 50);

                $element['image_url'] = $this->getCatNavImageUrl($subCat);
                
            }
        }

        return $navEl;
    }

    public function handleNavAuto($curCat){

        $subCats = $this->getDescendants($curCat, 1);

        $navEl = array();

        $idx = 1;

        foreach ($subCats as $subCat) {

            $subCat = $this->getCat($subCat->getData('entity_id'));

            $prodCol = $this->getProdColByCats($subCat->getData('entity_id'));

            if ($subCat['is_active'] == 1){

                $this->makeCatNavImage($subCat->getData('image'), 50);

                $navEl[$idx]['id']   = $subCat->getData('entity_id');
                $navEl[$idx]['name'] = $subCat->getData('name');
                $navEl[$idx]['link'] = $subCat->getUrl();

                $navEl[$idx]['image_url'] = $this->getCatNavImageUrl($subCat);
                
                $idx++;
            }
        }

        return $navEl;
    }

    public function makeCatNavImage($image, $height = null, $width = null) {

         try {
            $image = $this->processCatImageName($image);

            $catImagePath = $this->getMediaDir()->getAbsolutePath(self::CAT_MEDIA_PATH) . $image;

            if (!file_exists($catImagePath)) throw new \Exception('No category image path!');

            $catNavImagePath = $this->getMediaDir()->getAbsolutePath(self::CAT_NAV_MEDIA_PATH) . $image;

            if (file_exists($catNavImagePath)) throw new \Exception('No category navigation image path!');

            $imageResize = $this->_imageFactory->create();         
            $imageResize->open($catImagePath);
            $imageResize->constrainOnly(TRUE);         
            $imageResize->keepTransparency(TRUE);         
            $imageResize->keepFrame(FALSE);         
            $imageResize->keepAspectRatio(TRUE);         
            $imageResize->resize($width, $height);  

            //save image      
            $imageResize->save($catNavImagePath);         
        } 
        catch (\Exception $e) {

            $this->_logger->critical($e->getMessage());
        }
    }

    public function getCatNavImageUrl($subCat){

        $catNavImageUrl = '';
        
        try{

            if ($subCat->getImageUrl()){ 

                if ($this->findCatNavImage($subCat->getData('image'))){
                    // find it successfully    
                    $catNavImageUrl = str_replace(self::CAT_MEDIA_PATH, self::CAT_NAV_MEDIA_PATH, $subCat->getImageUrl());
                }else{

                    $catNavImageUrl = $this->findAlternativeForCatNavImageUrl($subCat);
                    if (strpos($catNavImageUrl, 'placeholder') !== false){
                        // if failed, try again
                        throw new \Exception('No category navigation image! Try to get default category image instead.');
                    }
                }
            }else{
                throw new \Exception('No category navigation image! Try to get default category image instead.');
            }
        }
        catch(\Exception $e){

            $catNavImageUrl = $this->manageGetDefaultCatImageUrl($subCat);
        }

        return $catNavImageUrl;
    }

    public function manageGetDefaultCatImageUrl($subCat){

        $catNavImageUrl = '';
        // load default category data
        $catCol = $this->_catColFactory->create();
        $catCol->addFieldToSelect("*")
            ->addAttributeToFilter('entity_id', $subCat->getEntityId())
            ->setStoreId(0)
            ->getSelect()
            ;

        if (count($catCol)){    
            // only 1 category
            foreach($catCol as $_subCat){

                $catNavImageUrl = $this->findAlternativeForCatNavImageUrl($_subCat);                 
            }
        }
        return $catNavImageUrl;
    }

    public function findAlternativeForCatNavImageUrl($subCat){

        $imageName = $this->processCatImageName($subCat->getData('image'));

        // load category image if category navigation image not found
        if ($this->findCatImage($imageName)){
            // find it successfully
            $catNavImageUrl = $subCat->getImageUrl();
        }else{
            // load placeholder image
            $catNavImageUrl = $this->getMediaUrl() . self::PRODUCT_PLH_PATH . $this->getConfig(self::PRODUCT_THUMB_PLH_CONFIG_PATH);
        }    

        return $catNavImageUrl;
    }

    public function findCatImage($image){

        if ($image && file_exists($this->getMediaDir()->getAbsolutePath(self::CAT_MEDIA_PATH) . $image)){

            return true;
        }
        return false;
    }

    public function findCatNavImage($image){

        $image = $this->processCatImageName($image);

        if ($image && file_exists($this->getMediaDir()->getAbsolutePath(self::CAT_NAV_MEDIA_PATH) . $image)){
            
            return true;
        }
        return false;
    }

    public function processCatImageName($imageName){
        // check if image name has "/"
        // e.g. /media/catalog/category/foto-2.png
        
        if ($imageName && strpos($imageName, '/') !== false) {

            $imageNames = explode('/', $imageName);
            $imageName = end($imageNames);
        }
        return $imageName;
    }


    public function isJson($string) {

        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }

    public function processCatIds($catIds){

        $catIds_array = explode(',', $catIds);

        foreach ($catIds_array as $key => &$value) {

            $value = preg_replace('/\s+/u', '', $value);

            if (!is_numeric($value)){

                unset($catIds_array[$key]);
            }
        }

        return $catIds_array;
    }

    public function getCats() {

        try {
            $catIds = $this->getData("catIds");
            
            if ($catIds){
                
                $catIds_array = $this->processCatIds($catIds);
                $catIds = implode(',', $catIds_array);

                $catCol = $this->_catColFactory->create();
                
                $catCol
                ->addFieldToSelect("*")
                ->addAttributeToFilter('entity_id', ['in' => $catIds])
                ->getSelect()
                ->order(new \Zend_Db_Expr('FIELD(e.entity_id, ' . $catIds .')'));

                return $catCol;
            }
            return NULL;


        } catch (\Exception $e) {
            $this->_logger->critical($e->getMessage());

            return NULL;
        }

    }

    public function getLoadedCatCol() {

        return $this->getCats();
    } 

    public function getMediaDir(){

        return $this->_fileSystem->getDirectoryRead(DirectoryList::MEDIA);
    }

    public function getMediaUrl(){

        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getConfig($config_path) {

        return $this->_storeManager->getStore()->getConfig($config_path);
    }

    public function getProdColByCats($ids)
    {
        $collection = $this->_prodColFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => $ids]);

        return $collection;
    }

    public function getCat($catId){

        return $this->_cat->load($catId);
    }

    public function getCurCat(){

        return $this->_catView->getCurrentCategory();
    }

    public function getDescendants($cat, $levels = 2) {

        if ((int)$levels < 1) {
            $levels = 1;
        }
        $collection = $this->_catColFactory->create()
              ->addPathsFilter($cat->getPath().'/') 
              ->addLevelFilter($cat->getLevel() + $levels)
              ->addFieldToFilter('is_active', true);
        return $collection;
    }
}
?>