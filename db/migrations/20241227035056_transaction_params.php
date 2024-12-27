<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class TransactionParams extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('transaction_params', ['id' => false, 'primary_key' => ['id_transaction_param_pk']]);
        $table->addColumn('id_transaction_param_pk', 'biginteger', ['identity' => true])
            ->addColumn('description', 'string', ['null' => false])
            ->addColumn('category', 'enum', [
                'values' => ['TYPE', 'GENDER'],
                'null' => false,
            ])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Drop the table
        $this->table('transaction_params')->drop()->save();
    }
}
