<?php

namespace Tests\Feature;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed('FoldersTableSeeder');
    }

    /**
     * 期限日が日付でない場合バリデーションエラー
     * @test
     */

    public function due_date_should_be_date()
    {
        $response = $this->post('/folders/1/tasks/create', [
            'title' => 'Sample task',
            'due_date' => 123,
        ]);

        $response->assertSessionHasErrors([
            'due_date' => '期限日 には日付を入力してください。'
        ]);
    }

    /**
     * 期限日が過去の日付の場合バリデーションエラー
     * @test
     */

    public function due_date_should_bot_be_past()
    {
        $response = $this->post('/folders/1/tasks/create', [
            'title' => 'Sample task',
            'due_date' => Carbon::yesterday()->format('Y/m/d'),
        ]);

        $response->assertSessionHasErrors([
            'due_date' => '期限日 には今日以降の日付を入力してください。'
        ]);
    }

    /**
     * 状態が定義された値出ない場合はバリデーションエラー
     * @test
     */

    public function status_should_be_within_definde_numbers()
    {
        $this->seed('TasksTableSeeder');

        $response = $this->post('/folders/1/tasks/1/edit', [
            'title' => 'Sample task',
            'due_date' => Carbon::today()->format('Y/m/d'),
            'status' => 999,
        ]);

        $response->assertSessionHasErrors([
            'status' => '状態 には 未着手、着手中、完了 のいずれかを指定してください。',
        ]);
    }

    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
