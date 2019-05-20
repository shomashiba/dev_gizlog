<?php

use Illuminate\Database\Seeder;

class QuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('questions')->truncate();
        DB::table('questions')->insert([
            [
                'user_id' => 1,
                'tag_category_id' => 1,
                'title' => 'フロントの質問',
                'content' => 'フロントのコンテンツ',
            ],
            [
                'user_id' => 1,
                'tag_category_id' => 2,
                'title' => 'バックの質問',
                'content' => 'バックのコンテンツ',
            ],
            [
                'user_id' => 2,
                'tag_category_id' => 3,
                'title' => 'インフラの質問',
                'content' => 'インフラのコンテンツ',
            ],
            [
                'user_id' => 2,
                'tag_category_id' => 4,
                'title' => 'こんにちは',
                'content' => 'シード中です。',
            ]
        ]);
    }
}
