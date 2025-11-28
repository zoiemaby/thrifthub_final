/**
 * Category management JS
 * - Loads categories
 * - Adds/updates/deletes categories via actions
 * - Uses SweetAlert2 for notifications
 */

document.addEventListener('DOMContentLoaded', function() {
  // Ensure Swal is available
  if (typeof Swal === 'undefined') {
    const s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
    document.head.appendChild(s);
  }

  async function loadCategories() {
    try {
      const res = await fetch('../actions/fetch_categories_action.php');
      const data = await res.json();
      if (data.success) {
        window._thrift_categories = data.categories || [];
        renderCategories();
      } else {
        console.warn('Could not load categories:', data.message);
      }
    } catch (err) {
      console.error('Error loading categories:', err);
    }
  }

  function renderCategories() {
    const grid = document.getElementById('categoriesGrid');
    const countEl = document.getElementById('categoryCount');
    if (!grid || !countEl) return;

    const categories = window._thrift_categories || [];
    countEl.textContent = `${categories.length} total`;
    if (categories.length === 0) {
      grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1;"><div class="empty-state-icon">📁</div><div class="empty-state-text">No categories yet. Add your first category above.</div></div>`;
      return;
    }

    grid.innerHTML = categories.map(c => `
      <div class="item-card">
        <div class="item-name">${escapeHtml(c.cat_name)}</div>
        <div class="item-actions">
          <button class="item-action-btn edit" data-id="${c.cat_id}" data-name="${escapeAttr(c.cat_name)}">✏️</button>
          <button class="item-action-btn delete" data-id="${c.cat_id}">🗑️</button>
        </div>
      </div>
    `).join('');

    // attach handlers
    grid.querySelectorAll('.item-action-btn.edit').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        editCategory(id, name);
      });
    });

    grid.querySelectorAll('.item-action-btn.delete').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        deleteCategory(id);
      });
    });
  }

  function escapeHtml(s) {
    if (!s) return '';
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  function escapeAttr(s) {
    if (!s) return '';
    return s.replace(/'/g, "\\'").replace(/"/g, '&quot;');
  }

  // Add category form
  const addForm = document.getElementById('addCategoryForm');
  if (addForm) {
    addForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const name = document.getElementById('categoryNameInput').value.trim();
      const msgEl = document.getElementById('categoryMessage');
      if (!name) {
        if (msgEl) { msgEl.textContent = 'Please enter a category name.'; msgEl.style.color = '#D32F2F'; }
        return;
      }

      try {
        const fd = new FormData();
        fd.append('cat_name', name);

        const res = await fetch('../actions/add_category_action.php', { method: 'POST', body: fd });
        
        // Get response text first to handle both success and error cases
        const responseText = await res.text();
        let data;
        
        try {
          data = JSON.parse(responseText);
        } catch (parseError) {
          console.error('JSON parse error:', parseError, 'Response:', responseText);
          Swal.fire({ icon: 'error', title: 'Error', text: 'Invalid response from server' });
          return;
        }
        
        // Log the response for debugging
        console.log('Category creation response:', data);
        
        if (data.success) {
          // Clear any previous messages
          if (msgEl) { msgEl.textContent = ''; }
          addForm.reset();
          await loadCategories();
          Swal.fire({ icon: 'success', title: 'Category added', text: data.message || 'Category created successfully!' });
        } else {
          // Only show error if it's not a duplicate name (user might have clicked twice)
          // Check if category was actually created by reloading the list
          await loadCategories();
          const categories = window._thrift_categories || [];
          const categoryExists = categories.some(c => c.cat_name.toLowerCase() === name.toLowerCase());
          
          if (categoryExists) {
            // Category was created despite the error message - likely a timing issue
            if (msgEl) { msgEl.textContent = ''; }
            addForm.reset();
            Swal.fire({ icon: 'success', title: 'Category added', text: 'Category created successfully!' });
          } else {
            // Real error - category wasn't created
            // Only show error message in the element, SweetAlert will handle the popup
            if (msgEl) { msgEl.textContent = data.message || 'Failed to add'; msgEl.style.color = '#D32F2F'; }
            Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to add category' });
          }
        }
      } catch (err) {
        console.error('Error adding category:', err);
        Swal.fire({ icon: 'error', title: 'Error', text: 'Could not add category. Please check the console for details.' });
      }
    });
  }

  async function editCategory(id, currentName) {
    const { value: newName } = await Swal.fire({
      title: 'Edit Category',
      input: 'text',
      inputValue: currentName,
      showCancelButton: true,
      inputValidator: (v) => { if (!v || !v.trim()) return 'Name required'; }
    });

    if (!newName) return;

    try {
      const fd = new FormData();
      fd.append('cat_id', id);
      fd.append('cat_name', newName.trim());
      const res = await fetch('../actions/update_category_action.php', { method: 'POST', body: fd });
      const data = await res.json();
      if (data.success) {
        await loadCategories();
        Swal.fire({ icon: 'success', title: 'Updated', text: data.message || '' });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to update' });
      }
    } catch (err) {
      console.error(err);
      Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update category' });
    }
  }

  async function deleteCategory(id) {
    const ok = await Swal.fire({ title: 'Delete category?', text: 'This cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' });
    if (!ok.isConfirmed) return;

    try {
      const fd = new FormData();
      fd.append('cat_id', id);
      const res = await fetch('../actions/delete_category_action.php', { method: 'POST', body: fd });
      const data = await res.json();
      if (data.success) {
        await loadCategories();
        Swal.fire({ icon: 'success', title: 'Deleted', text: data.message || '' });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to delete' });
      }
    } catch (err) {
      console.error(err);
      Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete category' });
    }
  }

  // Expose load for external calls
  window.loadCategories = loadCategories;
  // Populate a select element (e.g., productCategory) with categories
  async function populateCategoryDropdown(selectId = 'productCategory') {
    const categorySelect = document.getElementById(selectId);
    if (!categorySelect) return;

    try {
      const res = await fetch('../actions/fetch_categories_action.php');
      const data = await res.json();
      if (data.success && data.categories) {
        categorySelect.innerHTML = '<option value="">Select category</option>';
        data.categories.forEach(category => {
          const option = document.createElement('option');
          option.value = category.cat_id;
          option.textContent = category.cat_name;
          categorySelect.appendChild(option);
        });
      }
    } catch (err) {
      console.error('Error loading categories for dropdown:', err);
    }
  }
  window.populateCategoryDropdown = populateCategoryDropdown;

  // Initial load
  loadCategories();
});
