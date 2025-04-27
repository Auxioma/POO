<?php

use Phinx\Migration\AbstractMigration;

class AddCategoryTable extends AbstractMigration
{
    public function change()
    {
        $this->table('categories', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'integer', ['identity' => true])
            ->addColumn('name', 'string')
            ->addColumn('slug', 'string')
            ->addIndex('slug', ['unique' => true])
            ->create();
    }
}
