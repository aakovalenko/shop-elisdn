<?php

use yii\db\Migration;

class m171013_114746_user_networks_table extends Migration
{
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('{{%user_networks}}', [
           'id' => $this->primaryKey(),
           'user_id' => $this->integer()->notNull(),
           'identity' => $this->string()->notNull(),
           'network' => $this->string(16)->notNull(),
        ], $tableOptions);

        $this->createIndex('{{%idx-user_networks-identity-name}}',
            '{{%user_networks}}',
            ['identity', 'network'], true);
        $this->createIndex('{{%idx-user_networks-user_id}}',
            '{{%user_networks}}', 'user_id');
        $this->addForeignKey('{{%fk-user_networks-user_id}}',
            '{{%user_networks}}',
            'user_id', '{{%users}}', 'id',
            'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%user_networks}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171013_114746_user_networks_table cannot be reverted.\n";

        return false;
    }
    */
}
