<section id="startup-section"
    class="relative flex flex-col h-full w-full
           p-6 rounded-xl mx-auto
           overflow-hidden">

    <!-- spinner overlay while saving startup params -->
    <div id="startup-spinner"
         class="fixed inset-0 bg-gray-900/25 z-20 hidden">
        <div class="w-12 h-12 border-4 border-t-blue-500 border-gray-600 rounded-full animate-spin"></div>
    </div>

    <h2 class="text-3xl font-semibold mb-4">Startup Parameters</h2>
    <!-- show current compiled startup command -->
    <input id="startup-command"
           type="text"
           disabled
           class="w-full mb-6 px-4 py-2 bg-gray-800/25
                  text-base rounded-xl font-mono text-lg"
           value="Loading command…" />
    <form id="startup-form" class="rounded-xl space-y-6">
        <!-- dynamic inputs will be injected here -->
        <div id="startup-fields" class="grid grid-cols-1 md:grid-cols-2 gap-6"></div>
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Save Changes
            </button>
        </div>
    </form>
</section>
<!-- toast container (moved outside of section to always exist) -->
<div id="toast-container" class="fixed top-4 right-4 space-y-2 z-50 pointer-events-none"></div>

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
        }, 3000);
    }

    // --- API Client ---
    class ApiClient {
        constructor(serviceId) {
            this.serviceId = serviceId;
            this.baseUrl = `/services/${serviceId}/startup`;
        }

        async request(endpoint = '', method = 'GET', body = null) {
            const spinner = document.getElementById('startup-spinner');
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
                    method, headers, body
                });

                if (!resp.ok) {
                    const error = await resp.json();
                    throw new Error(error.error || `HTTP ${resp.status}`);
                }
                return await resp.json();
            } finally {
                if (spinner) spinner.classList.add('hidden');
            }
        }

        listStartup() {
            return this.request();
        }

        updateStartup(param) {
            // param = { key, value }
            return this.request('', 'POST', JSON.stringify(param));
        }
    }

    // --- Mount / Unmount System with Event Listener Tracking ---
    let isMounted = false;
    let mounting = false;
    let eventListeners = [];

    // Helper function to add tracked event listeners
    function addTrackedEventListener(element, type, listener) {
        if (!element) return;
        
        element.addEventListener(type, listener);
        eventListeners.push({ element, type, listener });
    }

    // Function to check if elements are already initialized
    function elementsAlreadyInitialized() {
        const container = document.getElementById('startup-fields');
        return container && container.hasAttribute('data-startup-initialized');
    }

    // Function to cleanup existing elements
    function cleanupExistingElements() {
        console.log('Cleaning up existing startup elements...');

        // Clean up event listeners
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

        // Clean up containers
        const fieldsEl = document.getElementById('startup-fields');
        if (fieldsEl) {
            fieldsEl.innerHTML = '';
            fieldsEl.removeAttribute('data-startup-initialized');
        }

        // Reset command display
        const cmdEl = document.getElementById('startup-command');
        if (cmdEl) cmdEl.value = 'Loading command…';
    }

    async function mountStartup() {
        if (isMounted || mounting) {
            console.log('Mount startup called but already mounted/mounting:', { isMounted, mounting });
            return;
        }

        // Check if elements are already initialized and clean them up
        if (elementsAlreadyInitialized()) {
            console.log('Startup elements already initialized, cleaning up before mounting...');
            cleanupExistingElements();
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        console.log('Starting startup mount...');
        mounting = true;

        try {
            const api = new ApiClient({{ $service->id }});
            const fieldsEl = document.getElementById('startup-fields');
            const formEl = document.getElementById('startup-form');
            const spinner = document.getElementById('startup-spinner');
            
            if (!fieldsEl || !formEl) {
                throw new Error('Required startup elements not found');
            }

            // Mark container as initialized
            fieldsEl.setAttribute('data-startup-initialized', 'true');
            
            const originalValues = {};

            // Load and render form fields
            async function loadParams() {
                try {
                    const resp = await api.listStartup();
                    
                    // Display the compiled startup_command in the disabled input
                    const cmdEl = document.getElementById('startup-command');
                    if (cmdEl) {
                        cmdEl.value = resp.meta?.startup_command || '—';
                    }
                    
                    fieldsEl.innerHTML = '';
                    
                    const data = resp.data || [];
                    if (data.length === 0) {
                        fieldsEl.innerHTML = `
                            <div class="col-span-full flex flex-col items-center justify-center py-12 text-base/50">
                                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <h3 class="text-lg font-bold mb-2">No startup parameters found</h3>
                                <p class="text-sm">This server doesn't have configurable startup parameters.</p>
                            </div>
                        `;
                        return;
                    }
                    
                    data.forEach(item => {
                        const { env_variable, name, description, server_value, is_editable } = item.attributes;
                        originalValues[env_variable] = server_value;

                        const card = document.createElement('div');
                        card.className = 'flex flex-col bg-gray-900/25 p-4 rounded-xl border border-neutral';

                        const label = document.createElement('label');
                        label.htmlFor = env_variable;
                        label.className = 'text-gray-700 dark:text-base/40 font-medium mb-2';
                        label.textContent = name;

                        const input = document.createElement('input');
                        input.id = env_variable;
                        input.name = env_variable;
                        input.type = 'text';
                        input.value = server_value || '';
                        input.disabled = !is_editable;
                        input.className = `mt-1 px-3 py-2 bg-gray-100 dark:bg-gray-800/25 text-gray-900 dark:text-white rounded-xl placeholder-gray-500 dark:placeholder-gray-400 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition ${!is_editable ? 'opacity-50 cursor-not-allowed' : ''}`;

                        const hint = document.createElement('p');
                        hint.className = 'text-xs text-gray-500 mt-2 italic';
                        hint.textContent = description || 'No description available';

                        card.append(label, input, hint);
                        fieldsEl.appendChild(card);
                    });
                } catch (error) {
                    console.error('Failed to load startup parameters:', error);
                    showToast('Failed to load startup parameters', 'error');
                    fieldsEl.innerHTML = `
                        <div class="col-span-full flex flex-col items-center justify-center py-12 text-red-400">
                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium mb-2">Failed to load parameters</h3>
                            <p class="text-sm">Please try refreshing the page.</p>
                        </div>
                    `;
                }
            }

            // Handle form submission with tracking
            addTrackedEventListener(formEl, 'submit', async (e) => {
                e.preventDefault();
                if (!isMounted) return;
                
                if (spinner) spinner.classList.remove('hidden');
                
                try {
                    const inputs = Array.from(fieldsEl.querySelectorAll('input:not([disabled])'));
                    const updates = inputs
                        .filter(inp => inp.value !== originalValues[inp.name])
                        .map(inp => ({ key: inp.name, value: inp.value }));

                    if (updates.length === 0) {
                        showToast('No changes to save', 'info');
                        return;
                    }

                    let successCount = 0;
                    let errorCount = 0;

                    for (const upd of updates) {
                        try {
                            await api.updateStartup(upd);
                            originalValues[upd.key] = upd.value;
                            successCount++;
                        } catch (error) {
                            console.error('Save error for parameter:', upd.key, error);
                            showToast(`Failed to save ${upd.key}: ${error.message}`, 'error');
                            errorCount++;
                        }
                    }

                    if (successCount > 0) {
                        showToast(`Successfully updated ${successCount} parameter${successCount !== 1 ? 's' : ''}`, 'success');
                    }
                    
                    if (errorCount === 0) {
                        // Reload parameters to get updated command
                        await loadParams();
                    }
                } catch (error) {
                    console.error('Form submission error:', error);
                    showToast('Failed to save changes', 'error');
                } finally {
                    if (spinner) spinner.classList.add('hidden');
                }
            });

            // Set mounted flag and load initial data
            isMounted = true;
            await loadParams();

            console.log('Startup mounted successfully');
        } catch (error) {
            console.error('Failed to mount startup:', error);
            showToast('Failed to initialize startup parameters', 'error');
        } finally {
            mounting = false;
        }
    }

    function unmountStartup() {
        if (!isMounted) {
            console.log('Unmount startup called but not mounted');
            return;
        }
        
        console.log('Unmounting startup...');
        isMounted = false;

        try {
            // Clean up existing elements
            cleanupExistingElements();
            
            console.log('Startup unmounted successfully');
        } catch (error) {
            console.error('Error during startup unmount:', error);
        }
    }

    function detectStartup() {
        const exists = Boolean(document.getElementById('startup-section'));
        if (exists && !isMounted) {
            mountStartup();
        }
        if (!exists && isMounted) {
            unmountStartup();
        }
    }

    // Initialize
    detectStartup();
    setInterval(detectStartup, 1000);
})();
</script>
@endscript
