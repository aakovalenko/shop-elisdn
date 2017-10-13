<?php
/**
 * Created by PhpStorm.
 * User: andri
 * Date: 13.10.17
 * Time: 14:39
 */

namespace shop\entities;


use Webmozart\Assert\Assert;
use yii\db\ActiveRecord;

class Network extends ActiveRecord
{
    public static function create($network, $identity): self
    {
        Assert::notEmpty($network);
        Assert::notEmpty($identity);

        $item = new static();
        $item->network = $network;
        $item->identity = $identity;

        return $item;
    }

    public static function tableName()
    {
        return '{{%user_networks}}';
    }
}