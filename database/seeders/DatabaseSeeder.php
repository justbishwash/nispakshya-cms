<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'admin@nispakshya.com'],
            [
                'name'       => 'Super Admin',
                'password'   => Hash::make('Admin@1234'),
                'role'       => 'super_admin',
                'is_active'  => true,
            ]
        );

        // Default site settings
        $defaults = [
            ['key' => 'site_name',       'value' => 'Nispakshya',          'group' => 'general'],
            ['key' => 'site_tagline',    'value' => 'निष्पक्ष समाचार',     'group' => 'general'],
            ['key' => 'contact_email',   'value' => 'info@nispakshya.com', 'group' => 'general'],
            ['key' => 'robots_txt',      'value' => "User-agent: *\nAllow: /\nSitemap: /sitemap.xml", 'group' => 'seo'],
        ];

        foreach ($defaults as $s) {
            Setting::updateOrCreate(['key' => $s['key']], ['value' => $s['value'], 'group' => $s['group']]);
        }
    }
}
