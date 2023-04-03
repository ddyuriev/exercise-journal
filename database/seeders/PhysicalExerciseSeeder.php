<?php

namespace Database\Seeders;

use App\Models\PhysicalExercise;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhysicalExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PhysicalExercise::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        PhysicalExercise::insert([
                [
                    'name' => 'Отжимание (классическое)'
                ],
                [
                    'name' => 'Скручивание (классическое)'
                ],
                [
                    'name' => 'Приседание (классическое)'
                ],
                [
                    'name' => 'Кошка'
                ],
                [
                    'name' => 'Подъем таза'
                ],
                [
                    'name' => 'Планка'
                ],
                [
                    'name' => 'Подтягивание'
                ],
                [
                    'name' => 'Берпи'
                ],
                [
                    'name' => 'Пловец'
                ],
                [
                    'name' => 'Упражнения кегеля'
                ],
                [
                    'name' => 'Круги руками'
                ],
                [
                    'name' => 'Наклоны с руками за головой'
                ],
                [
                    'name' => 'Подъём рук'
                ],
                [
                    'name' => 'Глубокий наклон в сторону'
                ],
                [
                    'name' => 'Скручивание'
                ],
                [
                    'name' => 'Приседание на носочках'
                ],
                [
                    'name' => 'Вращение плечами'
                ],
                [
                    'name' => 'Разворот рук'
                ],
                [
                    'name' => 'Наклоны головы'
                ],
                [
                    'name' => 'Мельница'
                ],
                [
                    'name' => 'Наклоны корпуса'
                ],
                [
                    'name' => 'Крылья'
                ],

                [
                    'name' => 'Массаж глаз'
                ],
            ]
        );


        $testNamesArr = [];
        for ($i = 1; $i <= 200; $i++) {
            $testNamesArr[] = ['name' => 'test_' . $i];
        }
        PhysicalExercise::insert($testNamesArr);

        $user = User::find(1);

        $user->physicalExercises()->sync(range(1,223));
    }
}
