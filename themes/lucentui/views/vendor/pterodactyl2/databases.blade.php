<!-- Database Management View -->

<section id="databases-section"
    class="relative flex flex-col h-full w-full
           p-6 rounded-xl mx-auto
           overflow-hidden">

    <!-- spinner overlay while loading databases -->
    <div id="databases-spinner"
         class="absolute inset-0 flex items-center justify-center bg-gray-900/25 z-20 hidden">
        <div class="w-12 h-12 border-4 border-t-blue-500 border-gray-600 rounded-full animate-spin"></div>
    </div>

    <!-- header with title and create button -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-semibold text-base">Databases</h2>
        <div class="flex items-center gap-4">
            <button id="create-db-btn"
                    class="px-4 py-2 bg-green-600 font-bold text-white rounded-xl hover:bg-green-700 transition-colors"
                    @disabled($server['feature_limits']['databases'] <= 0)>
                Create Database
            </button>
            <span class="text-sm text-base/50">Limit: {{ $server['feature_limits']['databases'] }}</span>
        </div>
    </div>

    <!-- database list container -->
    <div id="databases-container" class="flex-1 space-y-4 overflow-auto">
        <!-- databases will be dynamically inserted here -->
    </div>

</section>

<!-- Create Database Modal -->
<div id="create-db-modal" class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-black/40 hidden z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm mx-4 relative">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">New Database</h3>
            <button id="create-db-close" class="text-gray-500 dark:text-base/50 hover:text-red-500 text-2xl leading-none">&times;</button>
        </div>
        <div class="space-y-4">
            <input id="create-db-input" type="text" placeholder="Database name…"
                   class="w-full px-3 py-2 bg-gray-100 dark:bg-background-secondary/50 rounded border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <input id="create-db-remote" type="text" placeholder="Allowed hosts (e.g. %)…"
                   class="w-full px-3 py-2 bg-gray-100 dark:bg-background-secondary/50 rounded border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                   value="%" />
        </div>
        <div class="flex justify-end gap-2 pt-4">
            <button id="create-db-cancel" class="px-4 py-2 bg-gray-200 dark:bg-background-secondary/50 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
            <button id="create-db-confirm" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Create</button>
        </div>
    </div>
</div>

<!-- Toast container -->
<div id="toast-container" class="fixed top-4 right-4 space-y-2 z-50 pointer-events-none"></div>

<!-- Password Display Modal -->
<div id="password-modal" class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-black/40 hidden z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md mx-auto relative">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">New Database Password</h3>
            <button id="password-modal-close" class="text-gray-500 dark:text-base/50 hover:text-red-500 text-2xl leading-none">&times;</button>
        </div>
        <input id="password-modal-input" type="text" readonly
               class="w-full px-3 py-2 mb-4 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded font-mono text-sm border border-gray-300 dark:border-gray-600" />
        <div class="flex justify-end gap-2">
            <button id="password-modal-copy" class="px-4 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Copy</button>
            <button id="password-modal-ok" class="px-4 py-1 bg-gray-600 text-white rounded hover:bg-background-secondary/50">OK</button>
        </div>
    </div>
</div>

<!-- Confirm Action Modal -->
<div id="confirm-modal" class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-black/40 hidden z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm mx-4">
        <p id="confirm-message" class="text-gray-900 dark:text-gray-100 mb-4">Are you sure?</p>
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
        // Determine colors and icons
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

        // Fade out and remove after 3.5s
        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 500);
        }, 3500);
    }

    // --- API Client ---
    class ApiClient {
        constructor(serviceId) {
            this.baseUrl = `/services/${serviceId}/databases`;
        }

        async request(endpoint = '', method = 'GET', body = null) {
            const spinner = document.getElementById('databases-spinner');
            if (spinner) spinner.classList.remove('hidden');

            try {
                const headers = {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                };

                const resp = await fetch(this.baseUrl + endpoint, {
                    method,
                    headers,
                    body: body ? JSON.stringify(body) : null,
                });

                if (!resp.ok) {
                    throw new Error(`HTTP ${resp.status}`);
                }
                return await resp.json();
            } finally {
                if (spinner) spinner.classList.add('hidden');
            }
        }

        listDatabases() {
            return this.request();
        }

        createDatabase(data) {
            return this.request('', 'POST', data);
        }

        deleteDatabase(id) {
            return this.request(`/${id}`, 'DELETE');
        }

        rotateDatabasePassword(id) {
            return this.request(`/${id}/rotate-password`, 'POST');
        }
    }

    // --- Mount / Unmount System ---
    let isMounted = false;
    let mounting = false; // Flag to prevent concurrent mounting
    let lastDatabasesCheck = 0; // Debounce rapid checks
    let databasesWatchInterval = null; // Store interval reference for cleanup
    let eventListeners = []; // Track event listeners for cleanup

    // Function to check if elements are already initialized
    function elementsAlreadyInitialized() {
        const databasesContainer = document.getElementById('databases-container');
        const createBtn = document.getElementById('create-db-btn');
        
        // Check for signs that elements are already initialized
        const hasInitializedContent = databasesContainer && (
            databasesContainer.hasAttribute('data-databases-initialized') ||
            databasesContainer.children.length > 0
        );

        const hasActiveListeners = createBtn && (
            createBtn.hasAttribute('data-databases-listeners') ||
            (createBtn._databasesEventListeners && createBtn._databasesEventListeners.length > 0)
        );

        return hasInitializedContent || hasActiveListeners;
    }

    // Function to cleanup existing elements before reinitializing
    function cleanupExistingElements(force = false) {
        console.log('Cleaning up existing databases elements...', force ? '(forced)' : '');

        // Clean up containers
        const containers = ['databases-container'];
        containers.forEach(containerId => {
            const container = document.getElementById(containerId);
            if (container) {
                try {
                    container.innerHTML = '';
                    container.removeAttribute('data-databases-initialized');
                } catch (e) {
                    console.warn(`Error cleaning container ${containerId}:`, e);
                }
            }
        });

        // Clean up event listeners from tracked elements
        const trackedElements = ['create-db-btn', 'create-db-close', 'create-db-cancel', 
                                 'create-db-confirm', 'password-modal-close', 'password-modal-ok', 'password-modal-copy'];
        trackedElements.forEach(elementId => {
            try {
                const element = document.getElementById(elementId);
                if (element) {
                    // Clone element to remove ALL event listeners
                    const newElement = element.cloneNode(true);
                    if (element.parentNode) {
                        element.parentNode.replaceChild(newElement, element);
                    }
                    newElement.removeAttribute('data-databases-listeners');
                    newElement._databasesEventListeners = [];
                }
            } catch (e) {
                console.warn(`Error cleaning element ${elementId}:`, e);
            }
        });

        // Clean up modals
        const modals = ['create-db-modal', 'password-modal'];
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
        if (!element._databasesEventListeners) {
            element._databasesEventListeners = [];
        }
        element._databasesEventListeners.push({ type, listener });
        element.setAttribute('data-databases-listeners', 'true');
    }

    async function mountDatabases() {
        if (isMounted || mounting) {
            console.log('Mount databases called but already mounted/mounting:', { isMounted, mounting });
            return; // Prevent double mounting
        }

        // Check if elements are already initialized and clean them up
        if (elementsAlreadyInitialized()) {
            console.log('Databases elements already initialized, cleaning up before mounting...');
            cleanupExistingElements();
            // Small delay to ensure cleanup completes
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        console.log('Starting databases mount...');
        mounting = true; // Set mounting flag

        try {
            const api = new ApiClient({{ $service->id }});
            const container = document.getElementById('databases-container');
            const createBtn = document.getElementById('create-db-btn');
            const modal = document.getElementById('create-db-modal');
            const closeBtn = document.getElementById('create-db-close');
            const cancelBtn = document.getElementById('create-db-cancel');
            const confirmBtn = document.getElementById('create-db-confirm');
            const inputEl = document.getElementById('create-db-input');
            const remoteEl = document.getElementById('create-db-remote');
            const passwordModal = document.getElementById('password-modal');
            const passwordCloseBtn = document.getElementById('password-modal-close');
            const passwordOkBtn = document.getElementById('password-modal-ok');
            const passwordCopyBtn = document.getElementById('password-modal-copy');
            const passwordInputEl = document.getElementById('password-modal-input');
            const confirmModal = document.getElementById('confirm-modal');
            const confirmMessageEl = document.getElementById('confirm-message');
            const confirmCancelBtn = document.getElementById('confirm-cancel');
            const confirmOkBtn = document.getElementById('confirm-ok');

            // Verify required elements exist
            if (!container) {
                throw new Error('Databases container not found');
            }

            function closeConfirmModal() {
                if (confirmModal) confirmModal.classList.add('hidden');
            }

            // Setup event listeners with tracking
            if (createBtn) {
                addTrackedEventListener(createBtn, 'click', () => {
                    if (modal) modal.classList.remove('hidden');
                });
            }

            if (closeBtn) {
                addTrackedEventListener(closeBtn, 'click', () => {
                    if (modal) modal.classList.add('hidden');
                });
            }

            if (cancelBtn) {
                addTrackedEventListener(cancelBtn, 'click', () => {
                    if (modal) modal.classList.add('hidden');
                });
            }

            if (passwordCloseBtn) {
                addTrackedEventListener(passwordCloseBtn, 'click', () => {
                    if (passwordModal) passwordModal.classList.add('hidden');
                });
            }

            if (passwordOkBtn) {
                addTrackedEventListener(passwordOkBtn, 'click', () => {
                    if (passwordModal) passwordModal.classList.add('hidden');
                });
            }

            if (passwordCopyBtn) {
                addTrackedEventListener(passwordCopyBtn, 'click', () => {
                    if (passwordInputEl) {
                        passwordInputEl.select();
                        document.execCommand('copy');
                        showToast('Password copied to clipboard.', 'success');
                    }
                });
            }

            if (confirmCancelBtn) {
                addTrackedEventListener(confirmCancelBtn, 'click', closeConfirmModal);
            }

            if (confirmBtn) {
                addTrackedEventListener(confirmBtn, 'click', async () => {
                    if (!isMounted) return;

                    const name = inputEl?.value?.trim();
                    const remote = remoteEl?.value?.trim() || '%';
                    
                    if (!name) {
                        showToast('Please enter a database name.', 'error');
                        return;
                    }
                    if (!remote) {
                        showToast('Please enter a remote host pattern.', 'error');
                        return;
                    }
                    
                    try {
                        // Create database and get response
                        const resp = await api.createDatabase({ database: name, remote });
                        // Extract new password from response
                        const newPassword = resp.attributes.relationships.password.attributes.password;
                        // Hide create modal and reset fields
                        if (modal) modal.classList.add('hidden');
                        if (inputEl) inputEl.value = '';
                        if (remoteEl) remoteEl.value = '%';
                        // Show password modal
                        if (passwordInputEl) passwordInputEl.value = newPassword;
                        if (passwordModal) passwordModal.classList.remove('hidden');
                        showToast('Database created successfully.', 'success');
                        if (isMounted) loadDatabases();
                    } catch (err) {
                        showToast('Failed to create database.', 'error');
                    }
                });
            }

            async function loadDatabases() {
                if (!container || !isMounted) return;

                try {
                    const resp = await api.listDatabases();
                    if (!isMounted) return; // Check if still mounted

                    // Debug: Log the response structure
                    console.log('Database API Response:', resp);
                    if (resp.data && resp.data.length > 0) {
                        console.log('First database object:', resp.data[0]);
                    }

                    // Mark container as initialized
                    container.setAttribute('data-databases-initialized', 'true');

                    if (!resp.data.length) {
                        container.innerHTML = `
                            <div class="text-center py-8 text-base/50">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                </svg>
                                <h3 class="text-lg font-bold mb-2">No databases found</h3>
                                <p class="text-sm">Click "Create Database" to create your first database</p>
                            </div>
                        `;
                        return;
                    }

                    container.innerHTML = resp.data.map(db => {
                        const attr = db.attributes;
                        // Try multiple possible locations for the database ID
                        const dbId = db.id || attr.id || db.attributes?.id || null;
                        
                        if (!dbId) {
                            console.error('Database ID not found in object:', db);
                        }
                        
                        return `
                            <div class="bg-background-secondary/50 rounded-xl p-4 border border-neutral">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-lg font-semibold text-white">${attr.name}</h3>
                                    <div class="flex gap-2">
                                        <button class="rotate-password-btn px-3 py-1 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700 transition-colors"
                                                data-id="${dbId}">
                                            Rotate Password
                                        </button>
                                        <button class="delete-btn px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors"
                                                data-id="${dbId}">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base/50 w-20">Username:</span>
                                            <input type="text" value="${attr.username}" readonly class="bg-background-secondary/50 text-white px-2 py-1 rounded text-xs flex-1">
                                            <button class="copy-btn px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Copy</button>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-base/50 w-20">Host:</span>
                                            <input type="text" value="${attr.host.address}:${attr.host.port}" readonly class="bg-background-secondary/50 text-white px-2 py-1 rounded text-xs flex-1">
                                            <button class="copy-btn px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Copy</button>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div><span class="text-base/50">Remote:</span> <span class="text-white">${attr.connections_from}</span></div>
                                        <div><span class="text-base/50">Max Connections:</span> <span class="text-white">${attr.max_connections}</span></div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');

                    // Add event listeners for new buttons
                    container.querySelectorAll('.rotate-password-btn').forEach(btn => {
                        addTrackedEventListener(btn, 'click', async () => {
                            if (!isMounted) return;
                            
                            const id = btn.dataset.id;
                            if (!id || id === 'undefined' || id === 'null') {
                                showToast('Invalid database ID. Please refresh and try again.', 'error');
                                return;
                            }
                            
                            try {
                                const resp = await api.rotateDatabasePassword(id);
                                
                                // Debug: Log the response structure for password rotation
                                console.log('Password rotation API Response:', resp);
                                
                                // Try multiple possible locations for the password
                                let newPassword = null;
                                if (resp.attributes?.relationships?.password?.attributes?.password) {
                                    newPassword = resp.attributes.relationships.password.attributes.password;
                                } else if (resp.attributes?.password) {
                                    newPassword = resp.attributes.password;
                                } else if (resp.password) {
                                    newPassword = resp.password;
                                } else if (resp.data?.attributes?.password) {
                                    newPassword = resp.data.attributes.password;
                                } else if (resp.data?.attributes?.relationships?.password?.attributes?.password) {
                                    newPassword = resp.data.attributes.relationships.password.attributes.password;
                                }
                                
                                if (!newPassword) {
                                    console.error('Password not found in rotation response:', resp);
                                    showToast('Password rotated but could not retrieve new password. Please check manually.', 'error');
                                    return;
                                }
                                
                                if (passwordInputEl) passwordInputEl.value = newPassword;
                                if (passwordModal) passwordModal.classList.remove('hidden');
                                showToast('Password rotated successfully.', 'success');
                            } catch (err) {
                                console.error('Password rotation error:', err);
                                showToast('Failed to rotate password.', 'error');
                            }
                        });
                    });

                    container.querySelectorAll('.delete-btn').forEach(btn => {
                        addTrackedEventListener(btn, 'click', () => {
                            if (!isMounted) return;
                            
                            const id = btn.dataset.id;
                            if (!id || id === 'undefined' || id === 'null') {
                                showToast('Invalid database ID. Please refresh and try again.', 'error');
                                return;
                            }
                            
                            if (confirmMessageEl) confirmMessageEl.textContent = 'Are you sure you want to delete this database? This action cannot be undone.';
                            
                            // Clear any existing click handler and add new one
                            if (confirmOkBtn) {
                                const newConfirmBtn = confirmOkBtn.cloneNode(true);
                                confirmOkBtn.parentNode.replaceChild(newConfirmBtn, confirmOkBtn);
                                
                                addTrackedEventListener(newConfirmBtn, 'click', async () => {
                                    if (confirmModal) confirmModal.classList.add('hidden');
                                    try {
                                        await api.deleteDatabase(id);
                                        showToast('Database deleted.', 'success');
                                        if (isMounted) loadDatabases();
                                    } catch {
                                        showToast('Failed to delete database.', 'error');
                                    }
                                });
                            }
                            
                            if (confirmModal) confirmModal.classList.remove('hidden');
                        });
                    });

                    // Copy button actions for user and host fields
                    container.querySelectorAll('.copy-btn').forEach(btn => {
                        addTrackedEventListener(btn, 'click', () => {
                            const input = btn.previousElementSibling;
                            if (input) {
                                input.select();
                                document.execCommand('copy');
                                showToast('Copied to clipboard', 'success');
                            }
                        });
                    });
                } catch (err) {
                    if (!isMounted) return; // Don't show errors if unmounted
                    
                    console.error('Failed to load databases:', err);
                    showToast('Failed to load databases.', 'error');
                    container.innerHTML = `
                        <div class="text-center py-8 text-base/50">
                            <p>Failed to load databases</p>
                            <button class="retry-btn mt-2 text-blue-400 hover:text-blue-300">Retry</button>
                        </div>
                    `;
                    
                    const retryBtn = container.querySelector('.retry-btn');
                    if (retryBtn) {
                        addTrackedEventListener(retryBtn, 'click', loadDatabases);
                    }
                }
            }

        // Set mounted flag before initial load so loadDatabases doesn't return early
        isMounted = true;
        
        await loadDatabases();
        
        console.log('Databases mounted successfully');
    } catch (error) {
        console.error('Error mounting databases:', error);
        showToast('Error initializing databases: ' + error.message, 'error');
        // Reset mounted state on critical failure
        isMounted = false;
    } finally {
        mounting = false; // Always clear mounting flag
    }
}

function unmountDatabases() {
    if (!isMounted) {
        console.log('Unmount databases called but not mounted');
        return;
    }

    console.log('Unmounting databases...');
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
        const container = document.getElementById('databases-container');
        if (container) {
            container.innerHTML = '';
            container.removeAttribute('data-databases-initialized');
        }

        // Hide modals
        const modals = ['create-db-modal', 'password-modal'];
        modals.forEach(modalId => {
            try {
                const modal = document.getElementById(modalId);
                if (modal) modal.classList.add('hidden');
            } catch (e) {
                console.warn(`Error hiding modal ${modalId}:`, e);
            }
        });

        console.log('Databases unmounted successfully');
    } catch (error) {
        console.error('Error during databases unmount:', error);
    }
}

// Cleanup function to prevent memory leaks and multiple intervals
function cleanup() {
    console.log('Cleaning up databases resources');

    // Clean up watch interval
    if (databasesWatchInterval) {
        clearInterval(databasesWatchInterval);
        databasesWatchInterval = null;
    }

    // Unmount if mounted
    if (isMounted) {
        unmountDatabases();
    }
}

// Clean up on page unload/navigation
window.addEventListener('beforeunload', cleanup);
window.addEventListener('pagehide', cleanup);

// Also clean up if the script runs multiple times (e.g., hot reload during development)
if (window.databasesCleanup) {
    window.databasesCleanup();
}
window.databasesCleanup = cleanup;

// Clean up any existing interval (in case script runs multiple times)
if (window.databasesWatchInterval) {
    clearInterval(window.databasesWatchInterval);
    window.databasesWatchInterval = null;
}

// Check for and clean up any existing elements before initial mount
let attempts = 0;
const maxAttempts = 3;

while (attempts < maxAttempts && elementsAlreadyInitialized()) {
    console.log(`Detected existing databases elements on page load (attempt ${attempts + 1}), cleaning up...`);
    cleanupExistingElements(true); // Force cleanup
    attempts++;
    // Small delay to ensure cleanup completes
    await new Promise(resolve => setTimeout(resolve, 100 + (attempts * 50)));
}

if (attempts >= maxAttempts) {
    console.warn('Max cleanup attempts reached for databases, forcing fresh start...');
    // Last resort: clear container
    const container = document.getElementById('databases-container');
    if (container) {
        container.innerHTML = '';
        container.removeAttribute('data-databases-initialized');
    }
}

// Initial mount with error handling
try {
    const databasesSection = document.getElementById('databases-section');
    if (databasesSection) {
        await mountDatabases();
    }
} catch (error) {
    console.error('Failed to mount databases:', error);
}

// Watch for databases section and mount/unmount as needed
databasesWatchInterval = setInterval(() => {
    const now = Date.now();
    // Debounce: only check every 500ms minimum
    if (now - lastDatabasesCheck < 500) return;
    lastDatabasesCheck = now;

    const databasesSection = document.getElementById('databases-section');
    const exists = Boolean(databasesSection);

    if (!exists && isMounted && !mounting) {
        console.log('Databases section not found, unmounting databases');
        unmountDatabases();
    }
    
    if (exists && !isMounted && !mounting) {
        // Always check for stale elements when remounting
        if (elementsAlreadyInitialized()) {
            console.log('Found stale databases elements, performing cleanup...');
            cleanupExistingElements(true); // Force cleanup
            
            // Additional delay after cleanup
            setTimeout(() => {
                if (!isMounted && !mounting && document.getElementById('databases-section')) {
                    console.log('Attempting databases mount after cleanup...');
                    mountDatabases().catch(error => {
                        console.error('Failed to remount databases after cleanup:', error);
                        mounting = false;
                    });
                }
            }, 200);
            return;
        }

        console.log('Databases section found, mounting databases');
        mountDatabases().catch(error => {
            console.error('Failed to mount databases:', error);
            mounting = false; // Reset flag on error
        });
    }
}, 500); // Check every 2 seconds
})();
</script>
@endscript