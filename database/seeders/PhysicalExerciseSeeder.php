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

        $physicalExercises = [
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
            [
                'name' => 'Стойка на гвоздях'
            ],
            [
                'name' => 'Повороты тела'
            ],
            [
                'name' => 'Отжимание с колен'
            ],
            [
                'name' => 'Приседания с весом 2.5кг'
            ],
            [
                'name' => 'Отжимание, ноги на стуле'
            ],
            [
                'name' => 'Подъем таза с весом 2.5кг'
            ],
            [
                'name' => 'Сурья намаскара'
            ],
            [
                'name' => 'Приседания с весом 5кг'
            ],
            [
                'name' => 'Подъем таза с весом 5кг'
            ],
            [
                'name' => 'Становая тяга гантели 29.5кг'
            ],
        ];


        $testNamesArr = [];
        for ($i = 1; $i <= 200; $i++) {
            $testNamesArr[] = ['name' => 'test_' . $i];
        }

        foreach ($physicalExercises as &$physicalExercise) {
            $physicalExercise['is_active'] = 1;
        }

        PhysicalExercise::insert($testNamesArr);
        PhysicalExercise::insert($physicalExercises);

        $user = User::find(1);
        $user->physicalExercises()->sync(range(1,223));
    }
}
