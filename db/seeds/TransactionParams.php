<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class TransactionParams extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $data = [
            [
                'id_transaction_param_pk' => 1,
                'description'             => 'plot',
                'category'                => 'GENDER',
                'created_at'              => date('Y-m-d H:i:s'),
                'updated_at'              => date('Y-m-d H:i:s'),
            ],
            [
                'id_transaction_param_pk' => 2,
                'description'             => 'food',
                'category'                => 'GENDER',
                'created_at'              => date('Y-m-d H:i:s'),
                'updated_at'              => date('Y-m-d H:i:s'),
            ],
            [
                'id_transaction_param_pk' => 3,
                'description'             => 'payment',
                'category'                => 'GENDER',
                'created_at'              => date('Y-m-d H:i:s'),
                'updated_at'              => date('Y-m-d H:i:s'),
            ],
            [
                'id_transaction_param_pk' => 4,
                'description'             => 'entrie',
                'category'                => 'TYPE',
                'created_at'              => date('Y-m-d H:i:s'),
                'updated_at'              => date('Y-m-d H:i:s'),
            ],
            [
                'id_transaction_param_pk' => 5,
                'description'             => 'exit',
                'category'                => 'TYPE',
                'created_at'              => date('Y-m-d H:i:s'),
                'updated_at'              => date('Y-m-d H:i:s'),
            ],
        ];

        $this->table('transaction_params')->insert($data)->saveData();
    }
}
