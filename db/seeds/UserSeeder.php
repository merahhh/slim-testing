<?php

use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [];
        for ($i = 0; $i < 20; $i++) {
            $data[] = [
                'first_name'    => $faker->firstName,
                'last_name'     => $faker->lastName,
                'email'         => $faker->email,
                'password'      => $faker->password,
                'hash'          => password_hash($faker->password, PASSWORD_BCRYPT),
                'active'        => 1, //sha1($faker->password),
            ];
        }

        $posts = $this->table('SlimNew');
        $posts->insert($data)
            ->save();
    }
}
