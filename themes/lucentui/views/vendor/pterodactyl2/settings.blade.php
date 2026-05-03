<!-- Server Settings View -->

<section id="settings-section"
    class="relative flex flex-col h-full w-full
           p-6 rounded-lg mx-auto
           overflow-hidden">

    <!-- spinner overlay while loading settings -->
    <div id="settings-spinner"
         class="absolute inset-0 flex items-center justify-center bg-gray-900/25 z-20 hidden">
        <div class="w-12 h-12 border-4 border-t-blue-500 border-gray-600 rounded-full animate-spin"></div>
    </div>

    <!-- header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-semibold text-base">Server Settings</h2>
    </div>

    <!-- settings content -->
    <div id="settings-container" class="flex-1 space-y-6 overflow-auto">
        <!-- Server Information Card -->
        <div class="bg-background-secondary/50 border border-neutral rounded-lg p-6">
            <h3 class="text-lg font-semibold text-base mb-4">Server Information</h3>
            <div id="server-info" class="space-y-3">
                <!-- Server info will be dynamically inserted here -->
            </div>
        </div>

        <!-- Server Name Card -->
        <div class="bg-background-secondary/50 border border-neutral rounded-lg p-6">
            <h3 class="text-lg font-semibold text-base mb-4">Server Name</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-base/40 mb-2">Current Name</label>
                    <div class="flex items-center gap-4">
                        <input type="text" id="server-name-input" 
                               class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-background-secondary/50 text-gray-900 dark:text-white" 
                               placeholder="Server name">
                        <button id="rename-server-btn" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Rename
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-base/50 mt-1">Change your server's display name</p>
                </div>
            </div>        </div>

        <!-- SFTP Connection Card -->
        <div class="bg-background-secondary/50 border border-neutral rounded-lg p-6">
            <h3 class="text-lg font-semibold text-base mb-4">SFTP Connection Details</h3>
            <div id="sftp-info" class="space-y-3">
                <!-- SFTP info will be dynamically inserted here -->
            </div>
        </div>

        <!-- Danger Zone Card -->
        <div class="bg-red-900/20 border border-red-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-red-700 mb-4">Danger Zone</h3>
            <div class="space-y-4">
                <div>
                    <h4 class="text-base font-medium">Reinstall Server</h4>
                    <p class="text-base/70 text-sm mb-3">This will stop your server, destroy all files, and reinstall it with the default configuration.</p>
                    <button id="reinstall-server-btn" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Reinstall Server
                    </button>
                </div>
            </div>
        </div>        <!-- Docker Information Card -->
        <div class="bg-background-secondary/50 border border-neutral rounded-lg p-6">
            <h3 class="text-lg font-semibold text-base mb-4">Container & Environment</h3>
            <div id="docker-info" class="space-y-3">
                <!-- Docker info will be dynamically inserted here -->
            </div>
        </div>
    </div>

</section>

<!-- Toast container -->
<div id="toast-container" class="fixed top-4 right-4 space-y-2 z-50 pointer-events-none"></div>

<!-- Confirmation Modal -->
<div id="confirm-modal" class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-black/40 hidden z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Confirm Action</h3>
        <p id="confirm-message" class="text-gray-600 dark:text-base/40 mb-6"></p>
        <div class="flex justify-end gap-3">
            <button id="confirm-cancel" class="px-4 py-2 bg-gray-200 dark:bg-background-secondary/50 text-gray-800 dark:text-gray-100 rounded hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
            <button id="confirm-ok" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Confirm</button>
        </div>
    </div>
</div>

@script
<script>
(() => {
    class ApiClient {
        constructor(serviceId) {
            this.serviceId = serviceId;
        }

        async request(endpoint, method = 'GET', data = null) {
            const url = `/services/${this.serviceId}${endpoint}`;
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            };

            if (data && method !== 'GET') {
                options.body = JSON.stringify(data);
            }

            const response = await fetch(url, options);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || `HTTP ${response.status}`);
            }

            return result;
        }

        getSettings() {
            return this.request('/settings', 'GET');
        }

        renameServer(name) {
            return this.request('/settings/rename', 'POST', { name });
        }

        reinstallServer() {
            return this.request('/settings/reinstall', 'POST');
        }        getDockerInfo() {
            return this.request('/settings/docker', 'GET');
        }

        getSftpDetails() {
            return this.request('/settings/sftp', 'GET');
        }
    }

    // --- Mount / Unmount System ---
    let isMounted = false;

    async function mountSettings() {
        if (isMounted) return;
        isMounted = true;        const api = new ApiClient({{ $service->id }});
        const container = document.getElementById('settings-container');
        const serverInfoDiv = document.getElementById('server-info');
        const dockerInfoDiv = document.getElementById('docker-info');
        const sftpInfoDiv = document.getElementById('sftp-info');
        const serverNameInput = document.getElementById('server-name-input');
        const renameBtn = document.getElementById('rename-server-btn');
        const reinstallBtn = document.getElementById('reinstall-server-btn');
        const confirmModal = document.getElementById('confirm-modal');
        const confirmCancel = document.getElementById('confirm-cancel');
        const confirmOk = document.getElementById('confirm-ok');
        const confirmMessage = document.getElementById('confirm-message');

        let confirmCallback = null;        // Load settings
        async function loadSettings() {
            showSpinner();
            try {
                // Load settings first
                const settings = await api.getSettings();
                renderServerInfo(settings);
                
                // Set current server name
                if (serverNameInput) {
                    serverNameInput.value = settings.attributes?.name || '';
                }
                
                // Try to load Docker info separately with error handling
                try {
                    const dockerInfo = await api.getDockerInfo();
                    renderDockerInfo(dockerInfo);
                } catch (dockerErr) {
                    console.warn('Failed to load Docker info:', dockerErr);
                    renderDockerError();
                }
                
                // Try to load SFTP details separately with error handling
                try {
                    const sftpInfo = await api.getSftpDetails();
                    renderSftpInfo(sftpInfo);
                } catch (sftpErr) {
                    console.warn('Failed to load SFTP info:', sftpErr);
                    renderSftpError();
                }
                
            } catch (err) {
                showToast(`Failed to load settings: ${err.message}`, 'error');
            } finally {
                hideSpinner();
            }
        }function renderServerInfo(data) {
            if (!serverInfoDiv) return;
            
            // Check if data and attributes exist
            if (!data || !data.attributes) {
                serverInfoDiv.innerHTML = '<div class="text-red-400">Failed to load server information</div>';
                return;
            }
            
            const attr = data.attributes;
            const limits = attr.limits || {};
            const featureLimits = attr.feature_limits || {};
            
            serverInfoDiv.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-base/50">Server UUID</label>
                        <div class="text-base font-mono text-sm">${attr.uuid || 'Not available'}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-base/50">Identifier (For Support)</label>
                        <div class="text-base">${attr.identifier || 'Not available'}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-base/50">Node</label>
                        <div class="text-base">${attr.node || 'Not available'}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-base/50">Status</label>
                        <div class="font-medium text-white">
                            <span class="px-2 py-1 rounded text-xs ${
                                attr.server_owner ? 'bg-green-600' : 'bg-gray-600'
                            }">
                                ${attr.server_owner ? 'Owner' : 'Subuser'}
                            </span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-base/50">CPU Limit</label>
                        <div class="text-base">${limits.cpu ? limits.cpu + '%' : 'Unlimited'}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-base/50">Memory Limit</label>
                        <div class="text-base">${limits.memory ? formatBytes(limits.memory * 1024 * 1024) : 'Unlimited'}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-base/50">Disk Limit</label>
                        <div class="text-base">${limits.disk ? formatBytes(limits.disk * 1024 * 1024) : 'Unlimited'}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-base/50">Allocation Limit</label>
                        <div class="text-base">${featureLimits.allocations || 'Unlimited'}</div>
                    </div>
                </div>
            `;
        }        function renderDockerInfo(data) {
            if (!dockerInfoDiv) return;
            
            // Check if data and attributes exist
            if (!data || !data.attributes) {
                renderDockerError();
                return;
            }
            
            const attr = data.attributes;
            const container = attr.container || {};
            const environment = container.environment || {};
            
            dockerInfoDiv.innerHTML = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-base/50">Docker Image</label>
                        <div class="text-base font-mono text-sm">${container.image || 'Not available'}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-base/50">Installation Status</label>
                        <div class="text-white">
                            <span class="px-2 py-1 rounded text-xs ${
                                container.installed ? 'bg-green-600' : 'bg-red-600'
                            }">
                                ${container.installed ? 'Installed' : 'Not Installed'}
                            </span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-base/50">Startup Command</label>
                        <div class="text-base font-mono text-sm bg-background-secondary/70 p-2 rounded">${container.startup_command || 'Not available'}</div>
                    </div>
                    ${Object.keys(environment).length > 0 ? `
                        <div>
                            <label class="block text-sm font-medium text-base/50 mb-2">Environment Variables</label>
                            <div class="space-y-1 max-h-48 overflow-y-auto">
                                ${Object.entries(environment).map(([key, value]) => `
                                    <div class="text-sm bg-background-secondary/70 p-2 rounded">
                                        <span class="text-blue-400 font-mono">${key}:</span>
                                        <span class="text-base font-mono ml-2">${value}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-base/50">Server UUID</label>
                            <div class="text-base font-mono text-sm">${attr.uuid}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-base/50">Nest ID</label>
                            <div class="text-base">${attr.nest || 'Not available'}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-base/50">Egg ID</label>
                            <div class="text-base">${attr.egg || 'Not available'}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-base/50">Node ID</label>
                            <div class="text-base">${attr.node || 'Not available'}</div>
                        </div>
                    </div>
                </div>
            `;
        }        function renderDockerError() {
            if (!dockerInfoDiv) return;
            
            dockerInfoDiv.innerHTML = `
                <div class="text-center py-8">
                    <div class="text-base/50 mb-2">
                        <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <p class="text-base/50">Docker information not available</p>
                    <p class="text-sm text-gray-500 mt-1">This may be due to insufficient permissions</p>
                </div>
            `;
        }

        function renderSftpInfo(data) {
            if (!sftpInfoDiv) return;
            
            // Check if data exists
            if (!data || !data.data) {
                renderSftpError();
                return;
            }
            
            const sftp = data.data;
            
            sftpInfoDiv.innerHTML = `
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-base/50">Server Address</label>
                            <div class="flex items-center gap-2">
                                <code class="text-base font-mono text-sm bg-background-secondary/50 px-2 py-1 rounded">${sftp.server || 'Not available'}</code>
                                <button onclick="copyToClipboard('${sftp.server || ''}')" class="text-blue-400 hover:text-blue-300 text-xs" title="Copy to clipboard">📋</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-base/50">Port</label>
                            <div class="flex items-center gap-2">
                                <code class="text-base font-mono text-sm bg-background-secondary/50 px-2 py-1 rounded">${sftp.port || 'Not available'}</code>
                                <button onclick="copyToClipboard('${sftp.port || ''}')" class="text-blue-400 hover:text-blue-300 text-xs" title="Copy to clipboard">📋</button>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-base/50">Username</label>
                            <div class="flex items-center gap-2">
                                <code class="text-base font-mono text-sm bg-background-secondary/50 px-2 py-1 rounded flex-1">${sftp.username || 'Not available'}</code>
                                <button onclick="copyToClipboard('${sftp.username || ''}')" class="text-blue-400 hover:text-blue-300 text-xs" title="Copy to clipboard">📋</button>
                            </div>
                        </div>
                    </div>                    
                    <div class="bg-blue-800/30 border border-blue-500 rounded-lg p-4">
                        <h4 class="text-blue-500 font-medium mb-2">🔐 Authentication</h4>
                        <p class="text-blue-500 text-sm mb-2">Use your Game panel password to authenticate SFTP connections.</p>
                        <p class="text-blue-400 text-xs mb-3">Your password is the same one you use to log into the Game panel (the password you set by email for the panel).</p>
                        ${sftp.panel_url ? `
                            <div class="flex flex-wrap gap-3">
                                <a href="${sftp.panel_url}/auth/password" target="_blank" rel="noopener noreferrer" 
                                   class="inline-flex items-center gap-1 text-yellow-300 hover:text-yellow-200 text-sm transition-colors">
                                    🔑 Reset Password
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            </div>
                        ` : ''}
                    </div>
                    
                    <div class="bg-gray-800/50 border border-gray-500 rounded-lg p-4">
                        <h4 class="text-gray-300 font-medium mb-3">📁 Connection Example</h4>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-xs text-gray-100 mb-1">FileZilla / WinSCP</label>
                                <code class="block text-xs text-gray-200 bg-gray-700 p-2 rounded font-mono">
                                    Host: ${sftp.server || 'server'}<br>
                                    Port: ${sftp.port || 'port'}<br>
                                    Username: ${sftp.username || 'username'}<br>
                                    Password: [Your game panel password]
                                </code>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-100 mb-1">Command Line</label>
                                <div class="flex items-center gap-2">
                                    <code class="flex-1 text-xs text-gray-200 bg-gray-700 p-2 rounded font-mono">sftp -P ${sftp.port || 'port'} ${sftp.username || 'username'}@${sftp.server || 'server'}</code>
                                    <button onclick="copyToClipboard('sftp -P ${sftp.port || 'port'} ${sftp.username || 'username'}@${sftp.server || 'server'}')" class="text-blue-300 hover:text-blue-200 text-xs" title="Copy command">📋</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderSftpError() {
            if (!sftpInfoDiv) return;
            
            sftpInfoDiv.innerHTML = `
                <div class="text-center py-8">
                    <div class="text-base/50 mb-2">
                        <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <p class="text-base/50">SFTP connection details not available</p>
                    <p class="text-sm text-gray-500 mt-1">This may be due to insufficient permissions</p>
                </div>
            `;
        }        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }        function copyToClipboard(text) {
            if (!text) return;
            
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast('Copied to clipboard!', 'success');
                }).catch(() => {
                    showToast('Failed to copy to clipboard', 'error');
                });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'absolute';
                textArea.style.left = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    showToast('Copied to clipboard!', 'success');
                } catch (err) {
                    showToast('Failed to copy to clipboard', 'error');
                }
                document.body.removeChild(textArea);
            }
        }

        // Make copyToClipboard globally available
        window.copyToClipboard = copyToClipboard;

        function showConfirmModal(message, callback) {
            if (confirmMessage) confirmMessage.textContent = message;
            confirmCallback = callback;
            confirmModal?.classList.remove('hidden');
        }

        function showSpinner() {
            document.getElementById('settings-spinner')?.classList.remove('hidden');
        }

        function hideSpinner() {
            document.getElementById('settings-spinner')?.classList.add('hidden');
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `px-4 py-3 rounded-lg text-white pointer-events-auto transition-all duration-300 transform translate-x-full ${
                type === 'success' ? 'bg-green-600' :
                type === 'error' ? 'bg-red-600' :
                type === 'warning' ? 'bg-yellow-600' : 'bg-blue-600'
            }`;
            toast.textContent = message;

            const container = document.getElementById('toast-container');
            container?.appendChild(toast);

            // Slide in
            setTimeout(() => toast.classList.remove('translate-x-full'), 100);

            // Remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => container?.removeChild(toast), 300);
            }, 5000);
        }

        // Event handlers
        renameBtn?.addEventListener('click', async () => {
            const newName = serverNameInput?.value.trim();
            if (!newName) {
                showToast('Please enter a server name', 'error');
                return;
            }

            try {
                await api.renameServer(newName);
                showToast('Server renamed successfully', 'success');
                loadSettings(); // Reload to show updated info
            } catch (err) {
                showToast(`Failed to rename server: ${err.message}`, 'error');
            }
        });

        reinstallBtn?.addEventListener('click', () => {
            showConfirmModal(
                'Are you sure you want to reinstall this server? This will PERMANENTLY DELETE ALL FILES and reset the server to its default state. This action cannot be undone.',
                async () => {
                    try {
                        await api.reinstallServer();
                        showToast('Server reinstall initiated. This may take several minutes.', 'success');
                    } catch (err) {
                        showToast(`Failed to reinstall server: ${err.message}`, 'error');
                    }
                }
            );
        });

        confirmCancel?.addEventListener('click', () => {
            confirmModal?.classList.add('hidden');
        });

        confirmOk?.addEventListener('click', () => {
            confirmModal?.classList.add('hidden');
            if (confirmCallback) {
                confirmCallback();
                confirmCallback = null;
            }
        });

        // Initial load
        await loadSettings();
    }

    function unmountSettings() {
        if (!isMounted) return;
        isMounted = false;
        const container = document.getElementById('settings-container');
        if (container) container.innerHTML = '';
    }

    function detectSettings() {
        const exists = Boolean(document.getElementById('settings-section'));
        if (exists && !isMounted) mountSettings();
        if (!exists && isMounted) unmountSettings();
    }

    // Initialize
    detectSettings();
    setInterval(detectSettings, 1000);
})();
</script>
@endscript
