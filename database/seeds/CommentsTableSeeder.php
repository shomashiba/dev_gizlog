<?php

use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('comments')->truncate();
        DB::table('comments')->insert([
            [
                'user_id' => 2,
                'question_id' => '1',
                'comment' => 'コメント001',
                'created_at' => '2019-05-21 06:00:00',
            ],
            [
                'user_id' => 2,
                'question_id' => '2',
                'comment' => 'コメント002',
                'created_at' => '2019-05-21 07:00:00',
            ],
            [
                'user_id' => 1,
                'question_id' => '3',
                'comment' => 'コメント003',
                'created_at' => '2019-05-21 08:00:00',
            ],
            [
                'user_id' => 1,
                'question_id' => '4',
                'comment' => 'コメント004',
                'created_at' => '2019-05-21 09:00:00',
            ],
        ]);
    }
}
