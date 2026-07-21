document.addEventListener('DOMContentLoaded', function () {

    // Helper to extract plain text to avoid HTML injection
    function getSafeText(str) {
        const div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    // Event delegation on submit
    document.addEventListener('submit', async function (event) {
        const form = event.target;
        
        const isAddForm = form.classList.contains('data-ajax-add-form');
        const isDeleteForm = form.classList.contains('data-ajax-delete-form');
        const isEditForm = form.classList.contains('data-ajax-edit-form');

        if (!isAddForm && !isDeleteForm && !isEditForm) {
            return;
        }

        event.preventDefault();

        if (isDeleteForm) {
            const confirmMsg = form.getAttribute('data-confirm') || 'Yakin ingin menghapus data ini?';
            if (!confirm(confirmMsg)) {
                return;
            }
        }

        const submitBtn = form.querySelector('[type="submit"]') || form.querySelector('button:not([type="button"])');
        let originalBtnContent = '';
        let originalBtnDisabled = false;

        if (submitBtn) {
            originalBtnContent = submitBtn.innerHTML;
            originalBtnDisabled = submitBtn.disabled;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Loading...';
        }

        try {
            const formData = new FormData(form);
            const action = form.getAttribute('action') || window.location.href;
            
            // fetch doesn't naturally handle DELETE/PUT forms nicely if it's sent as POST via _method, 
            // but since Laravel accepts POST with _method="DELETE", we can just send it as POST.
            const method = form.getAttribute('method') || 'POST';

            const response = await fetch(action, {
                method: method,
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.status === 422) {
                const data = await response.json();
                showAdminAlert(data.message || 'Validasi gagal.', 'danger', data.errors);
                return;
            }

            if (!response.ok) {
                const data = await response.json().catch(() => ({}));
                showAdminAlert(data.message || 'Gagal memproses data.', 'danger');
                return;
            }

            const data = await response.json();
            showAdminAlert(data.message || 'Berhasil memproses data.', 'success');

            if (isAddForm) {
                form.reset();
            }

            if (isEditForm) {
                const editModalEl = document.getElementById('adminEditModal');
                if (editModalEl) {
                    const editModal = bootstrap.Modal.getInstance(editModalEl) || new bootstrap.Modal(editModalEl);
                    editModal.hide();
                }
            }

            const sectionId = form.getAttribute('data-refresh-section');
            if (sectionId) {
                await refreshAdminSection(sectionId);
            }

        } catch (error) {
            console.error('AJAX Form Error:', error);
            showAdminAlert('Terjadi kesalahan pada sistem.', 'danger');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = originalBtnDisabled;
                submitBtn.innerHTML = originalBtnContent;
            }
        }
    });

    // Event delegation for edit buttons
    document.addEventListener('click', async function (event) {
        const btn = event.target.closest('.ajax-edit-btn');
        if (!btn) return;

        event.preventDefault();
        
        const url = btn.getAttribute('href');
        const modalEl = document.getElementById('adminEditModal');
        const modalBody = document.getElementById('adminEditModalBody');

        if (!modalEl || !modalBody) return;

        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        
        // Show spinner
        modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        modal.show();

        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Gagal mengambil data form edit.');
            }

            const data = await response.json();
            // Expected JSON: { html: '...' }
            if (data.html) {
                modalBody.innerHTML = data.html;
            } else {
                throw new Error('Format respon tidak sesuai.');
            }
        } catch (error) {
            console.error('AJAX Edit Error:', error);
            modalBody.innerHTML = '<div class="alert alert-danger">Gagal memuat form edit. Silakan coba lagi.</div>';
        }
    });

    async function refreshAdminSection(sectionId) {
        try {
            const currentUrl = new URL(window.location.href);
            currentUrl.hash = '';

            const response = await fetch(currentUrl.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Gagal memperbarui tampilan.');
            }

            const html = await response.text();
            const parser = new DOMParser();
            const documentResult = parser.parseFromString(html, 'text/html');

            const currentSection = document.getElementById(sectionId);
            const updatedSection = documentResult.getElementById(sectionId);

            if (!currentSection || !updatedSection) {
                throw new Error('Bagian data tidak ditemukan.');
            }

            currentSection.replaceWith(updatedSection);

            // Sync all specific dropdowns if needed
            const selectsToSync = ['select[name="country_id"]', 'select[name="port_country_id"]'];
            selectsToSync.forEach(selector => {
                const currentSelect = document.querySelector(selector);
                const updatedSelect = documentResult.querySelector(selector);
                if (currentSelect && updatedSelect) {
                    currentSelect.replaceWith(updatedSelect);
                }
            });

            const element = document.getElementById(sectionId);
            if (element) {
                element.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        } catch (error) {
            console.error('Refresh Section Error:', error);
            showAdminAlert('Tampilan gagal diperbarui otomatis, silakan muat ulang halaman.', 'danger');
        }
    }

    function showAdminAlert(message, type = 'success', errors = null) {
        const alertContainer = document.getElementById('adminAjaxAlert');
        if (!alertContainer) return;

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${getSafeText(type)} alert-dismissible fade show`;
        alertDiv.setAttribute('role', 'alert');

        const messageSpan = document.createElement('span');
        messageSpan.textContent = message; // textContent prevents HTML injection
        alertDiv.appendChild(messageSpan);

        if (errors) {
            const errorList = document.createElement('ul');
            errorList.className = 'mb-0 mt-2';
            for (const field in errors) {
                if (errors.hasOwnProperty(field)) {
                    errors[field].forEach(errText => {
                        const li = document.createElement('li');
                        li.textContent = errText;
                        errorList.appendChild(li);
                    });
                }
            }
            alertDiv.appendChild(errorList);
        }

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'btn-close';
        closeBtn.setAttribute('data-bs-dismiss', 'alert');
        closeBtn.setAttribute('aria-label', 'Close');
        alertDiv.appendChild(closeBtn);

        alertContainer.innerHTML = '';
        alertContainer.appendChild(alertDiv);

        if (type === 'success') {
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.classList.remove('show');
                    setTimeout(() => alertDiv.remove(), 150);
                }
            }, 5000);
        }
    }
});
