// Category Management for Admin Dashboard
window.categoryManager = {
    async loadCategories() {
        try {
            const response = await fetch('/admin/project-categories');
            const categories = await response.json();
            this.renderCategories(categories);
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    },

    renderCategories(categories) {
        const container = document.getElementById('categories-container');
        if (!container) return;

        if (categories.length === 0) {
            container.innerHTML = `
                <div class="text-center" style="grid-column: 1/-1; padding: 2rem;">
                    <i class="fas fa-tags" style="font-size: 3rem; color: var(--gray-dark); margin-bottom: 1rem;"></i>
                    <h4 style="color: var(--gray);">No Categories Yet</h4>
                    <p style="color: var(--gray-dark);">Add categories to organize project filtering</p>
                </div>
            `;
            return;
        }

        container.innerHTML = categories.map(cat => `
            <div class="category-item" style="background: rgba(15, 20, 25, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h4 style="color: var(--light); margin: 0;">${cat.name}</h4>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <label class="toggle-switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" ${cat.is_visible ? 'checked' : ''} onchange="categoryManager.toggleVisibility(${cat.id})" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: ${cat.is_visible ? '#4c6fff' : '#ccc'}; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 18px; width: 18px; left: ${cat.is_visible ? '28px' : '3px'}; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                    <span style="background: rgba(76, 111, 255, 0.2); color: #4c6fff; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem;">${cat.slug}</span>
                    <span style="background: rgba(59, 130, 246, 0.2); color: #3b82f6; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem;">Order: ${cat.order}</span>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button class="btn btn-primary btn-sm" onclick="categoryManager.editCategory(${cat.id}, '${cat.name}', ${cat.order})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="categoryManager.deleteCategory(${cat.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    },

    showAddModal() {
        const modal = document.createElement('div');
        modal.innerHTML = `
            <div class="modal-overlay">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Add Category</h3>
                        <button class="modal-close" onclick="this.closest('.modal-overlay').remove()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="category-form" onsubmit="categoryManager.saveCategory(event)">
                            <div class="form-group">
                                <label>Category Name</label>
                                <input type="text" name="name" required placeholder="e.g., Web Development">
                            </div>
                            <div class="form-group">
                                <label>Order</label>
                                <input type="number" name="order" value="0" min="0">
                            </div>
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="is_visible" checked>
                                    <span class="checkmark"></span>
                                    Visible in Portfolio
                                </label>
                            </div>
                            <div class="modal-actions">
                                <button type="button" class="btn btn-secondary" onclick="this.closest('.modal-overlay').remove()">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    },

    editCategory(id, name, order) {
        const modal = document.createElement('div');
        modal.innerHTML = `
            <div class="modal-overlay">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Edit Category</h3>
                        <button class="modal-close" onclick="this.closest('.modal-overlay').remove()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="category-form" onsubmit="categoryManager.updateCategory(event, ${id})">
                            <div class="form-group">
                                <label>Category Name</label>
                                <input type="text" name="name" value="${name}" required>
                            </div>
                            <div class="form-group">
                                <label>Order</label>
                                <input type="number" name="order" value="${order}" min="0">
                            </div>
                            <div class="modal-actions">
                                <button type="button" class="btn btn-secondary" onclick="this.closest('.modal-overlay').remove()">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    },

    async saveCategory(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        
        try {
            const response = await fetch('/admin/project-categories', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    order: formData.get('order'),
                    is_visible: formData.get('is_visible') === 'on'
                })
            });
            
            const result = await response.json();
            if (result.success) {
                window.adminDashboard.showNotification(result.message, 'success');
                form.closest('.modal-overlay').remove();
                this.loadCategories();
            }
        } catch (error) {
            window.adminDashboard.showNotification('Error saving category', 'error');
        }
    },

    async updateCategory(event, id) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        
        try {
            const response = await fetch(`/admin/project-categories/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    order: formData.get('order')
                })
            });
            
            const result = await response.json();
            if (result.success) {
                window.adminDashboard.showNotification(result.message, 'success');
                form.closest('.modal-overlay').remove();
                this.loadCategories();
            }
        } catch (error) {
            window.adminDashboard.showNotification('Error updating category', 'error');
        }
    },

    async toggleVisibility(id) {
        try {
            const response = await fetch(`/admin/project-categories/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const result = await response.json();
            if (result.success) {
                window.adminDashboard.showNotification(result.message, 'success');
            }
        } catch (error) {
            window.adminDashboard.showNotification('Error toggling visibility', 'error');
        }
    },

    async deleteCategory(id) {
        const confirmed = await window.adminDashboard.showConfirm(
            'This category will be permanently deleted.',
            'Delete Category',
            'danger'
        );
        if (!confirmed) return;
        
        try {
            const response = await fetch(`/admin/project-categories/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const result = await response.json();
            if (result.success) {
                window.adminDashboard.showNotification(result.message, 'success');
                this.loadCategories();
            }
        } catch (error) {
            window.adminDashboard.showNotification('Error deleting category', 'error');
        }
    }
};
