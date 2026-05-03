<!-- Backup Management View -->

<section id="backups-section"
    class="relative flex flex-col h-full w-full
           p-6 rounded-xl mx-auto
           overflow-hidden">

    <!-- spinner overlay while loading backups -->
    <div id="backups-spinner"
         class="absolute inset-0 flex items-center justify-center bg-gray-900/25 z-20 hidden">
        <div class="w-12 h-12 border-4 border-t-blue-500 border-gray-600 rounded-full animate-spin"></div>
    </div>

    <!-- header with title and create button -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-base">Server Backups</h2>
        <div class="flex items-center gap-4">
            <button id="create-backup-btn"
                    class="px-4 py-2 bg-green-600 font-bold text-white rounded-xl hover:bg-green-700 transition-colors">
                Create Backup
            </button>
            <span class="text-sm text-base">Limit: {{ $server['feature_limits']['backups'] ?? 'Unlimited' }}</span>
        </div>
    </div>

    <!-- backup list container -->
    <div id="backups-container" class="flex-1 space-y-4 overflow-auto">
        <!-- backups will be dynamically inserted here -->
    </div>

</section>

<!-- Toast container -->
<div id="toast-container" class="fixed top-4 right-4 space-y-2 z-50 pointer-events-none"></div>

<!-- Create Backup Modal -->
<div id="create-backup-modal" class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-black/40 hidden z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Create Backup</h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-base/40 mb-1">Backup Name (Optional)</label>
                <input type="text" id="backup-name-input" placeholder="Leave empty for auto-generated name"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-background-secondary/50 text-gray-900 dark:text-gray-100">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-base/40 mb-1">Ignored Files (Optional)</label>
                <textarea id="backup-ignored-input" placeholder="Files/folders to ignore, one per line"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-background-secondary/50 text-gray-900 dark:text-gray-100 h-20 resize-none"></textarea>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" id="backup-locked-checkbox" class="mr-2">
                <label class="text-sm text-gray-700 dark:text-base/40">Lock backup (prevents automatic deletion)</label>
            </div>
        </div>
        
        <div class="flex justify-end gap-2 mt-6">
            <button id="create-backup-cancel" class="px-4 py-2 bg-gray-200 dark:bg-background-secondary/50 text-gray-800 dark:text-gray-100 rounded hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
            <button id="create-backup-confirm" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Create</button>
        </div>
    </div>
</div>

<!-- Confirm Action Modal -->
<div id="confirm-modal" class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-black/40 hidden z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm mx-4">
        <p id="confirm-message" class="text-gray-800 dark:text-gray-100 mb-4">Are you sure?</p>
        <div class="flex justify-end gap-2">
            <button id="confirm-cancel" class="px-4 py-2 bg-gray-200 dark:bg-background-secondary/50 text-gray-800 dark:text-gray-100 rounded hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
            <button id="confirm-ok" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Confirm</button>
        </div>
    </div>
</div>

@script
<script type="module">
(async () => {
    // --- Toast Helper ---
    function getToastContainer() {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed top-4 right-4 space-y-2 z-50 pointer-events-none';
            document.body.appendChild(container);
        }
        return container;
    }

    function showToast(message, type = 'info') {
        const container = getToastContainer();
        const toast = document.createElement('div');
        let borderColor, iconSvg;
        if (type === 'success') {
            borderColor = 'border-green-500';
            iconSvg = '<svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        } else if (type === 'error') {
            borderColor = 'border-red-500';
            iconSvg = '<svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
        } else {
            borderColor = 'border-yellow-500';
            iconSvg = '<svg class="w-6 h-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 17a5 5 0 100-10 5 5 0 000 10z"/></svg>';
        }
        toast.className = `flex items-start gap-3 bg-white dark:bg-gray-800 border-l-4 ${borderColor} shadow-lg rounded-xl p-4 pointer-events-auto transition-opacity duration-500`;
        toast.innerHTML = `
            <div class="flex-shrink-0">${iconSvg}</div>
            <div class="flex-1 text-sm text-gray-900 dark:text-gray-100">${message}</div>
        `;
        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 500);
        }, 3500);
    }

    // --- API Client ---
    class ApiClient {
        constructor(serviceId) {
            this.baseUrl = `/services/${serviceId}`;
        }

        async request(endpoint = '', method = 'GET', body = null) {
            const spinner = document.getElementById('backups-spinner');
            if (spinner) spinner.classList.remove('hidden');

            try {
                const url = this.baseUrl + endpoint;
                const options = {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                };
                if (body) options.body = JSON.stringify(body);

                const response = await fetch(url, options);
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || data.message || `HTTP ${response.status}`);
                }

                return data;
            } finally {
                if (spinner) spinner.classList.add('hidden');
            }
        }

        listBackups() {
            return this.request('/backups');
        }

        createBackup(data) {
            return this.request('/backups', 'POST', data);
        }

        deleteBackup(id) {
            return this.request(`/backups/${id}`, 'DELETE');
        }        restoreBackup(id, truncate = false) {
            return this.request(`/backups/${id}/restore`, 'POST', { truncate });
        }        downloadBackup(id) {
            return this.request(`/backups/${id}/download`, 'GET');
        }

        toggleBackupLock(id, isLocked) {
            return this.request(`/backups/${id}/lock`, 'POST', { is_locked: isLocked });
        }
    }

    // --- Mount / Unmount System ---
    let isMounted = false;
    let mounting = false; // Flag to prevent concurrent mounting
    let lastBackupsCheck = 0; // Debounce rapid checks
    let backupsWatchInterval = null; // Store interval reference for cleanup
    let eventListeners = []; // Track event listeners for cleanup

    // Function to check if elements are already initialized
    function elementsAlreadyInitialized() {
        const backupsContainer = document.getElementById('backups-container');
        const createBtn = document.getElementById('create-backup-btn');
        
        // Check for signs that elements are already initialized
        const hasInitializedContent = backupsContainer && (
            backupsContainer.hasAttribute('data-backups-initialized') ||
            backupsContainer.children.length > 0
        );

        const hasActiveListeners = createBtn && (
            createBtn.hasAttribute('data-backups-listeners') ||
            (createBtn._backupsEventListeners && createBtn._backupsEventListeners.length > 0)
        );

        return hasInitializedContent || hasActiveListeners;
    }

    // Function to cleanup existing elements before reinitializing
    function cleanupExistingElements(force = false) {
        console.log('Cleaning up existing backups elements...', force ? '(forced)' : '');

        // Clean up containers
        const containers = ['backups-container'];
        containers.forEach(containerId => {
            const container = document.getElementById(containerId);
            if (container) {
                try {
                    container.innerHTML = '';
                    container.removeAttribute('data-backups-initialized');
                } catch (e) {
                    console.warn(`Error cleaning container ${containerId}:`, e);
                }
            }
        });

        // Clean up event listeners from tracked elements
        const trackedElements = ['create-backup-btn', 'create-backup-cancel', 'create-backup-confirm', 
                                 'create-db-close', 'confirm-cancel', 'confirm-ok'];
        trackedElements.forEach(elementId => {
            try {
                const element = document.getElementById(elementId);
                if (element) {
                    // Clone element to remove ALL event listeners
                    const newElement = element.cloneNode(true);
                    if (element.parentNode) {
                        element.parentNode.replaceChild(newElement, element);
                    }
                    newElement.removeAttribute('data-backups-listeners');
                    newElement._backupsEventListeners = [];
                }
            } catch (e) {
                console.warn(`Error cleaning element ${elementId}:`, e);
            }
        });

        // Clean up modals
        const modals = ['create-backup-modal', 'confirm-modal'];
        modals.forEach(modalId => {
            try {
                const modal = document.getElementById(modalId);
                if (modal) modal.classList.add('hidden');
            } catch (e) {
                console.warn(`Error hiding modal ${modalId}:`, e);
            }
        });

        // Clear tracked event listeners
        eventListeners.forEach(({ element, type, listener }) => {
            try {
                if (element && typeof element.removeEventListener === 'function') {
                    element.removeEventListener(type, listener);
                }
            } catch (e) {
                console.warn('Error removing event listener:', e);
            }
        });
        eventListeners = [];

        // Force garbage collection if available (development only)
        if (force && window.gc && typeof window.gc === 'function') {
            try {
                window.gc();
            } catch (e) {
                // Ignore - gc() not available in production
            }
        }
    }

    // Helper function to add tracked event listeners
    function addTrackedEventListener(element, type, listener) {
        if (!element) return;
        
        element.addEventListener(type, listener);
        eventListeners.push({ element, type, listener });
        
        // Mark element as having listeners
        if (!element._backupsEventListeners) {
            element._backupsEventListeners = [];
        }
        element._backupsEventListeners.push({ type, listener });
        element.setAttribute('data-backups-listeners', 'true');
    }

    async function mountBackups() {
        if (isMounted || mounting) {
            console.log('Mount backups called but already mounted/mounting:', { isMounted, mounting });
            return; // Prevent double mounting
        }

        // Check if elements are already initialized and clean them up
        if (elementsAlreadyInitialized()) {
            console.log('Backups elements already initialized, cleaning up before mounting...');
            cleanupExistingElements();
            // Small delay to ensure cleanup completes
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        console.log('Starting backups mount...');
        mounting = true; // Set mounting flag

        try {
            const api = new ApiClient({{ $service->id }});
            const container = document.getElementById('backups-container');
            const createBtn = document.getElementById('create-backup-btn');
            const createModal = document.getElementById('create-backup-modal');
            const createCancel = document.getElementById('create-backup-cancel');
            const createConfirm = document.getElementById('create-backup-confirm');
            const nameInput = document.getElementById('backup-name-input');
            const ignoredInput = document.getElementById('backup-ignored-input');
            const lockedCheckbox = document.getElementById('backup-locked-checkbox');
            const confirmModal = document.getElementById('confirm-modal');
            const confirmMessage = document.getElementById('confirm-message');
            const confirmCancel = document.getElementById('confirm-cancel');
            const confirmOk = document.getElementById('confirm-ok');
            
            let currentBackupAction = null;
            let isCreatingBackup = false; // Flag to prevent duplicate backup creation
            let isDeletingBackup = false; // Flag to prevent duplicate backup deletion
            let isTogglingLock = false; // Flag to prevent duplicate lock operations
            let isRestoring = false; // Flag to prevent duplicate restore operations
            let refreshInterval = null; // Auto-refresh interval for in-progress backups

            // Verify required elements exist
            if (!container) {
                throw new Error('Backups container not found');
            }

            // Load and display backups with error handling
            async function loadBackups() {
                const container = document.getElementById('backups-container');
                if (!container || !isMounted) return;

                try {
                    const response = await api.listBackups();
                    if (isMounted) { // Check if still mounted before updating
                        displayBackups(response.data || []);
                    }
                } catch (err) {
                    if (!isMounted) return; // Don't show errors if unmounted
                    
                    console.error('Failed to load backups:', err);
                    showToast(`Failed to load backups: ${err.message}`, 'error');
                    container.innerHTML = `
                        <div class="text-center py-8 text-base/50">
                            <p>Failed to load backups</p>
                            <button class="retry-btn mt-2 text-blue-400 hover:text-blue-300">Retry</button>
                        </div>
                    `;
                    
                    const retryBtn = container.querySelector('.retry-btn');
                    if (retryBtn) {
                        addTrackedEventListener(retryBtn, 'click', loadBackups);
                    }
                }
            }

            function displayBackups(backups) {
                const container = document.getElementById('backups-container');
                if (!container || !isMounted) return;

                // Mark container as initialized
                container.setAttribute('data-backups-initialized', 'true');

                // Check if there are any in-progress backups
                const hasInProgressBackups = backups.some(backup => 
                    backup.attributes.completed_at === null
                );

                // Setup auto-refresh for in-progress backups
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                }
                
                if (hasInProgressBackups && isMounted) {
                    console.log('Setting up auto-refresh for in-progress backups');
                    refreshInterval = setInterval(() => {
                        if (isMounted) {
                            console.log('Auto-refreshing backup status...');
                            loadBackups();
                        }
                    }, 10000); // Refresh every 10 seconds
                }

                if (!backups.length) {
                    container.innerHTML = `
                        <div class="text-center py-8 text-base/50">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p>No backups found</p>
                            <p class="text-sm">Click "Create Backup" to create your first backup</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = backups
                    .map(backup => createBackupCard(backup))
                    .join('');

                attachBackupEventListeners();
            }

            function createBackupCard(backup) {
                const attr = backup.attributes;
                
                // Better status detection
                const isCompleted = attr.completed_at !== null;
                const isSuccessful = attr.is_successful === true;
                const isFailed = attr.completed_at !== null && attr.is_successful === false;
                const isInProgress = attr.completed_at === null;
                
                const isLocked = attr.is_locked;
                const createdAt = new Date(attr.created_at).toLocaleString();
                const completedAt = attr.completed_at ? new Date(attr.completed_at).toLocaleString() : null;
                const size = attr.bytes ? formatBytes(attr.bytes) : 'Unknown';
                
                // Determine status display
                let statusBadge;
                if (isFailed) {
                    statusBadge = '<span class="px-2 py-1 bg-red-600 text-white text-xs rounded">Failed</span>';
                } else if (isCompleted && isSuccessful) {
                    statusBadge = '<span class="px-2 py-1 bg-green-600 text-white text-xs rounded">Completed</span>';
                } else if (isInProgress) {
                    statusBadge = '<span class="px-2 py-1 bg-yellow-600 text-white text-xs rounded animate-pulse">In Progress</span>';
                } else {
                    statusBadge = '<span class="px-2 py-1 bg-gray-600 text-white text-xs rounded">Unknown</span>';
                }
            
            return `
                <div class="bg-background-secondary/50 border border-neutral rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="text-lg font-medium text-base">
                                    ${attr.name || 'Unnamed Backup'}
                                </div>
                                <div class="flex gap-2">
                                    ${statusBadge}
                                    ${isLocked ? '<span class="px-2 py-1 bg-blue-600 text-white text-xs rounded">Locked</span>' : ''}
                                </div>
                            </div>
                            <div class="text-sm text-base/75 space-y-1">
                                <div><strong>Created:</strong> ${createdAt}</div>
                                <div><strong>Completed:</strong> ${completedAt || 'In progress...'}</div>
                                <div><strong>Size:</strong> ${size}</div>
                                ${attr.checksum ? `<div><strong>Checksum:</strong> <span class="font-mono text-xs">${attr.checksum}</span></div>` : ''}
                            </div>
                        </div>                        <div class="flex items-center gap-2">
                            ${(isCompleted && isSuccessful) ? `
                                <button class="download-btn px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" 
                                        data-id="${attr.uuid}">
                                    Download
                                </button>
                            ` : ''}
                            ${(isCompleted && isSuccessful) ? `
                                <button class="restore-btn px-3 py-1 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700" 
                                        data-id="${attr.uuid}">
                                    Restore
                                </button>
                            ` : ''}
                            <button class="lock-btn px-3 py-1 ${isLocked ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-600 hover:bg-background-secondary/50'} text-white text-sm rounded" 
                                    data-id="${attr.uuid}" data-locked="${isLocked}">
                                ${isLocked ? 'Unlock' : 'Lock'}
                            </button>
                            ${!isLocked ? `
                                <button class="delete-btn px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700" 
                                        data-id="${attr.uuid}">
                                    Delete
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function attachBackupEventListeners() {
            // Download backup
            document.querySelectorAll('.download-btn').forEach(btn => {
                addTrackedEventListener(btn, 'click', async () => {
                    const id = btn.dataset.id;
                    try {
                        const response = await api.downloadBackup(id);
                        if (response.attributes?.url) {
                            window.open(response.attributes.url, '_blank');
                            showToast('Download link generated successfully', 'success');
                        } else {
                            showToast('Download link not available', 'error');
                        }
                    } catch (err) {
                        showToast(`Failed to generate download link: ${err.message}`, 'error');
                    }
                });
            });

            // Restore backup
            document.querySelectorAll('.restore-btn').forEach(btn => {
                addTrackedEventListener(btn, 'click', () => {
                    if (isRestoring) return; // Prevent duplicate restore
                    
                    const id = btn.dataset.id;
                    showConfirmModal(
                        'Are you sure you want to restore this backup? This will overwrite all current server files and cannot be undone.',
                        async () => {
                            if (isRestoring) return; // Double-check
                            isRestoring = true;
                            
                            try {
                                await api.restoreBackup(id, true);
                                showToast('Backup restore initiated successfully', 'success');
                                if (isMounted) loadBackups();
                            } catch (err) {
                                showToast(`Failed to restore backup: ${err.message}`, 'error');
                            } finally {
                                isRestoring = false; // Reset flag
                            }
                        }
                    );
                });
            });

            // Delete backup
            document.querySelectorAll('.delete-btn').forEach(btn => {
                addTrackedEventListener(btn, 'click', () => {
                    if (isDeletingBackup) return; // Prevent duplicate deletion
                    
                    const id = btn.dataset.id;
                    showConfirmModal(
                        'Are you sure you want to delete this backup? This action cannot be undone.',
                        async () => {
                            if (isDeletingBackup) return; // Double-check in case confirm is clicked multiple times
                            isDeletingBackup = true;
                            
                            try {
                                await api.deleteBackup(id);
                                showToast('Backup deleted successfully', 'success');
                                if (isMounted) loadBackups();
                            } catch (err) {
                                showToast(`Failed to delete backup: ${err.message}`, 'error');
                            } finally {
                                isDeletingBackup = false; // Reset flag
                            }
                        }
                    );
                });
            });

            // Lock/Unlock backup
            document.querySelectorAll('.lock-btn').forEach(btn => {
                addTrackedEventListener(btn, 'click', async () => {
                    if (isTogglingLock) return; // Prevent duplicate lock operations
                    isTogglingLock = true;
                    
                    const id = btn.dataset.id;
                    const isCurrentlyLocked = btn.dataset.locked === 'true';
                    const newLockState = !isCurrentlyLocked;
                    const action = newLockState ? 'lock' : 'unlock';
                    
                    try {
                        await api.toggleBackupLock(id, newLockState);
                        showToast(`Backup ${action}ed successfully`, 'success');
                        if (isMounted) loadBackups();
                    } catch (err) {
                        showToast(`Failed to ${action} backup: ${err.message}`, 'error');
                    } finally {
                        isTogglingLock = false; // Reset flag
                    }
                });
            });
        }

        // Setup event listeners with error handling
            if (createBtn) {
                addTrackedEventListener(createBtn, 'click', () => {
                    if (nameInput) nameInput.value = '';
                    if (ignoredInput) ignoredInput.value = '';
                    if (lockedCheckbox) lockedCheckbox.checked = false;
                    if (createModal) createModal.classList.remove('hidden');
                });
            }

            if (createCancel) {
                addTrackedEventListener(createCancel, 'click', () => {
                    if (createModal) createModal.classList.add('hidden');
                });
            }

            if (createConfirm) {
                addTrackedEventListener(createConfirm, 'click', async () => {
                    if (!isMounted || isCreatingBackup) return; // Don't proceed if unmounted or already creating

                    isCreatingBackup = true; // Set flag to prevent duplicates
                    
                    try {
                        const name = nameInput?.value?.trim() || '';
                        const ignored = ignoredInput?.value?.trim() || '';
                        const locked = lockedCheckbox?.checked || false;

                        const data = {};
                        if (name) data.name = name;
                        if (ignored) data.ignored = ignored;
                        if (locked) data.is_locked = locked;

                        await api.createBackup(data);
                        showToast('Backup creation initiated successfully', 'success');
                        if (createModal) createModal.classList.add('hidden');
                        if (isMounted) loadBackups(); // Only reload if still mounted
                    } catch (err) {
                        showToast(`Failed to create backup: ${err.message}`, 'error');
                    } finally {
                        isCreatingBackup = false; // Always reset flag
                    }
                });
            }

            // Confirm modal handlers
            if (confirmCancel) {
                addTrackedEventListener(confirmCancel, 'click', hideConfirmModal);
            }

            if (confirmOk) {
                addTrackedEventListener(confirmOk, 'click', async () => {
                    if (currentBackupAction && isMounted) {
                        await currentBackupAction();
                        currentBackupAction = null;
                    }
                    hideConfirmModal();
                });
            }

            function showConfirmModal(message, action) {
                if (!isMounted) return;
                if (confirmMessage) confirmMessage.textContent = message;
                currentBackupAction = action;
                if (confirmModal) confirmModal.classList.remove('hidden');
            }

            function hideConfirmModal() {
                if (confirmModal) confirmModal.classList.add('hidden');
                currentBackupAction = null;
            }

            // Make functions available for backup cards
            window.backupsShowConfirm = showConfirmModal;
            window.backupsApi = api;
            window.backupsLoadBackups = loadBackups;

            // Set mounted flag before initial load so loadBackups doesn't return early
            isMounted = true;
            
            // Initial load
            await loadBackups();
            
            console.log('Backups mounted successfully');
        } catch (error) {
            console.error('Error mounting backups:', error);
            showToast('Error initializing backups: ' + error.message, 'error');
            // Reset mounted state on critical failure
            isMounted = false;
        } finally {
            mounting = false; // Always clear mounting flag
        }
    }

    function unmountBackups() {
        if (!isMounted) {
            console.log('Unmount backups called but not mounted');
            return;
        }

        console.log('Unmounting backups...');
        isMounted = false;
        mounting = false; // Reset mounting flag

        try {
            // Clean up all event listeners
            eventListeners.forEach(({ element, type, listener }) => {
                try {
                    if (element && typeof element.removeEventListener === 'function') {
                        element.removeEventListener(type, listener);
                    }
                } catch (e) {
                    console.warn('Error removing event listener during unmount:', e);
                }
            });
            eventListeners = [];

            // Clear container content
            const container = document.getElementById('backups-container');
            if (container) {
                container.innerHTML = '';
                container.removeAttribute('data-backups-initialized');
            }

            // Hide modals
            const modals = ['create-backup-modal', 'confirm-modal'];
            modals.forEach(modalId => {
                try {
                    const modal = document.getElementById(modalId);
                    if (modal) modal.classList.add('hidden');
                } catch (e) {
                    console.warn(`Error hiding modal ${modalId}:`, e);
                }
            });

            // Clear global references
            if (window.backupsShowConfirm) delete window.backupsShowConfirm;
            if (window.backupsApi) delete window.backupsApi;
            if (window.backupsLoadBackups) delete window.backupsLoadBackups;

            console.log('Backups unmounted successfully');
        } catch (error) {
            console.error('Error during backups unmount:', error);
        }
    }

    // Cleanup function to prevent memory leaks and multiple intervals
    function cleanup() {
        console.log('Cleaning up backups resources');

        // Clean up watch interval
        if (backupsWatchInterval) {
            clearInterval(backupsWatchInterval);
            backupsWatchInterval = null;
        }

        // Unmount if mounted
        if (isMounted) {
            unmountBackups();
        }
    }

    // Clean up on page unload/navigation
    window.addEventListener('beforeunload', cleanup);
    window.addEventListener('pagehide', cleanup);

    // Also clean up if the script runs multiple times (e.g., hot reload during development)
    if (window.backupsCleanup) {
        window.backupsCleanup();
    }
    window.backupsCleanup = cleanup;

    // Clean up any existing interval (in case script runs multiple times)
    if (window.backupsWatchInterval) {
        clearInterval(window.backupsWatchInterval);
        window.backupsWatchInterval = null;
    }

    // Check for and clean up any existing elements before initial mount
    let attempts = 0;
    const maxAttempts = 3;

    while (attempts < maxAttempts && elementsAlreadyInitialized()) {
        console.log(`Detected existing backups elements on page load (attempt ${attempts + 1}), cleaning up...`);
        cleanupExistingElements(true); // Force cleanup
        attempts++;
        // Small delay to ensure cleanup completes
        await new Promise(resolve => setTimeout(resolve, 100 + (attempts * 50)));
    }

    if (attempts >= maxAttempts) {
        console.warn('Max cleanup attempts reached for backups, forcing fresh start...');
        // Last resort: clear container
        const container = document.getElementById('backups-container');
        if (container) {
            container.innerHTML = '';
            container.removeAttribute('data-backups-initialized');
        }
    }

    // Initial mount with error handling
    try {
        const backupsSection = document.getElementById('backups-section');
        if (backupsSection) {
            await mountBackups();
        }
    } catch (error) {
        console.error('Failed to mount backups:', error);
    }

    // Watch for backups section and mount/unmount as needed
    backupsWatchInterval = setInterval(() => {
        const now = Date.now();
        // Debounce: only check every 500ms minimum
        if (now - lastBackupsCheck < 500) return;
        lastBackupsCheck = now;

        const backupsSection = document.getElementById('backups-section');
        const exists = Boolean(backupsSection);

        if (!exists && isMounted && !mounting) {
            console.log('Backups section not found, unmounting backups');
            unmountBackups();
        }
        
        if (exists && !isMounted && !mounting) {
            // Always check for stale elements when remounting
            if (elementsAlreadyInitialized()) {
                console.log('Found stale backups elements, performing cleanup...');
                cleanupExistingElements(true); // Force cleanup
                
                // Additional delay after cleanup
                setTimeout(() => {
                    if (!isMounted && !mounting && document.getElementById('backups-section')) {
                        console.log('Attempting backups mount after cleanup...');
                        mountBackups().catch(error => {
                            console.error('Failed to remount backups after cleanup:', error);
                            mounting = false;
                        });
                    }
                }, 200);
                return;
            }

            console.log('Backups section found, mounting backups');
            mountBackups().catch(error => {
                console.error('Failed to mount backups:', error);
                mounting = false; // Reset flag on error
            });
        }
    }, 2000); // Check every 2 seconds
})();
</script>
@endscript
