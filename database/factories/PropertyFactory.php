<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // ربط العقار بمالك معتمد وموجود في السيستم باستخدام العمود الصحيح user_id
            'user_id' => User::factory()->create([
                'role' => 'owner',
                'status' => 'approved'
            ])->id,

            'governorate'        => $this->faker->randomElement(['Assiut', 'Cairo', 'Giza']),
            'location'           => $this->faker->address(),
            'proximity'          => $this->faker->randomElement(['5min', '10min', '15min']),
            'bedrooms'           => $this->faker->numberBetween(1, 4),
            'bathrooms'          => $this->faker->numberBetween(1, 2),
            'floor'              => $this->faker->numberBetween(0, 10),
            'price'              => $this->faker->numberBetween(1500, 5000),
            'gender_type'        => $this->faker->randomElement(['male', 'female']),
            'is_furnished'       => $this->faker->boolean(),
            'utilities_included' => $this->faker->boolean(),
            'available_from'     => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'images'             => ['property_fake_image1.jpg', 'property_fake_image2.jpg'], // مصفوفة لأن الـ cast عندك array
            'description'        => $this->faker->paragraph(),
            'status'             => 'approved', // الحالة الافتراضية للـ test وممكن الـ tests تغيرها براحتها
            'rented_at'          => null,
        ];
    }
}
