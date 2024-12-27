<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Transactions extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('transactions', [
            'id' => false,
            'primary_key' => ['id_transaction_pk']
        ]);

        $table->addColumn('id_transaction_pk', 'integer', [
            'identity' => true
        ])
            ->addColumn('id_user_fk', 'biginteger', [
                'null' => false
            ])
            ->addColumn('id_type_fk', 'biginteger', [
                'null' => false
            ])
            ->addColumn('id_gender_fk', 'biginteger', [
                'null' => false
            ])
            ->addColumn('description', 'string', [
                'limit' => 255,
                'null' => false
            ])
            ->addColumn('value', 'float', [
                'precision' => 255,
                'scale' => 2,
                'null' => false
            ])
            ->addColumn('date', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false
            ])
            ->addColumn('recurrence', 'enum', [
                'values' => ['Y', 'N'],
                'default' => 'N',
                'null' => true
            ])
            ->addColumn('recurrence_id', 'char', [
                'limit' => 36,
                'null' => true
            ])
            ->addColumn('plot_total_value', 'float', [
                'precision' => 255,
                'scale' => 2,
                'null' => true
            ])
            ->addColumn('plot_total', 'integer', [
                'null' => true
            ])
            ->addColumn('plot_number', 'integer', [
                'null' => true
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false
            ])
            ->addColumn('updated_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP',
                'null' => false
            ])
            ->addForeignKey('id_user_fk', 'users', 'id_user_pk', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE'
            ])
            ->addForeignKey('id_type_fk', 'transaction_params', 'id_transaction_param_pk', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE'
            ])
            ->addForeignKey('id_gender_fk', 'transaction_params', 'id_transaction_param_pk', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE'
            ])
            ->create();
    }

    public function down()
    {
        $this->table('transactions')->drop()->save();
    }
}
