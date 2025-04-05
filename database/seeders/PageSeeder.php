<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'name' => 'About Us',
                'slug' => 'about-us',
                'content' => '<p><strong>ServeEase</strong> is your trusted platform for connecting service providers with customers. We simplify the process of finding and booking professional services.</p>
<h3>Our Mission</h3>
<p>To provide a seamless experience for both service providers and customers, making service booking easy and efficient.</p>
<h3>Our Vision</h3>
<p>To become the leading service marketplace platform, empowering providers and delivering satisfaction to customers.</p>'
            ],
            [
                'name' => 'Contact Us',
                'slug' => 'contact-us',
                'content' => '<p><strong>Get in touch with us!</strong> We\'re here to help with any questions or concerns.</p>
<p><strong>Email:</strong> support@serveease.com</p>
<p><strong>Phone:</strong> (555) 123-4567</p>
<p><strong>Address:</strong> 123 Business Street, Suite 100, City, State 12345</p>'
            ]
        ];


        foreach ($pages as $page) {
            Page::create($page);
        }
    }
}
