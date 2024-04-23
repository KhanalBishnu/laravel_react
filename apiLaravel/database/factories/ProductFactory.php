<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first(); 

        return [
            'title'=>fake()->sentence(),
            'description'=>fake()->paragraph(),
            'user_id'=>$user->id,
            'tags'=>$this->generateRandomTags()
        ];
    }
    private function generateRandomTags()
    {
        $tags = ['Tag1', 'Tag2', 'Tag3', 'Tag4', 'Tag5']; // Define your tags here
        shuffle($tags);
        return implode(',', array_slice($tags, 0, rand(1, 3))); // Get a random subset of tags
    }
}
