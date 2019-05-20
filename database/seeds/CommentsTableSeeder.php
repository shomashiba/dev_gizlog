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
                'questions_id' => '1',
                'comment' => 'コメント001',
            ],
            [
                'user_id' => 2,
                'questions_id' => '2',
                'comment' => 'コメント002',
            ],
            [
                'user_id' => 1,
                'questions_id' => '3',
                'comment' => 'コメント003',
            ],
            [
                'user_id' => 1,
                'questions_id' => '4',
                'comment' => 'コメント004',
            ],
        ]);
    }
}
