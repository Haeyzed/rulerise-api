<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<h1>About Our Job Portal</h1><p>Welcome to our job portal, where we connect talented professionals with outstanding career opportunities. Our mission is to simplify the job search process and help companies find the right talent to grow their businesses.</p><p>Founded in 2023, our platform has quickly become a leading destination for job seekers and employers alike. We leverage cutting-edge technology and industry expertise to create meaningful connections in the job market.</p><p>Our team consists of experienced professionals from HR, technology, and recruitment backgrounds who understand the challenges of modern hiring.</p>',
                'excerpt' => 'Learn about our mission, vision, and the team behind our job portal platform.',
                'meta_title' => 'About Us | Job Portal',
                'meta_description' => 'Learn about our job portal mission, vision, and the team behind our platform.',
                'meta_keywords' => 'about us, job portal, career platform, recruitment site, employment website',
                'featured_image' => '/images/pages/about-us.jpg',
                'is_active' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h1>Privacy Policy</h1><p>Last updated: January 1, 2023</p><p>This Privacy Policy describes how we collect, use, and disclose your personal information when you use our job portal.</p><h2>Information We Collect</h2><p>We collect information that you provide directly to us, such as when you create an account, update your profile, apply for jobs, or communicate with us.</p><h2>How We Use Your Information</h2><p>We use your information to provide, maintain, and improve our services, process job applications, communicate with you, and comply with legal obligations.</p><h2>Information Sharing</h2><p>We may share your information with employers when you apply for jobs, service providers who perform services on our behalf, and as required by law.</p><h2>Your Rights</h2><p>Depending on your location, you may have certain rights regarding your personal information, including the right to access, correct, or delete your data.</p>',
                'excerpt' => 'Our privacy policy outlines how we collect, use, and protect your personal information.',
                'meta_title' => 'Privacy Policy | Job Portal',
                'meta_description' => 'Our privacy policy outlines how we collect, use, and protect your personal information.',
                'meta_keywords' => 'privacy policy, data protection, personal information, privacy rights, GDPR',
                'featured_image' => '/images/pages/privacy-policy.jpg',
                'is_active' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'content' => '<h1>Terms of Service</h1><p>Last updated: January 1, 2023</p><p>These Terms of Service govern your use of our job portal. By accessing or using our services, you agree to be bound by these terms.</p><h2>Account Registration</h2><p>You must provide accurate information when creating an account and keep your credentials secure. You are responsible for all activity under your account.</p><h2>User Conduct</h2><p>You agree not to misuse our services, post false information, or engage in any activity that violates applicable laws or regulations.</p><h2>Intellectual Property</h2><p>Our platform and its contents are protected by copyright, trademark, and other laws. You may not use our intellectual property without our prior written consent.</p><h2>Limitation of Liability</h2><p>To the maximum extent permitted by law, we shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of our services.</p>',
                'excerpt' => 'Read our terms of service to understand the rules and guidelines for using our job portal.',
                'meta_title' => 'Terms of Service | Job Portal',
                'meta_description' => 'Read our terms of service to understand the rules and guidelines for using our job portal.',
                'meta_keywords' => 'terms of service, user agreement, legal terms, conditions of use, service terms',
                'featured_image' => '/images/pages/terms-of-service.jpg',
                'is_active' => true,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'content' => '<h1>Contact Us</h1><p>We\'re here to help! If you have any questions, feedback, or concerns, please don\'t hesitate to reach out to us.</p><h2>Customer Support</h2><p>Email: support@jobportal.com<br>Phone: (555) 123-4567<br>Hours: Monday-Friday, 9am-5pm EST</p><h2>Business Inquiries</h2><p>For partnership opportunities or business-related questions:<br>Email: business@jobportal.com</p><h2>Office Location</h2><p>123 Employment Avenue<br>Career City, JB 12345<br>United States</p><h2>Send Us a Message</h2><p>Use the contact form below to send us a message, and we\'ll get back to you as soon as possible.</p>',
                'excerpt' => 'Get in touch with our team for support, feedback, or business inquiries.',
                'meta_title' => 'Contact Us | Job Portal',
                'meta_description' => 'Get in touch with our team for support, feedback, or business inquiries.',
                'meta_keywords' => 'contact us, support, help, feedback, customer service, get in touch',
                'featured_image' => '/images/pages/contact-us.jpg',
                'is_active' => true,
                'order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'FAQ',
                'slug' => 'faq',
                'content' => '<h1>Frequently Asked Questions</h1><p>Find answers to commonly asked questions about our job portal.</p><h2>For Job Seekers</h2><h3>How do I create an account?</h3><p>Click on the "Sign Up" button, select "Job Seeker," and follow the registration process.</p><h3>Is it free to apply for jobs?</h3><p>Yes, applying for jobs on our platform is completely free for all job seekers.</p><h3>How can I make my profile stand out?</h3><p>Complete all sections of your profile, upload a professional photo, add your skills, and keep your experience up-to-date.</p><h2>For Employers</h2><h3>How do I post a job?</h3><p>After creating an employer account, click on "Post a Job" and fill out the job details form.</p><h3>What subscription plans do you offer?</h3><p>We offer various plans ranging from free basic listings to premium packages with advanced features.</p><h3>Can I search for candidates directly?</h3><p>Yes, with our premium plans, you can search our candidate database using various filters.</p>',
                'excerpt' => 'Find answers to common questions about using our job portal for both job seekers and employers.',
                'meta_title' => 'Frequently Asked Questions | Job Portal',
                'meta_description' => 'Find answers to common questions about using our job portal for both job seekers and employers.',
                'meta_keywords' => 'FAQ, frequently asked questions, help, support, job seeker help, employer help',
                'featured_image' => '/images/pages/faq.jpg',
                'is_active' => true,
                'order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($pages as $page) {
            Page::create($page);
        }
    }
}

