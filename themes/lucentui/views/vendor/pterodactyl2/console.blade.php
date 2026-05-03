<!-- import xterm uising cdn -->
@assets
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@xterm/xterm@5.5.0/css/xterm.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/@xterm/xterm@5.5.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/@xterm/addon-canvas@0.7.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/@xterm/addon-fit@0.10.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        @keyframes status-pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }

            50% {
                opacity: 0.9;
                transform: scale(1.02);
                box-shadow: 0 0 2px 0.5px rgba(255, 255, 255, 0.15);
            }
        }

        .status-pulse {
            animation: status-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .status-pulse-green {
            animation: status-pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .status-pulse-yellow {
            animation: status-pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .status-pulse-orange {
            animation: status-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .status-pulse-red {
            animation: status-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .status-pulse-blue {
            animation: status-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .status-pulse-purple {
            animation: status-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
@endassets

<section
    class="fade-appear-done fade-enter-done p-6 rounded-xl mx-auto"
    id="console-section">

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-base">{{ $server['name'] }}</h1>
                    <p class="text-sm text-base/50 mt-1">Console</p>
                </div>
                <div id="status-indicator"
                    class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 rounded-full">
                    <div id="status-dot" class="w-2.5 h-2.5 rounded-full bg-gray-500 status-pulse"></div>
                    <span id="status-text"
                        class="text-sm font-medium text-gray-700 dark:text-base/40">Loading...</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <!-- Server Action Buttons -->
                <div class="flex items-center gap-1.5 p-1 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-neutral/50"
                    id="server-actions">
                    <button id="btn-start" data-action="start" disabled
                        class="group relative p-2.5 rounded-xl bg-white dark:bg-background-secondary/50 border border-gray-200 dark:border-gray-600 
                               text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 
                               hover:border-green-300 dark:hover:border-green-500 transition-all duration-200 
                               disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white 
                               disabled:hover:border-gray-200 dark:disabled:hover:bg-background-secondary/50 
                               dark:disabled:hover:border-gray-600 shadow-sm hover:shadow-md 
                               w-10 h-10 flex items-center justify-center"
                        title="Start Server">
                        <i class="ri-play-fill text-lg"></i>
                        <div
                            class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                            Start
                        </div>
                    </button>
                    <button id="btn-stop" data-action="stop" disabled
                        class="group relative p-2.5 rounded-xl bg-white dark:bg-background-secondary/50 border border-gray-200 dark:border-gray-600 
                               text-yellow-600 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 
                               hover:border-yellow-300 dark:hover:border-yellow-500 transition-all duration-200 
                               disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white 
                               disabled:hover:border-gray-200 dark:disabled:hover:bg-background-secondary/50 
                               dark:disabled:hover:border-gray-600 shadow-sm hover:shadow-md 
                               w-10 h-10 flex items-center justify-center"
                        title="Stop Server">
                        <i class="ri-pause-fill text-lg"></i>
                        <div
                            class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                            Stop
                        </div>
                    </button>
                    <button id="btn-restart" data-action="restart" disabled
                        class="group relative p-2.5 rounded-xl bg-white dark:bg-background-secondary/50 border border-gray-200 dark:border-gray-600 
                               text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 
                               hover:border-blue-300 dark:hover:border-blue-500 transition-all duration-200 
                               disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white 
                               disabled:hover:border-gray-200 dark:disabled:hover:bg-background-secondary/50 
                               dark:disabled:hover:border-gray-600 shadow-sm hover:shadow-md 
                               w-10 h-10 flex items-center justify-center"
                        title="Restart Server">
                        <i class="ri-restart-fill text-lg"></i>
                        <div
                            class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                            Restart
                        </div>
                    </button>
                    <button id="btn-kill" data-action="kill" disabled
                        class="group relative p-2.5 rounded-xl bg-white dark:bg-background-secondary/50 border border-gray-200 dark:border-gray-600 
                               text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 
                               hover:border-red-300 dark:hover:border-red-500 transition-all duration-200 
                               disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white 
                               disabled:hover:border-gray-200 dark:disabled:hover:bg-background-secondary/50 
                               dark:disabled:hover:border-gray-600 shadow-sm hover:shadow-md 
                               w-10 h-10 flex items-center justify-center"
                        title="Force Stop Server">
                        <i class="ri-stop-fill text-lg"></i>
                        <div
                            class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                            Kill
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Banner -->
    <div id="status-banner" class="hidden mb-4 p-4 rounded-xl border-l-4 flex items-center gap-3">
        <div id="status-icon" class="flex-shrink-0">
        </div>
        <div class="flex-1">
            <h4 id="status-title" class="font-semibold text-sm"></h4>
            <p id="status-message" class="text-sm"></p>
        </div>
    </div>

    <div class="grid grid-cols-9 gap-4 my-4">
        <div class="grid grid-cols-9 xl:col-span-7 lg:col-span-9 col-span-9 gap-1 rounded-xl p-3 bg-gray-900/50 border border-neutral/50 shadow-lg">
            <!-- status banner -->
            <!-- status indicator -->
            <!-- terminal -->
            <div id="terminal" class="w-full h-[790] col-span-9 mb-0">
            </div>
            <input type="text" id="console-input" name="ConsoleInput" placeholder="Enter command here..."
                class="col-span-9 p-2 bg-background-secondary/50 rounded-md text-base opacity-85" />
        </div>
        <div class="col-span-2  xl:col-span-2 col-span-9 grid grid-cols-6 gap-2">
            <!-- server information cards -->
            <div
                class="bg-background-secondary/50 border border-neutral/50 p-2 rounded-xl h-28 max-h-28 max-w-64 xl:col-span-6 lg:col-span-2 sm:col-span-3 col-span-3">
                <div class="flex flex-col justify-center overflow-hidden w-full ml-4 h-full">
                    <p class="text-lg text-base">Address</p>
                    <p class="text-sm font-semibold break-words mr-4" style="font-size: 115.625%;" id="server-adress"> loading....</p>
                </div>
            </div>
            <div
                class="bg-background-secondary/50 border border-neutral/50 p-2 rounded-xl h-28 max-h-28 max-w-64 xl:col-span-6 lg:col-span-2 sm:col-span-3 col-span-3">
                <div class="flex flex-col justify-center overflow-hidden w-full ml-4 h-full">
                    <p class="text-lg text-base">Uptime</p>
                    <p class="text-sm font-semibold" style="font-size: 115.625%;" id="server-uptime"> loading...</p>
                </div>
            </div>
            <div
                class="bg-background-secondary/50 border border-neutral/50 p-2 rounded-xl h-28 max-h-28 max-w-64 xl:col-span-6 lg:col-span-2 sm:col-span-3 col-span-3">
                <div class="flex flex-col justify-center overflow-hidden w-full ml-4 h-full">
                    <p class="text-lg text-base">CPU Load</p>
                    <div class="h-[1.75rem] w-full font-semibold text-base truncate" style="font-size: 115.625%;"
                        id="server-cpu">
                        ~ <span class="ml-1 text-base/40 text-[70%] select-none">/ 100%</span></div>

                </div>
            </div>
            <div
                class="bg-background-secondary/50 border border-neutral/50 p-2 rounded-xl h-28 max-h-28 max-w-64 xl:col-span-6 lg:col-span-2 sm:col-span-3 col-span-3">
                <div class="flex flex-col justify-center overflow-hidden w-full px-4 h-full">
                    <p class="text-lg text-base">Memory</p>
                    <div class="h-[1.75rem] w-full font-semibold text-base truncate" style="font-size: 115.625%;"
                        id="server-ram">~<span class="ml-1 text-base/40 text-[70%] select-none">/ ~</span></div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2 dark:bg-background-secondary/50">
                        <div id="server-ram-bar" class="bg-yellow-500 h-2 rounded-full" style="width: 0%"></div>
                    </div>
                </div>
            </div>
            <div
                class="bg-background-secondary/50 border border-neutral/50 p-2 rounded-xl h-28 max-h-28 max-w-64 xl:col-span-6 lg:col-span-2 sm:col-span-3 col-span-3">
                <div class="flex flex-col justify-center overflow-hidden w-full px-4 h-full">
                    <p class="text-lg text-base">Disk</p>
                    <div class="h-[1.75rem] w-full font-semibold text-base truncate" style="font-size: 115.625%;"
                        id="server-disk">~<span class="ml-1 text-base/40 text-[70%] select-none">/ ~</span></div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2 dark:bg-background-secondary/50">
                        <div id="server-disk-bar" class="bg-green-500 h-2 rounded-full" style="width: 0%"></div>
                    </div>
                </div>
            </div>
            <div
                class="bg-background-secondary/50 border border-neutral/50 p-2 rounded-xl h-28 max-h-28 max-w-64 xl:col-span-6 lg:col-span-2 sm:col-span-3 col-span-3">
                <div class="flex flex-col justify-center overflow-hidden w-full ml-4 h-full">
                    <p class="text-lg text-base">Network (Inbound)</p>
                    <div class="h-[1.75rem] w-full font-semibold text-base truncate" style="font-size: 115.625%;"
                        id="server-inbound">~</div>
                    <div id="server-inbound-pps" class="text-sm text-base/75 select-none">~ pps</div>
                </div>
            </div>
            <div
                class="bg-background-secondary/50 border border-neutral/50 p-2 rounded-xl h-28 max-h-28 max-w-64 xl:col-span-6 lg:col-span-2 sm:col-span-3 col-span-3">
                <div class="flex flex-col justify-center overflow-hidden w-full ml-4 h-full">
                    <p class="text-lg text-base">Network (Outbound)</p>
                    <div class="h-[1.75rem] w-full font-semibold text-base truncate" style="font-size: 115.625%;"
                        id="server-outbound">~</div>
                    <div id="server-outbound-pps" class="text-sm text-base/75 select-none">~ pps</div>
                </div>
            </div>
        </div>

        <!-- here you can add the grids for the server information like CPU, RAM, Disk Usage, Server Address. on the left side of thwe terminal -->

        <!-- CPU RAM network charts with tailwind dark theme -->
        <div class="group bg-background-secondary/50 border border-neutral/50 p-4 rounded-xl xl:col-span-3 lg:col-span-9 col-span-9">
            <div class="flex items-center justify-between">
                <h3 class=" group-hover:text-gray-50">CPU Load</h3>
            </div>
            <div id="cpu-chart" class="w-full h-64"></div>
        </div>
        <div class="group bg-background-secondary/50 border border-neutral/50 p-4 rounded-xl xl:col-span-3 lg:col-span-9 col-span-9">
            <div class="flex items-center justify-between">
                <h3 class=" group-hover:text-gray-50">RAM Usage</h3>
            </div>
            <div id="ram-chart" class="w-full h-64"></div>
        </div>
        <div class="group bg-background-secondary/50 border border-neutral/50 p-4 rounded-xl xl:col-span-3 lg:col-span-9 col-span-9">
            <div class="flex items-center justify-between">
                <h3 class="group-hover:text-gray-50">Network Load</h3>
            </div>
            <div id="network-chart" class="w-full h-64"></div>
        </div>
    </div>

</section>

<!-- Toast container -->
<div id="toast-container" class="fixed top-4 right-4 space-y-2 z-50 pointer-events-none"></div>

@script
    <script type="module">
        /**
         * Console Management Script
         * 
         * Fixes for preventing double console loading:
         * 1. Added mounting flag to prevent concurrent mounting operations
         * 2. Added proper error handling and cleanup in try/catch blocks
         * 3. Added global interval cleanup to prevent multiple watchers
         * 4. Added debouncing to prevent rapid mount/unmount cycles
         * 5. Added page unload cleanup to prevent memory leaks
         * 6. Added logging for debugging mount/unmount operations
         */
        (async function() {
            // --- FETCH STATIC SERVER INFO (address + limits) ---
            async function fetchServerInfo() {
                const response = await fetch("/services/{{ $service->id }}/details");
                if (!response.ok) throw new Error('Network response was not ok ' + response.statusText);
                const data = await response.json();
                return data.attributes; // { address, limits:{ cpu, memory, disk }, … }
            }

            const serverInfo = await fetchServerInfo();
            // check if cpu limits is 0 if 0 its infinite set to infite symbol
            if (serverInfo.limits.cpu === 0) {
                serverInfo.limits.cpu = '∞';
            }

            // Update status banner based on server state
            updateStatusBanner(serverInfo);

            // Update status indicator
            updateStatusIndicator(serverInfo);

            // populate the address card immediately
            document.getElementById('server-adress').innerText = serverInfo.address;

            // pull limits out for later
            const {
                cpu: MAX_CPU,
                memory: MAX_RAM_MB,
                disk: MAX_DISK_MB
            } = serverInfo.limits;

            // --- STATUS BANNER ---
            function updateStatusBanner(serverInfo) {
                const banner = document.getElementById('status-banner');
                const icon = document.getElementById('status-icon');
                const title = document.getElementById('status-title');
                const message = document.getElementById('status-message');

                // Check for various server states (in order of priority)
                if (serverInfo.is_node_under_maintenance) {
                    showBanner('maintenance', 'Node Under Maintenance',
                        'The node hosting this server is currently under maintenance. Server functionality may be limited.'
                        );
                } else if (serverInfo.is_suspended) {
                    showBanner('suspended', 'Server Suspended',
                        'This server has been suspended. Please check your invoices for unpaid bills or contact support if this is a mistake.'
                        );
                } else if (serverInfo.is_installing) {
                    showBanner('installing', 'Installing Server',
                        'This server is currently being installed. Please wait for the installation to complete.'
                        );
                } else if (serverInfo.is_transferring) {
                    showBanner('transferring', 'Server Transfer in Progress',
                        'This server is being transferred to another node. Some functionality may be limited during this process.'
                        );
                } else {
                    // Hide banner if no special status
                    banner.classList.add('hidden');
                    return;
                }

                function showBanner(type, titleText, messageText) {
                    banner.classList.remove('hidden');
                    title.textContent = titleText;
                    message.textContent = messageText;

                    // Reset classes
                    banner.className = 'mb-4 p-4 rounded-xl border-l-4 flex items-center gap-3';

                    // Set colors and icons based on type
                    switch (type) {
                        case 'maintenance':
                            banner.classList.add('bg-orange-50', 'dark:bg-orange-900/20', 'border-orange-500',
                                'text-orange-900', 'dark:text-orange-200');
                            icon.innerHTML = `
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            `;
                            break;
                        case 'suspended':
                            banner.classList.add('bg-red-50', 'dark:bg-red-900/20', 'border-red-500',
                                'text-red-900', 'dark:text-red-200');
                            icon.innerHTML = `
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                                </svg>
                            `;
                            break;
                        case 'installing':
                            banner.classList.add('bg-blue-50', 'dark:bg-blue-900/20', 'border-blue-500',
                                'text-blue-900', 'dark:text-blue-200');
                            icon.innerHTML = `
                                <svg class="w-6 h-6 text-blue-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            `;
                            break;
                        case 'transferring':
                            banner.classList.add('bg-purple-50', 'dark:bg-purple-900/20', 'border-purple-500',
                                'text-purple-900', 'dark:text-purple-200');
                            icon.innerHTML = `
                                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                            `;
                            break;
                    }
                }
            }

            // --- STATUS INDICATOR ---
            function updateStatusIndicator(serverInfo) {
                const dot = document.getElementById('status-dot');
                const text = document.getElementById('status-text');

                if (!dot || !text) return;

                // Remove all existing classes except base ones
                dot.className = 'w-2.5 h-2.5 rounded-full';

                // Determine status and styling
                if (serverInfo.is_node_under_maintenance) {
                    dot.classList.add('bg-orange-500', 'status-pulse-orange');
                    text.textContent = 'Under Maintenance';
                    text.className = 'text-sm font-medium text-orange-600 dark:text-orange-400';
                } else if (serverInfo.is_suspended) {
                    dot.classList.add('bg-red-500', 'status-pulse-red');
                    text.textContent = 'Suspended';
                    text.className = 'text-sm font-medium text-red-600 dark:text-red-400';
                } else if (serverInfo.is_installing) {
                    dot.classList.add('bg-blue-500', 'status-pulse-blue');
                    text.textContent = 'Installing';
                    text.className = 'text-sm font-medium text-blue-600 dark:text-blue-400';
                } else if (serverInfo.is_transferring) {
                    dot.classList.add('bg-purple-500', 'status-pulse-purple');
                    text.textContent = 'Transferring';
                    text.className = 'text-sm font-medium text-purple-600 dark:text-purple-400';
                } else {
                    // Normal operation - status will be updated by websocket
                    dot.classList.add('bg-gray-500', 'status-pulse');
                    text.textContent = 'Connecting...';
                    text.className = 'text-sm font-medium text-gray-600 dark:text-base/50';
                }
            }

            // Function to update status indicator from websocket data
            function updateStatusFromWebsocket(status) {
                const dot = document.getElementById('status-dot');
                const text = document.getElementById('status-text');

                if (!dot || !text) return;

                // Remove all animation classes but keep base classes
                dot.classList.remove('animate-pulse', 'animate-ping', 'animate-bounce', 'status-pulse',
                    'status-pulse-strong',
                    'status-pulse-green', 'status-pulse-yellow', 'status-pulse-orange', 'status-pulse-red',
                    'status-pulse-blue', 'status-pulse-purple');
                dot.className = 'w-2.5 h-2.5 rounded-full';

                switch (status) {
                    case 'running':
                        dot.classList.add('bg-green-500', 'status-pulse-green');
                        text.textContent = 'Online';
                        text.className = 'text-sm font-medium text-green-600 dark:text-green-400';
                        break;
                    case 'offline':
                        dot.classList.add('bg-gray-500');
                        text.textContent = 'Offline';
                        text.className = 'text-sm font-medium text-gray-600 dark:text-base/50';
                        break;
                    case 'starting':
                        dot.classList.add('bg-yellow-500', 'status-pulse-yellow');
                        text.textContent = 'Starting';
                        text.className = 'text-sm font-medium text-yellow-600 dark:text-yellow-400';
                        break;
                    case 'stopping':
                        dot.classList.add('bg-orange-500', 'status-pulse-orange');
                        text.textContent = 'Stopping';
                        text.className = 'text-sm font-medium text-orange-600 dark:text-orange-400';
                        break;
                    default:
                        dot.classList.add('bg-gray-500', 'status-pulse');
                        text.textContent = 'Unknown';
                        text.className = 'text-sm font-medium text-gray-600 dark:text-base/50';
                        break;
                }
            }

            // --- UTILS ---
            function getLastXSecondsLabels(n) {
                return Array.from({
                    length: n
                }, (_, i) => {
                    const d = new Date(Date.now() - i * 1000);
                    return d.toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                }).reverse();
            }

            function formatBytes(bytes, dec = 2) {
                // check if bytes is a number
                if (typeof bytes !== 'number' || isNaN(bytes)) return 'N/A';
                if (bytes === 0) return '0 Bytes';
                const k = 1024,
                    dm = Math.max(0, dec);
                const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
            }

            function formatUptime(s) {
                const d = Math.floor(s / 86400);
                const h = Math.floor((s % 86400) / 3600);
                const m = Math.floor((s % 3600) / 60);
                const sec = Math.floor(s % 60);

                return [
                    d ? `${d}d` : '',
                    h ? `${h}h` : '',
                    m ? `${m}m` : '',
                    (sec || (!d && !h && !m)) ? `${sec}s` : ''
                ].filter(Boolean).join(' ');
            }


            // --- HISTORY ---
            class History {
                constructor(input, key = 'consoleCommandHistory') {
                    this.el = input;
                    this.key = key;
                    this.list = JSON.parse(localStorage.getItem(key) || '[]');
                    this.idx = -1;
                    this._bind();
                }

                _bind() {
                    this.el.addEventListener('keydown', e => {
                        if (e.key === 'ArrowUp') this._up(e);
                        else if (e.key === 'ArrowDown') this._down(e);
                    });
                }

                commit(cmd) {
                    if (!cmd) return;
                    this.list.push(cmd);
                    // keep only the last 100 commands
                    if (this.list.length > 100) {
                        this.list.shift();
                    }
                    localStorage.setItem(this.key, JSON.stringify(this.list));
                    this.idx = -1;
                }

                _up(e) {
                    if (!this.list.length) return;
                    this.idx = this.idx <= 0 ?
                        this.list.length - 1 :
                        this.idx - 1;
                    this.el.value = this.list[this.idx];
                    e.preventDefault();
                }

                _down(e) {
                    if (!this.list.length) return;
                    this.idx = this.idx < 0 || this.idx >= this.list.length - 1 ?
                        -1 :
                        this.idx + 1;
                    this.el.value = this.idx < 0 ?
                        '' :
                        this.list[this.idx];
                    e.preventDefault();
                }
            }


            // --- TERMINAL MANAGEMENT ---
            const Terminal = window.Terminal;
            const FitAddon = window.FitAddon.FitAddon;
            const CanvasAddon = window.CanvasAddon.CanvasAddon;

            class TerminalManager {
                constructor(containerId) {
                    const container = document.getElementById(containerId);
                    if (!container) {
                        throw new Error(`Terminal container '${containerId}' not found`);
                    }

                    // Mark container as initialized
                    container.setAttribute('data-console-initialized', 'true');

                    this.term = new Terminal({
                        convertEol: true,
                        cursorBlink: false,
                        theme: {
                            // make it transparent
                            background: 'rgba(255, 255, 255, 0)',
                        },
                        allowTransparency: true
                    });
                    this.fit = new FitAddon();
                    this.term.loadAddon(this.fit);
                    this.term.loadAddon(new CanvasAddon());
                    this.term.open(container);
                    window.addEventListener('resize', () => this.fit.fit());
                    setTimeout(() => this.fit.fit(), 100);
                }
                write(msg, kind = 'default') {
                    const prefix = {
                        error: '\x1b[31m[ERROR]\x1b[39m ',
                        info: '\x1b[33m[INFO]\x1b[39m '
                    } [kind] || '';
                    this.term.writeln(prefix + msg);
                }
            }


            // --- CHARTS (ApexCharts) ---
            class ChartManager {
                constructor() {
                    this.cpu = null;
                    this.ram = null;
                    this.net = null;
                    // common chart options
                    this._initCharts();
                }
                _initCharts() {
                    // common chart options
                    const MAX_POINTS = 60; // max points to show in the chart

                    const common = {
                        chart: {
                            type: 'area',
                            height: '98%',
                            width: '100%',
                            toolbar: {
                                show: false
                            },
                            animations: {
                                enabled: true,
                                easing: 'easeout'
                            },
                            background: 'transparent'
                        },
                        theme: {
                            mode: 'dark'
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shade: 'dark',
                                gradientToColors: ['transparent'],
                                opacityFrom: 0.9,
                                opacityTo: 0.1,
                                shadeIntensity: 1
                            }
                        },

                        // disable all grid lines
                        grid: {
                            show: false,
                            xaxis: {
                                lines: {
                                    show: false
                                }
                            },
                            yaxis: {
                                lines: {
                                    show: false
                                }
                            }
                        },

                        xaxis: {
                            type: 'datetime',
                            labels: {
                                show: false
                            },
                            axisTicks: {
                                show: false
                            },
                            axisBorder: {
                                show: false
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: ['#888']
                                },
                                formatter: v => v.toFixed(0)
                            },
                            axisTicks: {
                                show: false
                            },
                            axisBorder: {
                                show: false
                            }
                        },

                        tooltip: {
                            theme: 'dark'
                        },
                        dataLabels: {
                            enabled: false
                        },
                        markers: {
                            size: 0
                        },
                        legend: {
                            show: false
                        },
                        noData: {
                            text: ''
                        }
                    };

                    this.cpu = new ApexCharts(
                        document.getElementById('cpu-chart'), {
                            ...common,
                            series: [{
                                name: 'CPU',
                                data: []
                            }],
                            colors: ['#10B981']
                        }
                    );
                    this.ram = new ApexCharts(
                        document.getElementById('ram-chart'), {
                            ...common,
                            series: [{
                                name: 'RAM',
                                data: []
                            }],
                            colors: ['#06B6D4']
                        }
                    );
                    this.net = new ApexCharts(
                        document.getElementById('network-chart'), {
                            ...common,
                            series: [{
                                    name: 'In',
                                    data: []
                                },
                                {
                                    name: 'Out',
                                    data: []
                                }
                            ],
                            colors: ['#F59E0B', '#EF4444']
                        }
                    );

                    this.cpu.render();
                    this.ram.render();
                    this.net.render();
                }

                push(chartKey, val) {
                    const chart = this[chartKey];
                    const ts = Date.now();

                    // clone current series arrays
                    const newSeries = chart.w.config.series.map(s => ({
                        name: s.name,
                        data: [...s.data]
                    }));

                    // push the new point(s)
                    if (chartKey === 'net') {
                        newSeries[0].data.push({
                            x: ts,
                            y: val.rx
                        });
                        newSeries[1].data.push({
                            x: ts,
                            y: val.tx
                        });
                    } else {
                        newSeries[0].data.push({
                            x: ts,
                            y: val
                        });
                    }

                    // trim to last 60 points
                    newSeries.forEach(s => {
                        if (s.data.length > 45) s.data = s.data.slice(-45);
                    });

                    // update the chart
                    chart.updateSeries(newSeries, /* animate = */ false);
                }
            }


            // --- TOAST FUNCTION ---
            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `px-4 py-3 rounded-xl text-white pointer-events-auto transition-all duration-300 transform translate-x-full ${
                    type === 'success' ? 'bg-green-600' :
                    type === 'error' ? 'bg-red-600' :
                    type === 'warning' ? 'bg-yellow-600' : 
                    type === 'info' ? 'bg-blue-600' : 'bg-gray-600'
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

            // --- WEBSOCKET CLIENT ---
            class WSClient {
                constructor(term, charts) {
                    this.term = term;
                    this.charts = charts;
                    this.socket = null;
                    this.total = {
                        rx: 0,
                        tx: 0
                    };
                    this.netBase = null; // <-- use this to calc throughput
                    this.serviceId = null; // will be set when connecting
                }

                async getToken(serviceId) {
                    const res = await fetch(`/services/${serviceId}/websocket`);
                    if (!res.ok) throw new Error(res.statusText);
                    const {
                        data
                    } = await res.json();
                    return data;
                }


                async connect(serviceId) {
                    this.serviceId = serviceId; // store serviceId for later use
                    const res = await this.getToken(serviceId);
                    if (!res) throw new Error('Failed to get token');

                    // Store reference globally for cleanup detection
                    this.socket = new WebSocket(res.socket);
                    window.activeConsoleWebSocket = this.socket;

                    this.socket.addEventListener('open', () => {
                        this.socket.send(JSON.stringify({
                            event: 'auth',
                            args: [res.token]
                        }));
                    });
                    this.socket.addEventListener('message', e => this._onMsg(JSON.parse(e.data)));
                    this.socket.addEventListener('close', () => {
                        this.term.write('Disconnected', 'warning');
                        // Clear global reference when closed
                        if (window.activeConsoleWebSocket === this.socket) {
                            window.activeConsoleWebSocket = null;
                        }
                    });
                    this.socket.addEventListener('error', () => this.term.write('WS error', 'error'));
                }

                _onMsg({
                    event,
                    args
                }) {
                    switch (event) {
                        case 'stats':
                            const s = JSON.parse(args[0]);
                            // Only update if console is still mounted
                            if (mounted) {
                                this._updateStatusCards(s);
                                this._handleStats(s);
                            }
                            break;
                        case 'auth success':
                            this.socket.send(JSON.stringify({
                                event: 'send logs',
                                args: [null]
                            }));
                            break;
                        case 'console output':
                            args.forEach(line => this.term.write(line));
                            break;
                        case 'status':
                            updateButtons(args[0]);
                            this.term.write(`Server is now ${args[0]}`, 'info');
                            break;
                        case 'install output':
                            this.term.write(args[0]);
                            break;
                        case 'token expiring':
                            this.term.write('Session token is expiring generating a new one...', 'info');
                            this.getToken(this.serviceId)
                                .then(res => {
                                    this.socket.send(JSON.stringify({
                                        event: 'auth',
                                        args: [res.token]
                                    }));
                                })
                                .catch(err => this.term.write(`Error getting new token: ${err.message}`,
                                    'error'));
                            break;
                        case 'daemon message':
                            // Show daemon messages as warning toasts
                            if (args[0]) {
                                showToast(args[0], 'warning');
                                this.term.write(`[Daemon] ${args[0]}`, 'info');
                            }
                            break;
                        case 'install started':
                            // Show install start as info toast
                            showToast('Server installation has started...', 'info');
                            this.term.write('Installation started...', 'info');
                            break;
                        case 'install completed':
                            // Show install completion as success toast
                            showToast('Server installation completed successfully!', 'success');
                            this.term.write('Installation completed successfully!', 'success');
                            break;
                        default:
                            this.term.write(`Unknown event: ${event}`, 'error');
                            console.warn(`Unknown event: ${event}`, args);
                            break;
                    }
                }

                _updateStatusCards(s) {
                    // Prevent updates if elements don't exist (during unmount)
                    if (!document.getElementById('server-adress')) {
                        console.log('Status cards not found, skipping update');
                        return;
                    }

                    // Address (note your blade uses “server-adress”) attributes.relationships.allocations.data[0].attributes.ip and attributes.relationships.allocations.data[0].attributes.port we need to find the default  attributes.relationships.allocations.data[0].attributes.is_default
                    const allocation = serverInfo.relationships.allocations.data.find(a => a.attributes
                        .is_default);

                    // Address
                    // Note: allocation.attributes.ip and allocation.attributes.port are used to display the server address
                    // If no allocation is found, we display 'N/A'
                    const address = allocation ?
                        `${allocation.attributes.ip_alias ?? allocation.attributes.ip }:${allocation.attributes.port}` :
                        'N/A';
                    const addressEl = document.getElementById('server-adress');
                    if (addressEl) addressEl.innerText = address || 'N/A';

                    // Uptime
                    const uptimeEl = document.getElementById('server-uptime');
                    if (uptimeEl) uptimeEl.innerText = formatUptime(s.uptime / 1000 || 0);

                    // CPU (used / max)
                    const cpuEl = document.getElementById('server-cpu');
                    if (cpuEl) cpuEl.innerText = MAX_CPU === '∞' ?
                        `${s.cpu_absolute.toFixed(1)} / ∞` :
                        `${s.cpu_absolute.toFixed(1)} / ${MAX_CPU}%`;

                    // RAM: used / max using formatBytes
                    const maxRamBytes = MAX_RAM_MB * 1024 * 1024;
                    const ramEl = document.getElementById('server-ram');
                    if (ramEl) ramEl.innerText =
                        `${formatBytes(s.memory_bytes)} / ${formatBytes(maxRamBytes)}`;
                    // update RAM bar width
                    const ramPercent = Math.round((s.memory_bytes / maxRamBytes) * 100);
                    const ramBarEl = document.getElementById('server-ram-bar');
                    if (ramBarEl) ramBarEl.style.width = `${ramPercent}%`;

                    // Disk: used / max using formatBytes
                    if (s.disk_bytes != null) {
                        const maxDiskBytes = MAX_DISK_MB * 1024 * 1024;
                        const diskEl = document.getElementById('server-disk');
                        if (diskEl) diskEl.innerText =
                            `${formatBytes(s.disk_bytes)} / ${formatBytes(maxDiskBytes)}`;
                        // update Disk bar width
                        const diskPercent = Math.round((s.disk_bytes / maxDiskBytes) * 100);
                        const diskBarEl = document.getElementById('server-disk-bar');
                        if (diskBarEl) diskBarEl.style.width = `${diskPercent}%`;
                    }

                    // Network in/out unchanged …
                    const inboundEl = document.getElementById('server-inbound');
                    if (inboundEl) inboundEl.innerText = formatBytes(s.network.rx_bytes);
                    const outboundEl = document.getElementById('server-outbound');
                    if (outboundEl) outboundEl.innerText = formatBytes(s.network.tx_bytes);

                    // rolling bytes-per-second
                    const now = Date.now();
                    if (!this.netBase) {
                        this.netBase = {
                            rx: s.network.rx_bytes,
                            tx: s.network.tx_bytes,
                            time: now
                        };
                    } else {
                        const secs = (now - this.netBase.time) / 1000;
                        const rxBps = (s.network.rx_bytes - this.netBase.rx) / secs;
                        const txBps = (s.network.tx_bytes - this.netBase.tx) / secs;
                        this.netBase = {
                            rx: s.network.rx_bytes,
                            tx: s.network.tx_bytes,
                            time: now
                        };

                        document.getElementById('server-inbound-pps').innerText =
                            formatBytes(rxBps) + '/s';
                        document.getElementById('server-outbound-pps').innerText =
                            formatBytes(txBps) + '/s';
                    }
                }

                _handleStats(s) {
                    const t = new Date().toLocaleTimeString();
                    this.charts.push('cpu', s.cpu_absolute, t);
                    this.charts.push('ram', s.memory_bytes / 1024 / 1024, t);
                    const rx = Math.max(0, s.network.rx_bytes / 1024 - this.total.rx);
                    const tx = Math.max(0, s.network.tx_bytes / 1024 - this.total.tx);
                    this.total = {
                        rx: s.network.rx_bytes / 1024,
                        tx: s.network.tx_bytes / 1024
                    };
                    this.charts.push('net', {
                        rx,
                        tx
                    }, t);
                }

                sendCommand(cmd) {
                    this.socket.send(JSON.stringify({
                        event: 'send command',
                        args: [cmd]
                    }));
                }

                setState(state) {
                    this.socket.send(JSON.stringify({
                        event: 'set state',
                        args: [state]
                    }));
                }
            }


            // button refs will be (re)selected on each mount
            let btnStart, btnStop, btnRestart, btnKill;
            // define server action handlers
            let onSuspendedCommand; // Declare in outer scope for cleanup
            let tetrisGame = null; // Declare tetrisGame in outer scope

            let history, term, charts, ws, mounted = false;
            let mounting = false; // Flag to prevent concurrent mounting
            let watchInterval = null; // Store interval reference for cleanup
            let lastTerminalCheck = 0; // Debounce rapid checks
            const inputEl = () => document.getElementById('console-input');

            // Function to check if elements are already initialized
            function elementsAlreadyInitialized() {
                const terminalEl = document.getElementById('terminal');
                const consoleInputEl = inputEl();

                // Check for signs that elements are already initialized
                const hasTerminalData = terminalEl && (
                    terminalEl.hasAttribute('data-console-initialized') ||
                    terminalEl.querySelector('.xterm') !== null ||
                    terminalEl.querySelector('.xterm-viewport') !== null ||
                    terminalEl.querySelector('.xterm-screen') !== null ||
                    terminalEl.querySelector('canvas') !== null ||
                    terminalEl.children.length > 0 ||
                    terminalEl.innerHTML.trim() !== ''
                );

                const hasInputListeners = consoleInputEl && (
                    consoleInputEl.hasAttribute('data-console-listeners') ||
                    // Check if input has any non-default event listeners
                    (consoleInputEl._consoleEventListeners && consoleInputEl._consoleEventListeners.length > 0)
                );

                const hasActiveWebSocket = window.activeConsoleWebSocket &&
                    window.activeConsoleWebSocket.readyState === WebSocket.OPEN;

                // Check for existing ApexCharts instances
                const hasActiveCharts = ['cpu-chart', 'ram-chart', 'network-chart'].some(chartId => {
                    const chartEl = document.getElementById(chartId);
                    return chartEl && (
                        chartEl.querySelector('.apexcharts-canvas') !== null ||
                        chartEl.children.length > 0 ||
                        chartEl.innerHTML.trim() !== ''
                    );
                });

                // Check for any global console state
                const hasGlobalState = window.consoleWatchInterval !== undefined ||
                    window.suspendedInterval !== undefined ||
                    window.activeConsoleWebSocket !== undefined;

                return hasTerminalData || hasInputListeners || hasActiveWebSocket || hasActiveCharts ||
                    hasGlobalState;
            }

            // Function to cleanup existing elements before reinitializing
            function cleanupExistingElements(force = false) {
                console.log('Cleaning up existing elements...', force ? '(forced)' : '');

                // Clean up terminal element - be more aggressive
                const terminalEl = document.getElementById('terminal');
                if (terminalEl) {
                    // Clear any existing xterm instances (multiple selectors for thorough cleanup)
                    const xtermSelectors = ['.xterm', '.xterm-viewport', '.xterm-screen', '.xterm-helper-textarea',
                        '.xterm-rows', '.xterm-cursor-layer', '.xterm-selection-layer',
                        'canvas', '.xterm-decoration-container'
                    ];

                    xtermSelectors.forEach(selector => {
                        const elements = terminalEl.querySelectorAll(selector);
                        elements.forEach(el => {
                            try {
                                el.remove();
                            } catch (e) {
                                console.warn('Error removing element:', e);
                            }
                        });
                    });

                    // Force clear all children and reset
                    try {
                        terminalEl.innerHTML = '';
                        terminalEl.removeAttribute('data-console-initialized');
                        // Clear any inline styles that might interfere
                        terminalEl.style.cssText = '';
                    } catch (e) {
                        console.warn('Error clearing terminal element:', e);
                    }
                }

                // Clean up input element listeners - more thorough approach
                const consoleInputEl = inputEl();
                if (consoleInputEl) {
                    try {
                        // Mark existing listeners for tracking
                        if (consoleInputEl._consoleEventListeners) {
                            consoleInputEl._consoleEventListeners.forEach(({
                                type,
                                listener
                            }) => {
                                consoleInputEl.removeEventListener(type, listener);
                            });
                        }

                        // Clone element to remove ALL event listeners
                        const newInput = consoleInputEl.cloneNode(true);
                        if (consoleInputEl.parentNode) {
                            consoleInputEl.parentNode.replaceChild(newInput, consoleInputEl);
                        }
                        newInput.removeAttribute('data-console-listeners');
                        newInput._consoleEventListeners = [];
                    } catch (e) {
                        console.warn('Error cleaning input element:', e);
                    }
                }

                // Clean up any existing WebSocket connections
                if (window.activeConsoleWebSocket) {
                    try {
                        if (window.activeConsoleWebSocket.readyState === WebSocket.OPEN ||
                            window.activeConsoleWebSocket.readyState === WebSocket.CONNECTING) {
                            window.activeConsoleWebSocket.close();
                        }
                    } catch (e) {
                        console.warn('Error closing WebSocket:', e);
                    }
                    window.activeConsoleWebSocket = null;
                }

                // Clean up chart containers - more thorough
                ['cpu-chart', 'ram-chart', 'network-chart'].forEach(chartId => {
                    const chartEl = document.getElementById(chartId);
                    if (chartEl) {
                        try {
                            // Remove ApexCharts specific elements
                            const apexSelectors = ['.apexcharts-canvas', '.apexcharts-svg',
                                '.apexcharts-inner',
                                '.apexcharts-graphical', '.apexcharts-tooltip'
                            ];
                            apexSelectors.forEach(selector => {
                                const elements = chartEl.querySelectorAll(selector);
                                elements.forEach(el => {
                                    try {
                                        el.remove();
                                    } catch (removeError) {
                                        console.warn(`Error removing chart element:`, removeError);
                                    }
                                });
                            });

                            chartEl.innerHTML = '';
                        } catch (e) {
                            console.warn(`Error cleaning chart ${chartId}:`, e);
                        }
                    }
                });

                // Reset button states - clone to remove all listeners
                const buttons = ['start', 'stop', 'restart', 'kill'];
                buttons.forEach(action => {
                    try {
                        const btn = document.querySelector(`[data-action="${action}"]`);
                        if (btn) {
                            // Clone button to remove event listeners
                            const newBtn = btn.cloneNode(true);
                            if (btn.parentNode) {
                                btn.parentNode.replaceChild(newBtn, btn);
                            }
                        }
                    } catch (e) {
                        console.warn(`Error cleaning button ${action}:`, e);
                    }
                });

                // Clean up any global intervals
                if (window.suspendedInterval) {
                    clearInterval(window.suspendedInterval);
                    window.suspendedInterval = null;
                }

                if (window.consoleWatchInterval) {
                    clearInterval(window.consoleWatchInterval);
                    window.consoleWatchInterval = null;
                }

                // Force garbage collection if available (development only)
                if (force && window.gc && typeof window.gc === 'function') {
                    try {
                        window.gc();
                    } catch (e) {
                        // Ignore - gc() not available in production
                    }
                }
            }

            function updateButtons(status) {
                btnStart = btnStart || document.querySelector('[data-action="start"]');
                btnStop = btnStop || document.querySelector('[data-action="stop"]');
                btnRestart = btnRestart || document.querySelector('[data-action="restart"]');
                btnKill = btnKill || document.querySelector('[data-action="kill"]');
                // offline → allow start, disallow stop/restart/kill
                const isRunning = status === 'running';
                const isOffline = status === 'offline';
                btnStart.disabled = !isOffline;
                btnStop.disabled = !isRunning;
                btnRestart.disabled = !isRunning;
                btnKill.disabled = !isRunning;

                // Update status indicator
                updateStatusFromWebsocket(status);
            }

            // --- SUSPENDED SERVER SIMULATION ---
            async function mountSuspendedConsole() {
                history = new History(inputEl());
                term = new TerminalManager('terminal');
                charts = new ChartManager();

                // Disable all buttons for suspended server
                btnStart = document.querySelector('[data-action="start"]');
                btnStop = document.querySelector('[data-action="stop"]');
                btnRestart = document.querySelector('[data-action="restart"]');
                btnKill = document.querySelector('[data-action="kill"]');

                [btnStart, btnStop, btnRestart, btnKill].forEach(btn => {
                    if (btn) {
                        btn.disabled = true;
                        btn.title = 'Server is suspended';
                    }
                });

                // Suspended console messages with easter eggs
                const suspendedMessages = [
                    '🔒 Server suspended - Check your invoices or contact support if this is a mistake',
                    '💤 Server is taking a nap... Check for unpaid invoices or contact support!',
                    '🚫 Access denied - Verify payment status or contact support if error',
                    '� Server suspended: Check your billing or contact support for assistance',
                    '📋 Review your invoices for unpaid bills or contact support if needed',
                    '🎭 Plot twist: Your server is suspended! Check invoices or contact support',
                    '🕷️ With great power comes great responsibility... and invoice payments',
                    '🎮 Game over! Check your billing or contact support to continue (or type "tetris" for fun)',
                    '🍕 Your server is as suspended as delivery with unpaid bills',
                    '🎸 Server suspended like a guitar string... Check invoices or contact support!',
                    '🚀 Houston, we have a problem... Check your billing dashboard',
                    '🧙‍♂️ Even magic can\'t unsuspend without payment... or support assistance!',
                    '🦄 Unicorns are real, but unpaid invoices suspend servers',
                    '🎪 Welcome to the suspended server circus! Check billing or contact support',
                    '🏰 Your server is locked in a tower. Payment or support has the key!',
                    '💰 Server hibernating due to billing issues - Check invoices or contact support',
                    '📞 Suspension notice: Verify payments or contact support if this is an error',
                    '🔍 Check your billing dashboard or contact support for clarification',
                ];

                let messageIndex = 0;
                let fakeStats = {
                    cpu: 0,
                    memory: 0,
                    disk: 0,
                    uptime: 0,
                    network: {
                        rx: 0,
                        tx: 0
                    }
                };

                // Show initial suspended message
                term.write('🔒 SERVER SUSPENDED', 'error');
                term.write('Your server has been suspended and cannot be accessed.', 'info');
                term.write(
                    'Please check your invoices for unpaid bills or contact support if this is a mistake.',
                    'info');
                term.write('', 'default');

                // Update info cards with fake data but real address
                function updateSuspendedCards() {
                    // Prevent updates if elements don't exist (during unmount)
                    if (!document.getElementById('server-adress')) {
                        console.log('Suspended status cards not found, skipping update');
                        return;
                    }

                    // Keep real address
                    const allocation = serverInfo.relationships.allocations.data.find(a => a.attributes
                        .is_default);
                    const address = allocation ?
                        `${allocation.attributes.ip_alias ?? allocation.attributes.ip}:${allocation.attributes.port}` :
                        'N/A';
                    const addressEl = document.getElementById('server-adress');
                    if (addressEl) addressEl.innerText = address;

                    // Fake uptime
                    const uptimeEl = document.getElementById('server-uptime');
                    if (uptimeEl) uptimeEl.innerText = '0s';

                    // Fake CPU (always 0)
                    const cpuEl = document.getElementById('server-cpu');
                    if (cpuEl) cpuEl.innerText = '0.0 / 0%';

                    // Fake RAM (always 0)
                    const maxRamBytes = MAX_RAM_MB * 1024 * 1024;
                    const ramEl = document.getElementById('server-ram');
                    if (ramEl) ramEl.innerText = `0 Bytes / ${formatBytes(maxRamBytes)}`;
                    const ramBarEl = document.getElementById('server-ram-bar');
                    if (ramBarEl) ramBarEl.style.width = '0%';

                    // Fake Disk (always 0)
                    const maxDiskBytes = MAX_DISK_MB * 1024 * 1024;
                    const diskEl = document.getElementById('server-disk');
                    if (diskEl) diskEl.innerText = `0 Bytes / ${formatBytes(maxDiskBytes)}`;
                    const diskBarEl = document.getElementById('server-disk-bar');
                    if (diskBarEl) diskBarEl.style.width = '0%';

                    // Fake Network (always 0)
                    const inboundEl = document.getElementById('server-inbound');
                    if (inboundEl) inboundEl.innerText = '0 Bytes';
                    const outboundEl = document.getElementById('server-outbound');
                    if (outboundEl) outboundEl.innerText = '0 Bytes';
                    const inboundPpsEl = document.getElementById('server-inbound-pps');
                    if (inboundPpsEl) inboundPpsEl.innerText = '0 Bytes/s';
                    const outboundPpsEl = document.getElementById('server-outbound-pps');
                    if (outboundPpsEl) outboundPpsEl.innerText = '0 Bytes/s';
                }

                // Update charts with fake data (always 0)
                function updateSuspendedCharts() {
                    charts.push('cpu', 0);
                    charts.push('ram', 0);
                    charts.push('net', {
                        rx: 0,
                        tx: 0
                    });
                }

                // Console input handler for suspended server
                onSuspendedCommand = function(e) {
                    if (e.key !== 'Enter' && e.keyCode !== 13) return;
                    e.preventDefault();
                    const cmd = inputEl().value.trim();
                    if (!cmd) return;

                    // Show the command they tried
                    term.write(`> ${cmd}`, 'default');

                    // Check for special commands
                    if (cmd.toLowerCase() === 'tetris' || cmd.toLowerCase() === 'play tetris') {
                        startTetrisGame();
                        history.commit(cmd);
                        inputEl().value = '';
                        return;
                    }

                    if (cmd.toLowerCase() === 'help' || cmd.toLowerCase() === 'commands') {
                        term.write('Available commands while suspended:', 'info');
                        term.write('  tetris - Play a fun Tetris game!', 'info');
                        term.write('  help - Show this help message', 'info');
                        term.write('Note: Server management is disabled while suspended.', 'info');
                        history.commit(cmd);
                        inputEl().value = '';
                        return;
                    }

                    // Respond with suspension message
                    const responses = [
                        '🔒 Command blocked: Server suspended - Check invoices or contact support',
                        '❌ Access denied: Verify payment status or contact support if error',
                        '🚫 Server suspended: Check billing dashboard or contact support',
                        '💤 Server sleeping: Check unpaid invoices or contact support',
                        '🎭 Nice try! Server suspended - Check your billing or contact support',
                        '🎮 Command not found: Check invoices or try "tetris" for fun, or "help" for commands',
                        '🦄 Magic command detected, but billing issues suspend servers',
                        '🕷️ With great commands comes great invoice responsibility',
                        '💳 Suspension active: Check payment status or contact support for help',
                        '📋 Server locked: Review billing or contact support if this is an error',
                    ];

                    term.write(responses[Math.floor(Math.random() * responses.length)], 'error');

                    history.commit(cmd);
                    inputEl().value = '';
                };

                // Wire up input
                const inputElement = inputEl();

                // Track event listeners for cleanup detection
                if (!inputElement._consoleEventListeners) {
                    inputElement._consoleEventListeners = [];
                }

                inputElement.addEventListener('keydown', onSuspendedCommand);
                inputElement.addEventListener('keypress', onSuspendedCommand);
                inputElement.setAttribute('data-console-listeners', 'true');

                // Track the listeners we just added
                inputElement._consoleEventListeners.push({
                    type: 'keydown',
                    listener: onSuspendedCommand
                }, {
                    type: 'keypress',
                    listener: onSuspendedCommand
                });

                // TETRIS GAME IMPLEMENTATION

                function startTetrisGame() {
                    term.write('🎮 Starting Tetris Game!', 'info');
                    term.write('Controls: A/D = Move, S = Rotate, W = Drop, Q = Quit', 'info');
                    term.write('═══════════════════════════════════════', 'default');

                    tetrisGame = new TetrisGame(term);
                    tetrisGame.start();
                }

                class TetrisGame {
                    constructor(terminal) {
                        this.term = terminal;
                        this.board = Array(20).fill().map(() => Array(10).fill(' '));
                        this.score = 0;
                        this.level = 1;
                        this.lines = 0;
                        this.gameRunning = false;
                        this.currentPiece = null;
                        this.nextPiece = null;
                        this.gameInterval = null;

                        // Tetris pieces
                        this.pieces = [{
                                shape: [
                                    [1, 1, 1, 1]
                                ],
                                color: '█'
                            }, // I
                            {
                                shape: [
                                    [1, 1],
                                    [1, 1]
                                ],
                                color: '█'
                            }, // O
                            {
                                shape: [
                                    [0, 1, 0],
                                    [1, 1, 1]
                                ],
                                color: '█'
                            }, // T
                            {
                                shape: [
                                    [0, 1, 1],
                                    [1, 1, 0]
                                ],
                                color: '█'
                            }, // S
                            {
                                shape: [
                                    [1, 1, 0],
                                    [0, 1, 1]
                                ],
                                color: '█'
                            }, // Z
                            {
                                shape: [
                                    [1, 0, 0],
                                    [1, 1, 1]
                                ],
                                color: '█'
                            }, // J
                            {
                                shape: [
                                    [0, 0, 1],
                                    [1, 1, 1]
                                ],
                                color: '█'
                            }, // L
                        ];

                        this.currentPos = {
                            x: 4,
                            y: 0
                        };
                        this.bindControls();
                    }

                    bindControls() {
                        this.keyHandler = (e) => {
                            if (!this.gameRunning) return;

                            switch (e.key.toLowerCase()) {
                                case 'a':
                                    this.movePiece(-1, 0);
                                    break;
                                case 'd':
                                    this.movePiece(1, 0);
                                    break;
                                case 's':
                                    this.rotatePiece();
                                    break;
                                case 'w':
                                    this.dropPiece();
                                    break;
                                case 'q':
                                    this.quitGame();
                                    break;
                            }
                            e.preventDefault();
                        };

                        document.addEventListener('keydown', this.keyHandler);
                    }

                    start() {
                        this.gameRunning = true;
                        this.spawnPiece();
                        this.gameInterval = setInterval(() => {
                            this.update();
                        }, Math.max(100, 1000 - (this.level - 1) * 100));
                        this.render();
                    }

                    spawnPiece() {
                        this.currentPiece = this.nextPiece || this.getRandomPiece();
                        this.nextPiece = this.getRandomPiece();
                        this.currentPos = {
                            x: 4,
                            y: 0
                        };

                        if (this.checkCollision(this.currentPiece, this.currentPos)) {
                            this.gameOver();
                        }
                    }

                    getRandomPiece() {
                        return JSON.parse(JSON.stringify(this.pieces[Math.floor(Math.random() * this.pieces
                            .length)]));
                    }

                    movePiece(dx, dy) {
                        const newPos = {
                            x: this.currentPos.x + dx,
                            y: this.currentPos.y + dy
                        };
                        if (!this.checkCollision(this.currentPiece, newPos)) {
                            this.currentPos = newPos;
                            this.render();
                        }
                    }

                    rotatePiece() {
                        const rotated = this.rotate(this.currentPiece.shape);
                        const rotatedPiece = {
                            ...this.currentPiece,
                            shape: rotated
                        };
                        if (!this.checkCollision(rotatedPiece, this.currentPos)) {
                            this.currentPiece.shape = rotated;
                            this.render();
                        }
                    }

                    dropPiece() {
                        while (!this.checkCollision(this.currentPiece, {
                                x: this.currentPos.x,
                                y: this.currentPos.y + 1
                            })) {
                            this.currentPos.y++;
                        }
                        this.render();
                    }

                    rotate(matrix) {
                        const rows = matrix.length;
                        const cols = matrix[0].length;
                        const rotated = Array(cols).fill().map(() => Array(rows).fill(0));

                        for (let i = 0; i < rows; i++) {
                            for (let j = 0; j < cols; j++) {
                                rotated[j][rows - 1 - i] = matrix[i][j];
                            }
                        }
                        return rotated;
                    }

                    checkCollision(piece, pos) {
                        for (let y = 0; y < piece.shape.length; y++) {
                            for (let x = 0; x < piece.shape[y].length; x++) {
                                if (piece.shape[y][x]) {
                                    const newX = pos.x + x;
                                    const newY = pos.y + y;

                                    if (newX < 0 || newX >= 10 || newY >= 20) return true;
                                    if (newY >= 0 && this.board[newY][newX] !== ' ') return true;
                                }
                            }
                        }
                        return false;
                    }

                    placePiece() {
                        for (let y = 0; y < this.currentPiece.shape.length; y++) {
                            for (let x = 0; x < this.currentPiece.shape[y].length; x++) {
                                if (this.currentPiece.shape[y][x]) {
                                    const boardY = this.currentPos.y + y;
                                    const boardX = this.currentPos.x + x;
                                    if (boardY >= 0) {
                                        this.board[boardY][boardX] = this.currentPiece.color;
                                    }
                                }
                            }
                        }
                        this.clearLines();
                        this.spawnPiece();
                    }

                    clearLines() {
                        let linesCleared = 0;
                        for (let y = this.board.length - 1; y >= 0; y--) {
                            if (this.board[y].every(cell => cell !== ' ')) {
                                this.board.splice(y, 1);
                                this.board.unshift(Array(10).fill(' '));
                                linesCleared++;
                                y++; // Check the same line again
                            }
                        }

                        if (linesCleared > 0) {
                            this.lines += linesCleared;
                            this.score += linesCleared * 100 * this.level;
                            this.level = Math.floor(this.lines / 10) + 1;
                        }
                    }

                    update() {
                        if (!this.gameRunning) return;

                        if (this.checkCollision(this.currentPiece, {
                                x: this.currentPos.x,
                                y: this.currentPos.y + 1
                            })) {
                            this.placePiece();
                        } else {
                            this.currentPos.y++;
                        }
                        this.render();
                    }

                    render() {
                        if (!this.gameRunning) return;

                        // Create display board
                        const display = this.board.map(row => [...row]);

                        // Add current piece
                        if (this.currentPiece) {
                            for (let y = 0; y < this.currentPiece.shape.length; y++) {
                                for (let x = 0; x < this.currentPiece.shape[y].length; x++) {
                                    if (this.currentPiece.shape[y][x]) {
                                        const boardY = this.currentPos.y + y;
                                        const boardX = this.currentPos.x + x;
                                        if (boardY >= 0 && boardY < 20 && boardX >= 0 && boardX < 10) {
                                            display[boardY][boardX] = this.currentPiece.color;
                                        }
                                    }
                                }
                            }
                        }

                        // Clear terminal area (simplified)
                        let output = '';
                        output += `Score: ${this.score} | Level: ${this.level} | Lines: ${this.lines}\n`;
                        output += '┌──────────────────────┐\n';

                        for (let y = 0; y < Math.min(15, display.length); y++) {
                            output += '│';
                            for (let x = 0; x < display[y].length; x++) {
                                output += display[y][x] === ' ' ? '·' : '█';
                                output += display[y][x] === ' ' ? '·' : '█';
                            }
                            output += '│\n';
                        }
                        output += '└──────────────────────┘';

                        this.term.write('\x1b[2J\x1b[H' + output); // Clear screen and move cursor to top
                    }

                    gameOver() {
                        this.gameRunning = false;
                        clearInterval(this.gameInterval);
                        document.removeEventListener('keydown', this.keyHandler);

                        this.term.write('', 'default');
                        this.term.write('🎮 GAME OVER!', 'error');
                        this.term.write(`Final Score: ${this.score}`, 'info');
                        this.term.write(`Lines Cleared: ${this.lines}`, 'info');
                        this.term.write(`Level Reached: ${this.level}`, 'info');
                        this.term.write('Type "tetris" to play again!', 'info');
                        this.term.write('═══════════════════════════════════════', 'default');
                        tetrisGame = null;
                    }

                    quitGame() {
                        this.gameRunning = false;
                        clearInterval(this.gameInterval);
                        document.removeEventListener('keydown', this.keyHandler);

                        this.term.write('', 'default');
                        this.term.write('🎮 Game Quit! Thanks for playing!', 'info');
                        this.term.write(`Final Score: ${this.score}`, 'info');
                        this.term.write('Type "tetris" to play again!', 'info');
                        this.term.write('═══════════════════════════════════════', 'default');
                        tetrisGame = null;
                    }
                }

                // Initial data update
                updateSuspendedCards();
                updateSuspendedCharts();

                // Periodic fake messages and data updates
                const messageInterval = setInterval(() => {
                    if (!mounted || !serverInfo.is_suspended) {
                        clearInterval(messageInterval);
                        return;
                    }

                    // Show random suspended message
                    term.write(suspendedMessages[messageIndex % suspendedMessages.length], 'info');
                    messageIndex++;

                    // Update fake stats and charts
                    updateSuspendedCards();
                    updateSuspendedCharts();
                }, 8000 + Math.random() * 4000); // Random interval 8-12 seconds

                // Store interval for cleanup
                window.suspendedInterval = messageInterval;
            }

            async function mountConsole() {
                if (mounted || mounting) {
                    console.log('Mount console called but already mounted/mounting:', {
                        mounted,
                        mounting
                    });
                    return; // Prevent double mounting
                }

                // Check if elements are already initialized and clean them up
                if (elementsAlreadyInitialized()) {
                    console.log('Elements already initialized, cleaning up before mounting...');
                    cleanupExistingElements();
                    // Small delay to ensure cleanup completes
                    await new Promise(resolve => setTimeout(resolve, 100));
                }

                console.log('Starting console mount...');
                mounting = true; // Set mounting flag

                try {

                    // Check if server is suspended and handle differently
                    if (serverInfo.is_suspended) {
                        console.log('Mounting suspended console...');
                        await mountSuspendedConsole();
                        mounted = true;
                        mounting = false;
                        console.log('Suspended console mounted successfully');
                        return;
                    }

                    history = new History(inputEl());
                    term = new TerminalManager('terminal');
                    charts = new ChartManager();
                    ws = new WSClient(term, charts);

                    function onStartClick() {
                        ws.setState('start');
                    }

                    function onStopClick() {
                        ws.setState('stop');
                    }

                    function onRestartClick() {
                        ws.setState('restart');
                    }

                    function onKillClick() {
                        ws.setState('kill');
                    }

                    // wire ENTER on console input (support keydown & keypress)
                    const inputElement = inputEl();

                    // Track event listeners for cleanup detection
                    if (!inputElement._consoleEventListeners) {
                        inputElement._consoleEventListeners = [];
                    }

                    inputElement.addEventListener('keydown', onCommand);
                    inputElement.addEventListener('keypress', onCommand);
                    inputElement.setAttribute('data-console-listeners', 'true');

                    // Track the listeners we just added
                    inputElement._consoleEventListeners.push({
                        type: 'keydown',
                        listener: onCommand
                    }, {
                        type: 'keypress',
                        listener: onCommand
                    });

                    // (re)select buttons and wire actions
                    btnStart = document.querySelector('[data-action="start"]');
                    btnStop = document.querySelector('[data-action="stop"]');
                    btnRestart = document.querySelector('[data-action="restart"]');
                    btnKill = document.querySelector('[data-action="kill"]');
                    btnStart.addEventListener('click', onStartClick);
                    btnStop.addEventListener('click', onStopClick);
                    btnRestart.addEventListener('click', onRestartClick);
                    btnKill.addEventListener('click', onKillClick);

                    await ws.connect({{ $service->id }});
                    term.write('Waiting for server…', 'info');
                    updateButtons(serverInfo.status);

                    mounted = true;
                    console.log('Console mounted successfully');
                } catch (error) {
                    console.error('Error mounting console:', error);
                    term?.write('Error initializing console: ' + error.message, 'error');
                } finally {
                    mounting = false; // Always clear mounting flag
                }
            }

            function unmountConsole() {
                if (!mounted) {
                    console.log('Unmount console called but not mounted');
                    return;
                }

                console.log('Unmounting console...');
                mounted = false;
                mounting = false; // Reset mounting flag

                // Clean up suspended server interval if it exists
                if (window.suspendedInterval) {
                    clearInterval(window.suspendedInterval);
                    window.suspendedInterval = null;
                }

                // Clean up Tetris game if running
                if (tetrisGame && tetrisGame.gameRunning) {
                    try {
                        tetrisGame.quitGame();
                    } catch (e) {
                        console.warn('Error quitting Tetris game:', e);
                    }
                }
                tetrisGame = null; // Clear reference

                // Clean up regular console components if they exist
                if (ws) {
                    if (ws.socket) {
                        try {
                            ws.socket.close();
                            // Clear global reference
                            if (window.activeConsoleWebSocket === ws.socket) {
                                window.activeConsoleWebSocket = null;
                            }
                        } catch (e) {
                            console.warn('Error closing WebSocket:', e);
                        }
                    }
                    // Clear the netBase to prevent further updates
                    ws.netBase = null;
                }

                if (term && term.term) {
                    try {
                        term.term.dispose();
                    } catch (e) {
                        console.warn('Error disposing terminal:', e);
                    }
                }

                if (charts) {
                    try {
                        if (charts.cpu) charts.cpu.destroy();
                        if (charts.ram) charts.ram.destroy();
                        if (charts.net) charts.net.destroy();
                    } catch (e) {
                        console.warn('Error destroying charts:', e);
                    }
                }

                // Remove event listeners with error handling
                const input = inputEl();
                if (input) {
                    try {
                        if (typeof onCommand === 'function') {
                            input.removeEventListener('keydown', onCommand);
                            input.removeEventListener('keypress', onCommand);
                        }
                        if (typeof onSuspendedCommand === 'function') {
                            input.removeEventListener('keydown', onSuspendedCommand);
                            input.removeEventListener('keypress', onSuspendedCommand);
                        }
                        input.removeAttribute('data-console-listeners');
                        if (input._consoleEventListeners) {
                            input._consoleEventListeners = [];
                        }
                    } catch (e) {
                        console.warn('Error removing input event listeners:', e);
                    }
                }

                // Clear terminal initialization marker
                const terminalEl = document.getElementById('terminal');
                if (terminalEl) {
                    terminalEl.removeAttribute('data-console-initialized');
                }

                // Clear references
                history = null;
                term = null;
                charts = null;
                ws = null;
                onSuspendedCommand = null;

                console.log('Console unmounted successfully');
            }

            function onCommand(e) {
                // only react to Enter key
                if (e.key !== 'Enter' && e.keyCode !== 13) return;
                e.preventDefault();
                const cmd = inputEl().value.trim();
                if (!cmd || !ws) return;
                console.log('Sending command:', cmd);
                ws.sendCommand(cmd);
                history.commit(cmd);
                inputEl().value = '';
            }

            // Cleanup function to prevent memory leaks and multiple intervals
            function cleanup() {
                console.log('Cleaning up console resources');

                // Clean up watch interval
                if (window.consoleWatchInterval) {
                    clearInterval(window.consoleWatchInterval);
                    window.consoleWatchInterval = null;
                }

                // Clean up mutation observer
                if (mutationObserver) {
                    mutationObserver.disconnect();
                    mutationObserver = null;
                }

                // Unmount console
                if (mounted) {
                    unmountConsole();
                }
            }

            // Clean up on page unload/navigation
            window.addEventListener('beforeunload', cleanup);
            window.addEventListener('pagehide', cleanup);

            // Also clean up if the script runs multiple times (e.g., hot reload during development)
            if (window.consoleCleanup) {
                window.consoleCleanup();
            }
            window.consoleCleanup = cleanup;

            // Clean up any existing interval (in case script runs multiple times)
            if (window.consoleWatchInterval) {
                clearInterval(window.consoleWatchInterval);
                window.consoleWatchInterval = null;
            }

            // Check for and clean up any existing console elements before initial mount
            // Be more aggressive about cleanup on page load
            let attempts = 0;
            const maxAttempts = 3;

            while (attempts < maxAttempts && elementsAlreadyInitialized()) {
                console.log(
                    `Detected existing console elements on page load (attempt ${attempts + 1}), cleaning up...`);
                cleanupExistingElements(true); // Force cleanup
                attempts++;
                // Small delay to ensure cleanup completes
                await new Promise(resolve => setTimeout(resolve, 100 + (attempts * 50)));
            }

            if (attempts >= maxAttempts) {
                console.warn('Max cleanup attempts reached, forcing fresh start...');
                // Last resort: clear everything we can find
                const terminalEl = document.getElementById('terminal');
                if (terminalEl && terminalEl.parentNode) {
                    const newTerminal = document.createElement('div');
                    newTerminal.id = 'terminal';
                    newTerminal.className = terminalEl.className;
                    terminalEl.parentNode.replaceChild(newTerminal, terminalEl);
                }
            }

            // Initial mount with error handling
            try {
                await mountConsole();
            } catch (error) {
                console.error('Failed to mount console:', error);
            }

            // Watch #terminal and tear down/re-mount as needed
            // Store interval globally to prevent multiple intervals
            window.consoleWatchInterval = setInterval(() => {
                const now = Date.now();
                // Debounce: only check every 500ms minimum
                if (now - lastTerminalCheck < 500) return;
                lastTerminalCheck = now;

                const alive = !!document.getElementById('terminal');

                if (!alive && mounted && !mounting) {
                    console.log('Terminal element not found, unmounting console');
                    unmountConsole();
                }
                if (alive && !mounted && !mounting) {
                    // Always check for stale elements when remounting, be more aggressive
                    if (elementsAlreadyInitialized()) {
                        console.log('Found stale console elements, performing aggressive cleanup...');
                        cleanupExistingElements(true); // Force cleanup

                        // Double-check after cleanup
                        if (elementsAlreadyInitialized()) {
                            console.warn('Elements still detected after cleanup, forcing DOM reset...');

                            // Nuclear option: replace the terminal element entirely
                            const terminalEl = document.getElementById('terminal');
                            if (terminalEl && terminalEl.parentNode) {
                                const newTerminal = document.createElement('div');
                                newTerminal.id = 'terminal';
                                newTerminal.className = terminalEl.className;
                                terminalEl.parentNode.replaceChild(newTerminal, terminalEl);
                            }
                        }

                        // Additional delay after aggressive cleanup
                        setTimeout(() => {
                            if (!mounted && !mounting) {
                                console.log('Attempting mount after aggressive cleanup...');
                                mountConsole().catch(error => {
                                    console.error(
                                        'Failed to remount console after cleanup:',
                                        error);
                                    mounting = false;
                                });
                            }
                        }, 200);
                        return;
                    }

                    console.log('Terminal element found, mounting console');
                    mountConsole().catch(error => {
                        console.error('Failed to remount console:', error);
                        mounting = false; // Reset flag on error
                    });
                }
            }, 2000); // Increased interval to 2 seconds to reduce checking frequency

            // Add mutation observer to detect DOM changes that might indicate stale elements
            let mutationObserver = null;

            function setupMutationObserver() {
                if (mutationObserver) {
                    mutationObserver.disconnect();
                }

                mutationObserver = new MutationObserver((mutations) => {
                    let shouldCheck = false;

                    mutations.forEach((mutation) => {
                        // Check if terminal or related elements were modified
                        if (mutation.type === 'childList') {
                            const target = mutation.target;
                            if (target.id === 'terminal' ||
                                target.closest('#terminal') ||
                                target.id === 'console-input' ||
                                target.closest('#console-input')) {
                                shouldCheck = true;
                            }
                        }
                    });

                    if (shouldCheck && !mounted && !mounting) {
                        console.log('DOM mutation detected, checking for stale elements...');
                        if (elementsAlreadyInitialized()) {
                            console.log('Stale elements detected via mutation observer, cleaning up...');
                            cleanupExistingElements(true);
                        }
                    }
                });

                // Observe the entire console section
                const consoleSection = document.getElementById('console-section');
                if (consoleSection) {
                    mutationObserver.observe(consoleSection, {
                        childList: true,
                        subtree: true,
                        attributes: false
                    });
                }
            }

            // Setup mutation observer
            setupMutationObserver();

            // Initial mount with error handling
            try {
                await mountConsole();
            } catch (error) {
                console.error('Failed to mount console:', error);
            }

            // Watch #terminal and tear down/re-mount as needed
            // Store interval globally to prevent multiple intervals
            window.consoleWatchInterval = setInterval(() => {
                const now = Date.now();
                // Debounce: only check every 500ms minimum
                if (now - lastTerminalCheck < 500) return;
                lastTerminalCheck = now;

                const alive = !!document.getElementById('terminal');

                if (!alive && mounted && !mounting) {
                    console.log('Terminal element not found, unmounting console');
                    unmountConsole();
                }
                if (alive && !mounted && !mounting) {
                    // Always check for stale elements when remounting, be more aggressive
                    if (elementsAlreadyInitialized()) {
                        console.log('Found stale console elements, performing aggressive cleanup...');
                        cleanupExistingElements(true); // Force cleanup

                        // Double-check after cleanup
                        if (elementsAlreadyInitialized()) {
                            console.warn('Elements still detected after cleanup, forcing DOM reset...');

                            // Nuclear option: replace the terminal element entirely
                            const terminalEl = document.getElementById('terminal');
                            if (terminalEl && terminalEl.parentNode) {
                                const newTerminal = document.createElement('div');
                                newTerminal.id = 'terminal';
                                newTerminal.className = terminalEl.className;
                                terminalEl.parentNode.replaceChild(newTerminal, terminalEl);
                            }
                        }

                        // Additional delay after aggressive cleanup
                        setTimeout(() => {
                            if (!mounted && !mounting) {
                                console.log('Attempting mount after aggressive cleanup...');
                                mountConsole().catch(error => {
                                    console.error(
                                        'Failed to remount console after cleanup:',
                                        error);
                                    mounting = false;
                                });
                            }
                        }, 200);
                        return;
                    }

                    console.log('Terminal element found, mounting console');
                    mountConsole().catch(error => {
                        console.error('Failed to remount console:', error);
                        mounting = false; // Reset flag on error
                    });
                }
            }, 500); // Increased interval to 2 seconds to reduce checking frequency
            Livewire.hook('element.updated', (el, component) => {
                console.log('Livewire element updated:', el, component);
            });
            Livewire.hook('element.removed', (el, component) => {
                console.log('Livewire element removed:', el, component);
            })


        })();
    </script>
@endscript
