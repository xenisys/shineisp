<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version121 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->removeColumn('orders_items', 'callback_url');
    }

    public function down()
    {
        $this->addColumn('orders_items', 'callback_url', 'string', '200', array(
             'notnull' => '',
             ));
    }
}