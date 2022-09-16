<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // $this->faker->phoneNumber このまま入れるとハイフンがランダムな箇所に入ってダミーデータが生成されるので事前に置換した値を準備しておく
        $tel = str_replace('-', '', $this->faker->phoneNumber);
        // $this->faker->address ← これで作ると郵便番号+スペース+住所で作成されてしまうので先頭から9文字切り捨てて、変数に代入
        $address = mb_substr($this->faker->address, 9);
        return [
            'name' => $this->faker->name,
            'kana' => $this->faker->KanaName,
            'tel' => $tel,
            'email' => $this->faker->email,
            'postcode' => $this->faker->postcode,
            'address' => $address,
            'birthday' => $this->faker->dateTime,
            'gender' => $this->faker->numberBetween(0, 2),
            'memo' => $this->faker->realText(50),
        ];
    }
}
