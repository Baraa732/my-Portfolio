<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SkillEcosystem;
use App\Models\EcosystemSection;

class CVController extends Controller
{
    public function download()
    {
        // Get skills data
        $javascriptSkills = SkillEcosystem::where('ecosystem', 'javascript')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
            
        $phpSkills = SkillEcosystem::where('ecosystem', 'php')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $data = [
            'name' => 'Baraa Al-Rifaee',
            'title' => 'Full Stack Web Developer',
            'email' => 'baraaalrifaee732@gmail.com',
            'phone' => '+963 XXX XXX XXX',
            'location' => 'Syria',
            'website' => 'https://baraa-portfolio.com',
            'summary' => 'Passionate Full Stack Developer with expertise in modern web technologies. I create digital experiences that combine modern design with cutting-edge technology, transforming ideas into scalable, user-friendly solutions.',
            'javascriptSkills' => $javascriptSkills,
            'phpSkills' => $phpSkills,
            'experience' => [
                [
                    'title' => 'Senior Full Stack Developer',
                    'company' => 'Freelance',
                    'period' => '2023 - Present',
                    'description' => '• Developed 15+ modern web applications using Laravel, React, and Vue.js\n• Implemented advanced Three.js animations and interactive user interfaces\n• Built comprehensive admin dashboards with real-time analytics\n• Optimized application performance resulting in 40% faster load times'
                ],
                [
                    'title' => 'Full Stack Developer',
                    'company' => 'Various Projects',
                    'period' => '2022 - 2023',
                    'description' => '• Created responsive e-commerce platforms with payment integration\n• Developed portfolio websites with advanced animations and features\n• Implemented security measures including XSS and CSRF protection\n• Collaborated with clients to deliver projects on time and within budget'
                ],
                [
                    'title' => 'Web Developer',
                    'company' => 'Freelance Projects',
                    'period' => '2021 - 2022',
                    'description' => '• Built 10+ websites using HTML, CSS, JavaScript, and PHP\n• Focused on user experience and performance optimization\n• Learned modern frameworks and development best practices\n• Established foundation in full-stack development'
                ]
            ],
            'projects' => [
                [
                    'name' => 'Portfolio Website',
                    'description' => 'Modern portfolio with Three.js animations, admin dashboard, and advanced features',
                    'technologies' => 'Laravel, Three.js, JavaScript, CSS3'
                ],
                [
                    'name' => 'E-commerce Platform',
                    'description' => 'Full-featured online store with payment integration and inventory management',
                    'technologies' => 'Laravel, Vue.js, MySQL, Stripe API'
                ]
            ]
        ];

        $pdf = Pdf::loadView('cv.template', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Baraa_Al-Rifaee_CV_' . date('Y_m_d_H_i_s') . '.pdf';
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => 'Thu, 01 Jan 1970 00:00:00 GMT',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}