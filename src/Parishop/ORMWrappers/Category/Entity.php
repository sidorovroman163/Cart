<?php

namespace Parishop\ORMWrappers\Category;

/**
 * Class Entity
 * @property int                                                                   id
 * @property int                                                                   rootId
 * @property int                                                                   articleId
 * @property string                                                                name
 * @property string                                                                alias
 * @property string                                                                shortDescription
 * @property string                                                                titleDescription
 * @property string                                                                description
 * @property int                                                                   depth
 * @property int                                                                   left
 * @property int                                                                   right
 * @property string                                                                created
 * @property string                                                                modified
 * @property int                                                                   publish
 * @property int                                                                   ordering
 * @property \PHPixie\ORM\Relationships\Type\OneTo\Type\Many\Property\Entity\Items $article
 * @method \Parishop\ORMWrappers\Article\Entity article()
 ***********************************************************************************************************************
 * @property string                                                                meta_title
 * @property string                                                                meta_description
 * @property string                                                                meta_keywords
 * @property string                                                                og_title
 * @property string                                                                og_description
 * @property string                                                                og_image
 ***********************************************************************************************************************
 * @property int                                                                   keywordGroupId
 * @property int                                                                   yandexRegionId
 * @property int                                                                   sexId
 * @property int                                                                   plural
 * @property int                                                                   commercial
 * @property int                                                                   season
 * @property string                                                                season_start
 * @property string                                                                season_end
 * @property \PHPixie\ORM\Relationships\Type\OneTo\Type\Many\Property\Entity\Owner $keywordGroup
 * @property \PHPixie\ORM\Relationships\Type\OneTo\Type\Many\Property\Entity\Owner $yandexRegion
 * @property \PHPixie\ORM\Relationships\Type\OneTo\Type\Many\Property\Entity\Owner $sex
 * @property \PHPixie\ORM\Relationships\Type\NestedSet\Property\Parent\Entity      parent
 * @property \PHPixie\ORM\Relationships\Type\NestedSet\Property\Children\Entity    children
 * @property \PHPixie\ORM\Relationships\Type\OneTo\Type\Many\Property\Entity\Owner $filter
 * @property \PHPixie\ORM\Relationships\Type\OneTo\Type\Many\Property\Entity\Items $filters
 * @method \Parishop\ORMWrappers\KeywordGroup\Entity keywordGroup()
 * @method \Parishop\ORMWrappers\YandexRegion\Entity yandexRegion()
 * @method \PHPixie\ORM\Loaders\Loader\Proxy\Editable|\Parishop\ORMWrappers\Category\Entity[] children()
 * @method \Parishop\ORMWrappers\Category\Entity parent()
 * @method \Parishop\ORMWrappers\Sex\Entity sex()
 * @method \Parishop\ORMWrappers\Filter\Entity filter()
 * @method \PHPixie\ORM\Loaders\Loader\Proxy\Editable|\Parishop\ORMWrappers\Filter\Entity[] filters()
 * @method \PHPixie\ORM\Loaders\Loader\Proxy\Editable|\Parishop\ORMWrappers\ProductType\Entity[] productTypes()
 ***********************************************************************************************************************
 * @package    ORMWrappers
 * @subpackage Entity
 */
class Entity extends \Parishop\ORMWrappers\Entity
{
    protected $nested;

    /**
     * @param                       $entity
     * @param \Parishop\App\Builder $builder
     */
    public function __construct($entity, $builder)
    {
        parent::__construct($entity, $builder);
        $this->nested = new \PHPixie\ORM\Extension\Nested($builder, $this);
    }

    public function brands()
    {
        /**
         * @var \Parishop\ORMWrappers\Brand\Entity $brand
         */
        $brands = $this->builder->components()->cache()->get('getBrandsCategoriesId' . $this->id());
        if(!$brands) {
            $brands       = array();
            $brands_array = $this->builder->components()->orm()->brand->ordering()->relatedTo(
                'products', $this->products()
            )->find()->asArray();
            foreach($brands_array as $brand) {
                $brands[] = array(
                    'id'   => $brand->id(),
                    'name' => $brand->name(),
                    'url'  => $brand->url(),
                );
            }
            $this->builder->components()->cache()->set('getBrandsCategoriesId' . $this->id(), $brands);
        }

        return $brands;
    }

    public function fullName()
    {
        return str_repeat(' - ', $this->depth) . ' ' . $this->name();
    }

    public function keywords()
    {
        return $this->builder->components()->orm()->keyword->where('model', $this->modelName())->where(
            'modelId', $this->id()
        )->find();
    }

    /**
     * @return \PHPixie\ORM\Extension\Nested
     */
    public function nested()
    {
        return $this->nested;
    }

    public function parent_categories()
    {
        $category   = $this;
        $categories = array();
        $key        = 0;
        while($category->nested()->parent()) {
            if($category->nested()->parent()->id() == 10000) {
                break;
            }
            $key++;
            $categories[$key] = $category->nested()->parent();
            $category         = $category->nested()->parent();
        }
        krsort($categories);

        return $categories;
    }

    public function prefix()
    {
        return str_repeat(' - ', $this->depth);
    }

    /**
     * Возвращает объект Query товаров принадлежащих всем дочерним категориям
     * @return \Parishop\ORMWrappers\Product\Query
     */
    public function products()
    {
        /**
         * Получаем объект Query товаров
         */
        $products = $this->builder->components()->orm()->product;
        /**
         * Фильтруем объект Query товаров по половому признаку текущей категории
         */
        $products->where('sexId', $this->sexId);
        $products->relatedTo('productImages');
        $products->relatedTo('restOptions');

        return $products;
    }

    public function resolverPath()
    {
        return 'app.category';
    }

    /**
     * @param \PHPixie\Slice\Type\ArrayData $data
     */
    public function saveData($data)
    {
        $this->setField('name', $data->getRequired('name'));
        $this->setField('alias', $data->get('alias'));
        $this->setField('shortDescription', htmlentities($data->get('shortDescription')));
        $this->setField('titleDescription', htmlentities($data->get('titleDescription')));
        $this->setField('description', htmlentities($data->get('description')));
        $this->setField('filterName', $data->get('filterName'));
        $this->setIsNull('yandexRegionId', $data->getRequired('yandexRegionId'), 225);
        $this->setIsNull('sexId', $data->getRequired('sexId'), 3003);
        $this->setIsNull('keywordGroupId', $data->getRequired('keywordGroupId'), 1);
        $this->setIsNull('plural', $data->getRequired('plural'), 0);
        $this->setSeason($data->get('season'), $data->get('season_start'), $data->get('season_end'));
        $this->setIsNull('commercial', $data->getRequired('commercial'), 0);
        $this->setIsNull('meta_title', htmlentities($data->getRequired('meta_title')));
        $this->setIsNull('meta_description', htmlentities($data->getRequired('meta_description')));
        $this->setIsNull('meta_keywords', htmlentities($data->getRequired('meta_keywords')));
        $this->setIsNull('og_title', htmlentities($data->getRequired('og_title')));
        $this->setIsNull('og_description', htmlentities($data->getRequired('og_description')));
        $this->setIsNull('og_image', htmlentities($data->getRequired('og_image')));
        $this->setIsNull('modified', date('Y-m-d H:i:s'));
        $this->save();
        $parentId = $data->getRequired('parentId');
        if($parentId) {
            $parent = $this->builder->components()->orm()->query('category')->in($parentId)->findOne();
            $this->parent->set($parent);
        } else {
            $this->parent->remove();
        }
    }

    /**
     * @param array  $url
     * @param array  $query
     * @param string $resolverPath
     * @return \PHPixie\HTTP\Messages\URI|\Psr\Http\Message\UriInterface
     */
    public function url($url = array(), $query = array(), $resolverPath = 'app.category')
    {
        return parent::url(
            array_merge(
                array(
                    'categoryAlias' => $this->alias,
                ), $url
            ), $query, $resolverPath
        );
    }
}
