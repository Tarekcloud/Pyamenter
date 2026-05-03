<!-- Network Allocation Management View -->

<section id="network-section"
    class="relative flex flex-col h-full w-full
           p-6 rounded-xl mx-auto
           overflow-hidden">

    <!-- spinner overlay while loading allocations -->
    <div id="network-spinner"
         class="absolute inset-0 flex items-center justify-center bg-gray-900/25 z-20 hidden">
        <div class="w-12 h-12 border-4 border-t-blue-500 border-gray-600 rounded-full animate-spin"></div>
    </div>    <!-- header with title and create button -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-semibold text-base">Network Allocations</h2>
        <div class="flex items-center gap-4">
            @if($settings['auto_allocation'] ?? false)
                <button id="create-allocation-btn"
                        class="px-4 py-2 bg-green-600 text-base rounded-xl hover:bg-green-700 transition-colors">
                    Request Allocation
                </button>
            @else
                <span class="text-sm text-gray-500 italic">Auto allocation disabled</span>
            @endif
            <span class="text-sm text-base/50">Limit: {{ $server['feature_limits']['allocations'] ?? 'Unlimited' }}</span>
        </div>
    </div>

    <!-- allocation list container -->
    <div id="allocations-container" class="flex-1 space-y-4 overflow-auto">
        <!-- allocations will be dynamically inserted here -->
    </div>

</section>

<!-- Toast container -->
<div id="toast-container" class="fixed top-4 right-4 space-y-2 z-50 pointer-events-none"></div>

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
            this.baseUrl = `/services/${serviceId}/network`;
        }

        async request(endpoint = '', method = 'GET', body = null) {
            const spinner = document.getElementById('network-spinner');
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

        listAllocations() {
            return this.request('/allocations');
        }

        createAllocation() {
            return this.request('/allocations', 'POST');
        }

        setPrimaryAllocation(id) {
            return this.request(`/allocations/${id}/primary`, 'POST');
        }

        deleteAllocation(id) {
            return this.request(`/allocations/${id}`, 'DELETE');
        }
    }

    // --- Mount / Unmount System with Event Listener Tracking ---
    let isMounted = false;
    let mounting = false;
    let eventListeners = [];
    let networkWatchInterval = null;
    let lastNetworkCheck = 0;

    // Helper function to add tracked event listeners
    function addTrackedEventListener(element, type, listener) {
        if (!element) return;
        
        element.addEventListener(type, listener);
        eventListeners.push({ element, type, listener });
    }

    // Function to check if elements are already initialized
    function elementsAlreadyInitialized() {
        const container = document.getElementById('allocations-container');
        return container && container.hasAttribute('data-network-initialized');
    }

    // Function to cleanup existing elements
    function cleanupExistingElements() {
        console.log('Cleaning up existing network elements...');

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
        const container = document.getElementById('allocations-container');
        if (container) {
            container.innerHTML = '';
            container.removeAttribute('data-network-initialized');
        }

        // Hide modals
        const confirmModal = document.getElementById('confirm-modal');
        if (confirmModal) confirmModal.classList.add('hidden');
    }

    async function mountNetwork() {
        if (isMounted || mounting) {
            console.log('Mount network called but already mounted/mounting:', { isMounted, mounting });
            return;
        }

        // Check if elements are already initialized and clean them up
        if (elementsAlreadyInitialized()) {
            console.log('Network elements already initialized, cleaning up before mounting...');
            cleanupExistingElements();
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        console.log('Starting network mount...');
        mounting = true;

        try {
            const api = new ApiClient({{ $service->id }});
            const container = document.getElementById('allocations-container');
            const createBtn = document.getElementById('create-allocation-btn');
            const confirmModal = document.getElementById('confirm-modal');
            const confirmMessage = document.getElementById('confirm-message');
            const confirmCancel = document.getElementById('confirm-cancel');
            const confirmOk = document.getElementById('confirm-ok');
            
            if (!container) {
                throw new Error('Required network elements not found');
            }

            // Mark container as initialized
            container.setAttribute('data-network-initialized', 'true');
            
            let currentAllocationAction = null;

            // Load and display allocations
            async function loadAllocations() {
                const container = document.getElementById('allocations-container');
                if (!container) return;

                try {
                    const response = await api.listAllocations();
                    displayAllocations(response.data || []);
                } catch (err) {
                    showToast(`Failed to load allocations: ${err.message}`, 'error');
                    container.innerHTML = `
                        <div class="text-center py-8 text-base/50">
                            <p>Failed to load network allocations</p>
                            <button class="retry-btn mt-2 text-blue-400 hover:text-blue-300">Retry</button>
                        </div>
                    `;
                    
                    // Add retry functionality
                    const retryBtn = container.querySelector('.retry-btn');
                    if (retryBtn) {
                        addTrackedEventListener(retryBtn, 'click', loadAllocations);
                    }
                }
            }

            function displayAllocations(allocations) {
                const container = document.getElementById('allocations-container');
                if (!container) return;
                
                if (!allocations.length) {
                    const autoAllocationEnabled = {{ $settings['auto_allocation'] ?? 'false' ? 'true' : 'false' }};
                    const message = autoAllocationEnabled 
                        ? '<p class="text-sm">Click "Request Allocation" to add one</p>'
                        : '<p class="text-sm">Auto allocation is disabled. Contact your administrator to request allocations.</p>';
                    
                    container.innerHTML = `
                        <div class="text-center py-8 text-base/50">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                            <p>No network allocations found</p>
                            ${message}
                        </div>
                    `;
                    return;
                }

                container.innerHTML = allocations
                    .map(allocation => createAllocationCard(allocation))
                    .join('');

                attachAllocationEventListeners();
            }

            function createAllocationCard(allocation) {
                const attr = allocation.attributes;
                const isPrimary = attr.is_default;
                
                return `
                    <div class="bg-background-secondary/50 border border-neutral rounded-xl p-4 ${isPrimary ? 'border-3 border-primary' : ''}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <div class="text-lg font-mono text-base">
                                        ${attr.ip}:${attr.port}
                                    </div>
                                    <div class="flex gap-2">
                                        ${isPrimary ? '<span class="px-2 py-1 bg-blue-600 text-white text-xs rounded">Primary</span>' : ''}
                                    </div>
                                </div>
                                <div class="text-sm text-base/75 mt-1">
                                    <strong>Alias:</strong> ${attr.alias || 'None'}
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                ${!isPrimary ? `
                                    <button class="primary-btn px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700" 
                                            data-id="${attr.id}">
                                        Set Primary
                                    </button>
                                ` : ''}
                                ${!isPrimary ? `
                                    <button class="delete-btn px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700" 
                                            data-id="${attr.id}">
                                        Delete
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }

            function attachAllocationEventListeners() {
                document.querySelectorAll('.primary-btn').forEach(btn => {
                    addTrackedEventListener(btn, 'click', () => {
                        const id = btn.dataset.id;
                        showConfirmModal(
                            'Are you sure you want to set this as the primary allocation?',
                            async () => {
                                try {
                                    await api.setPrimaryAllocation(id);
                                    showToast('Primary allocation updated successfully', 'success');
                                    loadAllocations();
                                } catch (err) {
                                    showToast(`Failed to set primary allocation: ${err.message}`, 'error');
                                }
                            }
                        );
                    });
                });

                document.querySelectorAll('.delete-btn').forEach(btn => {
                    addTrackedEventListener(btn, 'click', () => {
                        const id = btn.dataset.id;
                        showConfirmModal(
                            'Are you sure you want to delete this allocation? This action cannot be undone.',
                            async () => {
                                try {
                                    await api.deleteAllocation(id);
                                    showToast('Allocation deleted successfully', 'success');
                                    loadAllocations();
                                } catch (err) {
                                    showToast(`Failed to delete allocation: ${err.message}`, 'error');
                                }
                            }
                        );
                    });
                });
            }

            // Modal event handlers with tracking
            if (confirmCancel) {
                addTrackedEventListener(confirmCancel, 'click', hideConfirmModal);
            }
            
            if (confirmOk) {
                addTrackedEventListener(confirmOk, 'click', async () => {
                    if (currentAllocationAction) {
                        await currentAllocationAction();
                        currentAllocationAction = null;
                    }
                    hideConfirmModal();
                });
            }

            function showConfirmModal(message, action) {
                if (confirmMessage) confirmMessage.textContent = message;
                currentAllocationAction = action;
                if (confirmModal) confirmModal.classList.remove('hidden');
            }

            function hideConfirmModal() {
                if (confirmModal) confirmModal.classList.add('hidden');
                currentAllocationAction = null;
            }

            // Create allocation button with tracking
            if (createBtn) {
                addTrackedEventListener(createBtn, 'click', async () => {
                    try {
                        await api.createAllocation();
                        showToast('Allocation requested successfully', 'success');
                        loadAllocations();
                    } catch (err) {
                        showToast(`Failed to request allocation: ${err.message}`, 'error');
                    }
                });
            }

            // Set mounted flag and load initial data
            isMounted = true;
            await loadAllocations();

            console.log('Network mounted successfully');
        } catch (error) {
            console.error('Failed to mount network:', error);
            showToast('Failed to initialize network manager', 'error');
        } finally {
            mounting = false;
        }
    }

    function unmountNetwork() {
        if (!isMounted) {
            console.log('Unmount network called but not mounted');
            return;
        }
        
        console.log('Unmounting network...');
        isMounted = false;

        try {
            // Clean up existing elements
            cleanupExistingElements();
            
            console.log('Network unmounted successfully');
        } catch (error) {
            console.error('Error during network unmount:', error);
        }
    }

    function detectNetwork() {
        const exists = Boolean(document.getElementById('network-section'));
        if (exists && !isMounted) mountNetwork();
        if (!exists && isMounted) unmountNetwork();
    }

    // Initialize
    detectNetwork();
    setInterval(detectNetwork, 1000);
})();
</script>
@endscript
