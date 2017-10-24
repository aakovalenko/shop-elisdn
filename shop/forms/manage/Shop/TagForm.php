<?php

namespace shop\forms\manage\Shop;


use yii\base\Model;
use shop\entities\Shop\Tag;

class TagForm extends Model
{
    public $name;
    public $slug;

    private $_tag;

    public function __construct(Tag $tag = null, $config = [])
    {
        if ($tag)
        {
            $this->name = $tag->name;
            $this->slug = $tag->slug;
            $this->_tag = $tag;
        }
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name', 'slug'], 'string', 'max'=> 255],
            ['slug', 'match', 'pattern' => '#^[a-z0-9_-]*$#'],
            [['name', 'slug'], 'unique', 'targetClass' => Tag::class]
        ];
    }

}