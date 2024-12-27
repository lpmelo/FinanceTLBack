<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Users extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('users', ['id' => false, 'primary_key' => ['id_user_pk']]);
        $table->addColumn('id_user_pk', 'biginteger', ['identity' => true])
            ->addColumn('username', 'string', ['null' => false])
            ->addColumn('password', 'string', ['null' => false])
            ->addColumn('name', 'string')
            ->addColumn('nickname', 'string', ['null' => false])
            ->addColumn('mail', 'string')
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->addIndex(['username', 'mail'],['unique'=> true])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Drop the table
        $this->table('users')->drop()->save();
    }
}
