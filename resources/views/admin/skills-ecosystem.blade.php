<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills Ecosystem - Admin Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="skills-ecosystem">
        <!-- Header -->
        <div class="ecosystem-header">
            <div class="header-content">
                <div class="header-left">
                    <a href="{{ route('admin.dashboard') }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="header-info">
                        <h1>Skills Ecosystem</h1>
                        <p>Manage your technology stack and expertise</p>
                    </div>
                </div>
                <div class="header-stats">
                    <div class="stat-card">
                        <div class="stat-icon js-icon">
                            <i class="fab fa-js-square"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">{{ $javascriptSkills->count() }}</span>
                            <span class="stat-label">JS Skills</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon php-icon">
                            <i class="fab fa-php"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">{{ $phpSkills->count() }}</span>
                            <span class="stat-label">PHP Skills</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="ecosystem-content">
            <!-- JavaScript Ecosystem -->
            <div class="ecosystem-panel js-panel">
                <div class="panel-header">
                    <div class="panel-title">
                        <div class="ecosystem-icon js-gradient">
                            <i class="fab fa-js-square"></i>
                        </div>
                        <div class="title-content">
                            <h2>{{ $javascriptSection->title ?? 'JavaScript Ecosystem' }}</h2>
                            <p>{{ $javascriptSection->description ?? 'Frontend frameworks and libraries' }}</p>
                        </div>
                    </div>
                    <div class="panel-controls">
                        <div class="visibility-toggle">
                            <input type="checkbox" id="js-visibility" {{ $javascriptSection && $javascriptSection->is_visible ? 'checked' : '' }}>
                            <label for="js-visibility" class="toggle-label">
                                <span class="toggle-slider"></span>
                                <span class="toggle-text">Portfolio Visible</span>
                            </label>
                        </div>
                        <button class="control-btn edit-section" onclick="openSectionModal('javascript')">
                            <i class="fas fa-cog"></i>
                        </button>
                        <button class="control-btn add-skill" onclick="openSkillModal('javascript')">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="panel-body">
                    @if($javascriptSkills->count() > 0)
                        <div class="skills-grid" id="js-skills">
                            @foreach($javascriptSkills as $skill)
                                <div class="skill-card {{ $skill->is_active ? 'active' : 'inactive' }}"
                                    data-id="{{ $skill->id }}">
                                    <div class="skill-header">
                                        <div class="skill-icon">
                                            <i class="{{ $skill->icon ?: 'fab fa-js' }}"></i>
                                        </div>
                                        <div class="skill-status">
                                            <span class="status-dot {{ $skill->is_active ? 'active' : 'inactive' }}"></span>
                                        </div>
                                    </div>
                                    <div class="skill-body">
                                        <h4 class="skill-name">{{ $skill->name }}</h4>
                                        <div class="skill-progress">
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: {{ $skill->proficiency }}%"></div>
                                            </div>
                                            <span class="progress-text">{{ $skill->proficiency }}%</span>
                                        </div>
                                    </div>
                                    <div class="skill-actions">
                                        <button class="action-btn toggle" onclick="toggleSkill({{ $skill->id }})"
                                            title="{{ $skill->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $skill->is_active ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                        <button class="action-btn edit"
                                            onclick="editSkill({{ $skill->id }}, '{{ $skill->name }}', '{{ $skill->icon }}', {{ $skill->proficiency }})"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteSkill({{ $skill->id }})"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fab fa-js-square"></i>
                            </div>
                            <h3>No JavaScript Skills</h3>
                            <p>Start building your JavaScript ecosystem</p>
                            <button class="btn-primary alternative shine" onclick="openSkillModal('javascript')">
                                <i class="fas fa-plus"></i>
                                Add First Skill
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- PHP Ecosystem -->
            <div class="ecosystem-panel php-panel">
                <div class="panel-header">
                    <div class="panel-title">
                        <div class="ecosystem-icon php-gradient">
                            <i class="fab fa-php"></i>
                        </div>
                        <div class="title-content">
                            <h2>{{ $phpSection->title ?? 'PHP Ecosystem' }}</h2>
                            <p>{{ $phpSection->description ?? 'Backend frameworks and tools' }}</p>
                        </div>
                    </div>
                    <div class="panel-controls">
                        <div class="visibility-toggle">
                            <input type="checkbox" id="php-visibility" {{ $phpSection && $phpSection->is_visible ? 'checked' : '' }}>
                            <label for="php-visibility" class="toggle-label">
                                <span class="toggle-slider"></span>
                                <span class="toggle-text">Portfolio Visible</span>
                            </label>
                        </div>
                        <button class="control-btn edit-section" onclick="openSectionModal('php')">
                            <i class="fas fa-cog"></i>
                        </button>
                        <button class="control-btn add-skill" onclick="openSkillModal('php')">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="panel-body">
                    @if($phpSkills->count() > 0)
                        <div class="skills-grid" id="php-skills">
                            @foreach($phpSkills as $skill)
                                <div class="skill-card {{ $skill->is_active ? 'active' : 'inactive' }}"
                                    data-id="{{ $skill->id }}">
                                    <div class="skill-header">
                                        <div class="skill-icon">
                                            <i class="{{ $skill->icon ?: 'fab fa-php' }}"></i>
                                        </div>
                                        <div class="skill-status">
                                            <span class="status-dot {{ $skill->is_active ? 'active' : 'inactive' }}"></span>
                                        </div>
                                    </div>
                                    <div class="skill-body">
                                        <h4 class="skill-name">{{ $skill->name }}</h4>
                                        <div class="skill-progress">
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: {{ $skill->proficiency }}%"></div>
                                            </div>
                                            <span class="progress-text">{{ $skill->proficiency }}%</span>
                                        </div>
                                    </div>
                                    <div class="skill-actions">
                                        <button class="action-btn toggle" onclick="toggleSkill({{ $skill->id }})"
                                            title="{{ $skill->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $skill->is_active ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                        <button class="action-btn edit"
                                            onclick="editSkill({{ $skill->id }}, '{{ $skill->name }}', '{{ $skill->icon }}', {{ $skill->proficiency }})"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteSkill({{ $skill->id }})"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fab fa-php"></i>
                            </div>
                            <h3>No PHP Skills</h3>
                            <p>Start building your PHP ecosystem</p>
                            <button class="btn-primary alternative shine" onclick="openSkillModal('php')">
                                <i class="fas fa-plus"></i>
                                Add First Skill
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Skill Modal -->
    <div id="skillModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="skillModalTitle">Add Skill</h3>
                <button class="close-btn" onclick="closeSkillModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="skillForm" class="modal-form">
                <input type="hidden" id="skillId">
                <input type="hidden" id="skillEcosystem">

                <div class="form-group">
                    <label>Skill Name</label>
                    <input type="text" id="skillName" placeholder="e.g., React, Laravel" required>
                </div>

                <div class="form-group">
                    <label>Icon Class</label>
                    <input type="text" id="skillIcon" placeholder="e.g., fab fa-react">
                    <small>Use FontAwesome icon classes</small>
                </div>

                <div class="form-group">
                    <label>Proficiency Level</label>
                    <div class="range-input">
                        <input type="range" id="skillProficiency" min="1" max="100" value="80">
                        <span id="proficiencyValue">80%</span>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeSkillModal()">Cancel</button>
                    <button type="submit" class="btn-primary alternative shine">Save Skill</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Section Modal -->
    <div id="sectionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="sectionModalTitle">Edit Section</h3>
                <button class="close-btn" onclick="closeSectionModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="sectionForm" class="modal-form">
                <input type="hidden" id="sectionEcosystem">

                <div class="form-group">
                    <label>Section Title</label>
                    <input type="text" id="sectionTitle" placeholder="e.g., JavaScript Ecosystem" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea id="sectionDescription" placeholder="Brief description of this technology stack"
                        rows="3"></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeSectionModal()">Cancel</button>
                    <button type="submit" class="btn-primary alternative shine">Save Section</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        :root {
            --primary: #1a365d;
            --accent: #4c6fff;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #0f172a;
            --light: #ffffff;
            --gray: #64748b;
            --border: rgba(255, 255, 255, 0.1);
            --glass: rgba(255, 255, 255, 0.05);
            --shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-lg: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .skills-ecosystem {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: var(--light);
        }

        /* Header */
        .ecosystem-header {
            background: linear-gradient(135deg, rgba(26, 54, 93, 0.8), rgba(15, 23, 42, 0.9));
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 2rem 0;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .back-btn {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--light);
            text-decoration: none;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: var(--light);
        }

        .header-info h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        .header-info p {
            color: var(--gray);
            margin: 0.5rem 0 0 0;
            font-size: 1.1rem;
        }

        .header-stats {
            display: flex;
            gap: 1.5rem;
        }

        .stat-card {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 120px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .js-icon {
            background: linear-gradient(135deg, #f7df1e, #f0db4f);
            color: #000;
        }

        .php-icon {
            background: linear-gradient(135deg, #777bb4, #8892bf);
            color: var(--light);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--accent);
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Main Content */
        .ecosystem-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 2rem;
            display: flex;
            flex-direction: column;
            gap: 3rem;
        }

        /* Ecosystem Panels */
        .ecosystem-panel {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            transition: var(--transition);
        }

        .ecosystem-panel:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .panel-header {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            padding: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .panel-title {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex: 1;
        }

        .ecosystem-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            box-shadow: var(--shadow);
        }

        .js-gradient {
            background: linear-gradient(135deg, #f7df1e, #f0db4f);
            color: #000;
        }

        .php-gradient {
            background: linear-gradient(135deg, #777bb4, #8892bf);
            color: var(--light);
        }

        .title-content h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--light);
            margin: 0;
        }

        .title-content p {
            color: rgba(255, 255, 255, 0.8);
            margin: 0.5rem 0 0 0;
        }

        .panel-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .visibility-toggle {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .toggle-label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            color: var(--light);
            font-weight: 500;
        }

        .toggle-slider {
            width: 50px;
            height: 26px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 26px;
            position: relative;
            transition: var(--transition);
        }

        .toggle-slider::before {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background: var(--light);
            border-radius: 50%;
            top: 3px;
            left: 3px;
            transition: var(--transition);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        input:checked+.toggle-label .toggle-slider {
            background: var(--success);
        }

        input:checked+.toggle-label .toggle-slider::before {
            transform: translateX(24px);
        }

        .control-btn {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1.1rem;
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
        }

        .control-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        /* Panel Body */
        .panel-body {
            padding: 2rem;
        }

        .skills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        /* Skill Cards */
        .skill-card {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 2px solid transparent;
            border-radius: 16px;
            padding: 1.5rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .skill-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(76, 111, 255, 0.1), transparent);
            transition: var(--transition);
        }

        .skill-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(76, 111, 255, 0.2);
            border-color: var(--accent);
        }

        .skill-card:hover::before {
            left: 100%;
        }

        .skill-card.active {
            border-color: rgba(16, 185, 129, 0.3);
        }

        .skill-card.inactive {
            opacity: 0.7;
            border-color: rgba(100, 116, 139, 0.3);
        }

        .skill-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .skill-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--accent), var(--primary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--light);
            box-shadow: var(--shadow);
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            transition: var(--transition);
        }

        .status-dot.active {
            background: var(--success);
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
        }

        .status-dot.inactive {
            background: var(--gray);
        }

        .skill-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--light);
            margin: 0 0 1rem 0;
        }

        .skill-progress {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .progress-bar {
            flex: 1;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-right: 1rem;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent), var(--primary));
            border-radius: 8px;
            transition: width 0.8s ease;
            box-shadow: 0 0 10px rgba(76, 111, 255, 0.3);
        }

        .progress-text {
            font-weight: 700;
            color: var(--accent);
            font-size: 0.9rem;
        }

        .skill-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .action-btn.toggle {
            background: rgba(100, 116, 139, 0.2);
            color: var(--gray);
        }

        .action-btn.edit {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .action-btn.delete {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gray);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--light);
        }

        .empty-state p {
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }



        .btn-secondary {
            background: rgba(100, 116, 139, 0.2);
            color: var(--light);
            border: 1px solid var(--border);
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-secondary:hover {
            background: rgba(100, 116, 139, 0.3);
            transform: translateY(-1px);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
        }

        .modal-content {
            background: linear-gradient(145deg, rgba(26, 54, 93, 0.95), rgba(15, 23, 42, 0.95));
            margin: 5% auto;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            backdrop-filter: blur(20px);
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            padding: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.4rem;
            color: var(--light);
        }

        .close-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .modal-form {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--light);
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: 12px;
            background: var(--glass);
            color: var(--light);
            font-size: 1rem;
            transition: var(--transition);
            backdrop-filter: blur(10px);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(76, 111, 255, 0.1);
        }

        .form-group small {
            color: var(--gray);
            font-size: 0.85rem;
            margin-top: 0.25rem;
            display: block;
        }

        .range-input {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .range-input input[type="range"] {
            flex: 1;
            height: 8px;
            background: var(--border);
            border-radius: 8px;
            outline: none;
            -webkit-appearance: none;
        }

        .range-input input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            background: var(--accent);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: var(--shadow);
        }

        #proficiencyValue {
            font-weight: 700;
            color: var(--accent);
            font-size: 1.1rem;
            min-width: 50px;
        }

        .modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-stats {
                width: 100%;
                justify-content: center;
            }

            .panel-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .skills-grid {
                grid-template-columns: 1fr;
            }

            .modal-actions {
                flex-direction: column;
            }
        }
    </style>

    <script>
        // CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Variables
        let currentSkillId = null;
        let currentEcosystem = null;

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle listeners
            document.getElementById('js-visibility').addEventListener('change', function () {
                toggleSectionVisibility('javascript', this.checked);
            });

            document.getElementById('php-visibility').addEventListener('change', function () {
                toggleSectionVisibility('php', this.checked);
            });

            // Range input listener
            document.getElementById('skillProficiency').addEventListener('input', function () {
                document.getElementById('proficiencyValue').textContent = this.value + '%';
            });

            // Form listeners
            document.getElementById('skillForm').addEventListener('submit', handleSkillSubmit);
            document.getElementById('sectionForm').addEventListener('submit', handleSectionSubmit);
        });

        // Toggle section visibility
        function toggleSectionVisibility(ecosystem, isVisible) {
            fetch('{{ route("admin.skills-ecosystem.toggle-section") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ ecosystem: ecosystem })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error updating section visibility', 'error');
                });
        }

        // Toggle skill status
        function toggleSkill(skillId) {
            fetch(`{{ url('admin/skills-ecosystem') }}/${skillId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ toggle_status: true })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error toggling skill status', 'error');
                });
        }

        // Open skill modal
        function openSkillModal(ecosystem) {
            currentEcosystem = ecosystem;
            currentSkillId = null;

            document.getElementById('skillModalTitle').textContent = `Add ${ecosystem.charAt(0).toUpperCase() + ecosystem.slice(1)} Skill`;
            document.getElementById('skillEcosystem').value = ecosystem;
            document.getElementById('skillForm').reset();
            document.getElementById('skillProficiency').value = 80;
            document.getElementById('proficiencyValue').textContent = '80%';
            document.getElementById('skillModal').style.display = 'block';
        }

        // Edit skill
        function editSkill(id, name, icon, proficiency) {
            currentSkillId = id;

            document.getElementById('skillModalTitle').textContent = 'Edit Skill';
            document.getElementById('skillId').value = id;
            document.getElementById('skillName').value = name;
            document.getElementById('skillIcon').value = icon;
            document.getElementById('skillProficiency').value = proficiency;
            document.getElementById('proficiencyValue').textContent = proficiency + '%';
            document.getElementById('skillModal').style.display = 'block';
        }

        // Delete skill
        function deleteSkill(id) {
            if (confirm('Are you sure you want to delete this skill?')) {
                fetch(`{{ url('admin/skills-ecosystem') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error deleting skill', 'error');
                    });
            }
        }

        // Open section modal
        function openSectionModal(ecosystem) {
            document.getElementById('sectionEcosystem').value = ecosystem;
            document.getElementById('sectionModalTitle').textContent = `Edit ${ecosystem.charAt(0).toUpperCase() + ecosystem.slice(1)} Section`;
            document.getElementById('sectionModal').style.display = 'block';
        }

        // Close modals
        function closeSkillModal() {
            document.getElementById('skillModal').style.display = 'none';
        }

        function closeSectionModal() {
            document.getElementById('sectionModal').style.display = 'none';
        }

        // Handle skill form submission
        function handleSkillSubmit(e) {
            e.preventDefault();

            const formData = {
                name: document.getElementById('skillName').value,
                ecosystem: document.getElementById('skillEcosystem').value || currentEcosystem,
                icon: document.getElementById('skillIcon').value,
                proficiency: document.getElementById('skillProficiency').value
            };

            const url = currentSkillId ?
                `{{ url('admin/skills-ecosystem') }}/${currentSkillId}` :
                `{{ route('admin.skills-ecosystem.store') }}`;

            const method = currentSkillId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeSkillModal();
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error saving skill', 'error');
                });
        }

        // Handle section form submission
        function handleSectionSubmit(e) {
            e.preventDefault();

            const formData = {
                ecosystem: document.getElementById('sectionEcosystem').value,
                title: document.getElementById('sectionTitle').value,
                description: document.getElementById('sectionDescription').value
            };

            fetch(`{{ route('admin.skills-ecosystem.update-section') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeSectionModal();
                        showNotification(data.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error updating section', 'error');
                });
        }

        // Show notification
        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;

            // Add styles
            notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? 'var(--success)' : 'var(--danger)'};
        color: var(--light);
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: var(--shadow-lg);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        transform: translateX(100%);
        transition: var(--transition);
    `;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Close modals on outside click
        window.onclick = function (event) {
            const skillModal = document.getElementById('skillModal');
            const sectionModal = document.getElementById('sectionModal');

            if (event.target === skillModal) {
                closeSkillModal();
            }
            if (event.target === sectionModal) {
                closeSectionModal();
            }
        }
    </script>
</body>

</html>
