<?php

namespace shop\entities\User;

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

    public function isFor($network, $identity): bool
    {
        return $this->network === $network && $this->identity === $identity;
    }

    public static function tableName()
    {
        return '{{%user_networks}}';
    }
}