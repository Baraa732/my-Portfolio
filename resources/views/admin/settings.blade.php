<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Settings - Admin Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .settings-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .settings-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(255,255,255,0.1);
        }
        
        .settings-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--light);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .settings-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            background: rgba(255,255,255,0.05);
            padding: 0.5rem;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        
        .tab-btn {
            padding: 1rem 1.5rem;
            background: transparent;
            border: none;
            color: var(--gray);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .tab-btn.active {
            background: var(--gradient);
            color: var(--light);
            box-shadow: 0 4px 15px rgba(76, 111, 255, 0.3);
        }
        
        .settings-content {
            display: none;
        }
        
        .settings-content.active {
            display: block;
        }
        
        .settings-section {
            background: linear-gradient(145deg, rgba(26, 54, 93, 0.4), rgba(15, 20, 25, 0.6));
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(20px);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--light);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .permission-badge {
            background: var(--gradient);
            color: var(--light);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .setting-item {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .setting-item:hover {
            border-color: rgba(76, 111, 255, 0.3);
            box-shadow: 0 4px 20px rgba(76, 111, 255, 0.1);
        }
        
        .setting-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .setting-name {
            font-weight: 600;
            color: var(--light);
            text-transform: capitalize;
        }
        
        .setting-required {
            color: var(--danger);
            font-size: 0.8rem;
        }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            color: var(--light);
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(76, 111, 255, 0.1);
        }
        
        .toggle-switch {
            position: relative;
            width: 50px;
            height: 24px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .toggle-switch.active {
            background: var(--gradient);
        }
        
        .toggle-slider {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            background: var(--light);
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .toggle-switch.active .toggle-slider {
            transform: translateX(26px);
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: var(--gradient);
            color: var(--light);
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: var(--light);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: var(--light);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            color: var(--light);
            font-weight: 600;
            z-index: 1000;
            transform: translateX(400px);
            transition: all 0.3s ease;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification.success {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
        }
        
        .notification.error {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }
        
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        @media (max-width: 768px) {
            .settings-container {
                padding: 1rem;
            }
            
            .settings-tabs {
                flex-direction: column;
            }
            
            .settings-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <div class="settings-header">
            <h1 class="settings-title">
                <i class="fas fa-cogs"></i>
                Advanced Settings
            </h1>
            <a href="/admin" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>

        <div class="settings-tabs">
            <button class="tab-btn active" data-tab="general">
                <i class="fas fa-globe"></i>
                General
            </button>
            <button class="tab-btn" data-tab="security">
                <i class="fas fa-shield-alt"></i>
                Security
            </button>
            <button class="tab-btn" data-tab="email">
                <i class="fas fa-envelope"></i>
                Email
            </button>
            <button class="tab-btn" data-tab="backup">
                <i class="fas fa-database"></i>
                Backup
            </button>
        </div>

        <div id="general-content" class="settings-content active">
            <div class="settings-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-globe"></i>
                        General Settings
                    </h2>
                </div>
                <div class="settings-grid" id="general-settings"></div>
                <div class="action-buttons">
                    <button class="btn btn-danger" onclick="resetSettings('general')">
                        <i class="fas fa-undo"></i>
                        Reset to Defaults
                    </button>
                    <button class="btn btn-primary" onclick="saveSettings('general')">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        <div id="security-content" class="settings-content">
            <div class="settings-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-shield-alt"></i>
                        Security Settings
                    </h2>
                    <span class="permission-badge">Super Admin Only</span>
                </div>
                <div class="settings-grid" id="security-settings"></div>
                <div class="action-buttons">
                    <button class="btn btn-danger" onclick="resetSettings('security')">
                        <i class="fas fa-undo"></i>
                        Reset to Defaults
                    </button>
                    <button class="btn btn-primary" onclick="saveSettings('security')">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        <div id="email-content" class="settings-content">
            <div class="settings-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-envelope"></i>
                        Email Configuration
                    </h2>
                </div>
                <div class="settings-grid" id="email-settings"></div>
                <div class="action-buttons">
                    <button class="btn btn-secondary" onclick="testEmail()">
                        <i class="fas fa-paper-plane"></i>
                        Send Test Email
                    </button>
                    <button class="btn btn-danger" onclick="resetSettings('email')">
                        <i class="fas fa-undo"></i>
                        Reset to Defaults
                    </button>
                    <button class="btn btn-primary" onclick="saveSettings('email')">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        <div id="backup-content" class="settings-content">
            <div class="settings-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-database"></i>
                        Backup Settings
                    </h2>
                    <span class="permission-badge">Super Admin Only</span>
                </div>
                <div class="settings-grid" id="backup-settings"></div>
                <div class="action-buttons">
                    <button class="btn btn-danger" onclick="resetSettings('backup')">
                        <i class="fas fa-undo"></i>
                        Reset to Defaults
                    </button>
                    <button class="btn btn-primary" onclick="saveSettings('backup')">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let settingsData = {};
        let userPermissions = {};

        document.addEventListener('DOMContentLoaded', function() {
            loadSettings();
            initializeTabs();
        });

        function initializeTabs() {
            const tabBtns = document.querySelectorAll('.tab-btn');
            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const tab = this.dataset.tab;
                    switchTab(tab);
                });
            });
        }

        function switchTab(tab) {
            // Update tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelector(`[data-tab="${tab}"]`).classList.add('active');

            // Update content
            document.querySelectorAll('.settings-content').forEach(content => content.classList.remove('active'));
            document.getElementById(`${tab}-content`).classList.add('active');
        }

        async function loadSettings() {
            try {
                const response = await fetch('/admin/settings/data', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                settingsData = data.settings;
                userPermissions = data.user_permissions;

                renderSettings();
            } catch (error) {
                showNotification('Failed to load settings', 'error');
            }
        }

        function renderSettings() {
            Object.keys(settingsData).forEach(group => {
                const container = document.getElementById(`${group}-settings`);
                if (!container) return;

                container.innerHTML = '';
                
                Object.keys(settingsData[group]).forEach(key => {
                    const setting = settingsData[group][key];
                    const settingElement = createSettingElement(key, setting, group);
                    container.appendChild(settingElement);
                });
            });
        }

        function createSettingElement(key, setting, group) {
            const div = document.createElement('div');
            div.className = 'setting-item';

            const config = setting.config;
            const value = setting.value;

            let inputElement = '';
            
            switch (config.type) {
                case 'boolean':
                    inputElement = `
                        <div class="toggle-switch ${value ? 'active' : ''}" onclick="toggleSetting(this, '${group}', '${key}')">
                            <div class="toggle-slider"></div>
                        </div>
                    `;
                    break;
                case 'password':
                    inputElement = `<input type="password" class="form-input" data-group="${group}" data-key="${key}" placeholder="Enter new password">`;
                    break;
                case 'text':
                    inputElement = `<textarea class="form-textarea" data-group="${group}" data-key="${key}" rows="3">${value || ''}</textarea>`;
                    break;
                default:
                    if (config.options) {
                        inputElement = `
                            <select class="form-select" data-group="${group}" data-key="${key}">
                                ${config.options.map(option => 
                                    `<option value="${option}" ${value === option ? 'selected' : ''}>${option}</option>`
                                ).join('')}
                            </select>
                        `;
                    } else {
                        inputElement = `<input type="${config.type === 'integer' ? 'number' : config.type}" class="form-input" data-group="${group}" data-key="${key}" value="${value || ''}" ${config.min ? `min="${config.min}"` : ''} ${config.max ? `max="${config.max}"` : ''}>`;
                    }
            }

            div.innerHTML = `
                <div class="setting-label">
                    <span class="setting-name">${key.replace(/_/g, ' ')}</span>
                    ${config.required ? '<span class="setting-required">*</span>' : ''}
                </div>
                ${inputElement}
            `;

            return div;
        }

        function toggleSetting(element, group, key) {
            element.classList.toggle('active');
            const value = element.classList.contains('active');
            
            if (!settingsData[group]) settingsData[group] = {};
            if (!settingsData[group][key]) settingsData[group][key] = {};
            settingsData[group][key].value = value;
        }

        async function saveSettings(group) {
            const container = document.getElementById(`${group}-settings`);
            const inputs = container.querySelectorAll('input, select, textarea');
            const settings = {};

            inputs.forEach(input => {
                const key = input.dataset.key;
                let value = input.value;
                
                if (input.type === 'number') {
                    value = parseInt(value) || 0;
                } else if (input.type === 'checkbox') {
                    value = input.checked;
                }
                
                if (value !== '' || input.type === 'password') {
                    settings[key] = value;
                }
            });

            // Add toggle values
            Object.keys(settingsData[group] || {}).forEach(key => {
                if (settingsData[group][key].config && settingsData[group][key].config.type === 'boolean') {
                    settings[key] = settingsData[group][key].value;
                }
            });

            try {
                container.classList.add('loading');
                
                const response = await fetch('/admin/settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        group: group,
                        settings: settings
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    showNotification('Settings saved successfully', 'success');
                    loadSettings(); // Reload to get updated values
                } else {
                    showNotification(data.message || 'Failed to save settings', 'error');
                }
            } catch (error) {
                showNotification('Network error occurred', 'error');
            } finally {
                container.classList.remove('loading');
            }
        }

        async function resetSettings(group) {
            if (!confirm(`Are you sure you want to reset ${group} settings to defaults?`)) {
                return;
            }

            try {
                const response = await fetch('/admin/settings/reset', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ group: group })
                });

                const data = await response.json();
                
                if (data.success) {
                    showNotification('Settings reset successfully', 'success');
                    loadSettings();
                } else {
                    showNotification(data.message || 'Failed to reset settings', 'error');
                }
            } catch (error) {
                showNotification('Network error occurred', 'error');
            }
        }

        async function testEmail() {
            const email = prompt('Enter email address to send test email:');
            if (!email) return;

            try {
                const response = await fetch('/admin/settings/test-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email: email })
                });

                const data = await response.json();
                
                if (data.success) {
                    showNotification('Test email sent successfully', 'success');
                } else {
                    showNotification(data.message || 'Failed to send test email', 'error');
                }
            } catch (error) {
                showNotification('Network error occurred', 'error');
            }
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => notification.classList.add('show'), 100);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>