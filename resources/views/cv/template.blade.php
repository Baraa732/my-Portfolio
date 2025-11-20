<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $name }} - CV</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
            margin: 0.5in;
        }
        
        .header {
            text-align: left;
            margin-bottom: 20px;
        }
        
        .name {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .title {
            font-size: 12pt;
            margin-bottom: 10px;
        }
        
        .contact-info {
            font-size: 10pt;
            margin-bottom: 15px;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }
        
        .job-title {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .company {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .period {
            margin-bottom: 5px;
        }
        
        .description {
            margin-bottom: 15px;
            white-space: pre-line;
        }
        
        .skills-list {
            margin-bottom: 10px;
        }
        
        .skill-category {
            margin-bottom: 10px;
        }
        
        .skill-category-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <!-- Contact Information -->
    <div class="header">
        <div class="name">{{ $name }}</div>
        <div class="title">{{ $title }}</div>
        <div class="contact-info">
            Email: {{ $email }} | Phone: {{ $phone }} | Location: {{ $location }}<br>
            Website: {{ $website }}
        </div>
    </div>

    <!-- Professional Summary -->
    <div class="section">
        <div class="section-title">Professional Summary</div>
        <div>{{ $summary }}</div>
    </div>

    <!-- Work Experience -->
    <div class="section">
        <div class="section-title">Work Experience</div>
        @foreach($experience as $job)
        <div>
            <div class="job-title">{{ $job['title'] }}</div>
            <div class="company">{{ $job['company'] }}</div>
            <div class="period">{{ $job['period'] }}</div>
            <div class="description">{{ $job['description'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- Technical Skills -->
    <div class="section">
        <div class="section-title">Technical Skills</div>
        <div class="skill-category">
            <div class="skill-category-title">JavaScript Technologies:</div>
            <div>@foreach($javascriptSkills as $skill){{ $skill->name }}@if(!$loop->last), @endif @endforeach</div>
        </div>
        <div class="skill-category">
            <div class="skill-category-title">PHP Technologies:</div>
            <div>@foreach($phpSkills as $skill){{ $skill->name }}@if(!$loop->last), @endif @endforeach</div>
        </div>
    </div>

    <!-- Key Projects -->
    <div class="section">
        <div class="section-title">Key Projects</div>
        @foreach($projects as $project)
        <div>
            <div class="job-title">{{ $project['name'] }}</div>
            <div class="company">Technologies: {{ $project['technologies'] }}</div>
            <div class="description">{{ $project['description'] }}</div>
        </div>
        @endforeach
    </div>
</body>
</html>