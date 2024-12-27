<?php

declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class Authentication extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('authentication', ['id' => false, 'primary_key' => ['id_session']]);
        $table->addColumn('id_session', 'biginteger', ['identity' => true])
            ->addColumn('id_user_fk', 'biginteger', ['null' => false])
            ->addColumn('status', 'smallinteger', ['null' => false])
            ->addColumn('login_date', 'datetime')
            ->addColumn('access_token', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->addForeignKey('id_user_fk', 'users', 'id_user_pk', [
                'delete' => 'NO_ACTION',
                'update' => 'NO_ACTION'
            ])
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Drop the table
        $this->table('authentication')->drop()->save();
    }
}
