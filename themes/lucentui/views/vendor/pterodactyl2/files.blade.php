<!-- replace CodeMirror imports with ACE -->
@assets
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.42.0/ace.js"
        integrity="sha512-ZFVNcFjotJAKSBDjAqsPOVW4GIFy4umHM80GBSScEKRFd93punBrbx6YqH/Gds2o1LbUwEytGBJ4QxpiS/UekQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endassets

<section id="files-section"
    class="relative flex flex-col h-full w-full
           p-6 rounded-lg mx-auto
           overflow-hidden">

    <!-- spinner overlay while loading file list -->
    <div id="files-spinner"
        class="absolute inset-0 flex items-center justify-center
               bg-[#1c1c1c]/[var(--bg-opacity)] [--bg-opacity:75%] z-10 hidden">
        <div class="w-12 h-12 border-4 border-t-blue-500 border-gray-600 rounded-full animate-spin"></div>
    </div>

    <!-- drop overlay during drag & drop -->
    <div id="files-drop-overlay"
        class="absolute inset-0 flex items-center justify-center
               bg-black/50 text-white text-lg font-semibold z-20 hidden pointer-events-none">
        Drop files to upload
    </div>

    <!-- breadcrumb + search + view toggle + new/delete -->
    <div class="flex items-center justify-between mb-4">
        <!-- make breadcrumb grow to push controls right -->
        <nav id="files-breadcrumb" class="flex-1 text-sm text-base flex flex-wrap gap-1"></nav>
        <!-- control group aligned right -->
        <div class="flex items-center gap-2 ml-4">
            <input id="file-search" type="text" placeholder="Search…"
                class="px-3 py-1 bg-gray-800/25 border border-neutral rounded-lg text-base focus:outline-none focus:ring-2 focus:ring-blue-500" />

            <!-- ADD these two toggle buttons -->
            <button id="view-grid" class="p-2 bg-neutral/20 rounded hover:bg-neutral/30 transition" title="Grid view">
                <!-- grid icon, e.g.: -->
                <svg class="w-5 h-5 text-text-base" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 3h4v4H4V3zM4 9h4v4H4V9zM4 15h4v2H4v-2zM10 3h6v6h-6V3zM10 11h6v6h-6v-6z" />
                </svg>
            </button>
            <button id="view-list" class="p-2 bg-neutral/20 rounded hover:bg-neutral/30 transition" title="List view">
                <!-- list icon, e.g.: -->
                <svg class="w-5 h-5 text-text-base" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 5h14v2H3V5zM3 9h14v2H3V9zM3 13h14v2H3V13z" />
                </svg>
            </button>

            <!-- NEW: create dropdown menu -->
            <div class="relative">
                <button id="create-menu" class="p-2 bg-green-200 text-gray-700 rounded hover:bg-green-300 transition">
                    <!-- plus icon to indicate “add” -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
                    </svg>
                </button>
                <div id="create-menu-items"
                    class="absolute right-0 mt-1 w-48
                           backdrop-blur-lg bg-gray-900/80 border border-neutral
                           text-gray-200 rounded-lg shadow-xl
                           overflow-hidden divide-y divide-gray-800
                           hidden z-50">
                    <button id="upload-file" class="block w-full px-4 py-2 text-left text-gray-200 hover:bg-neutral/10">
                        Upload File…
                    </button>
                    <button id="create-file" class="block w-full px-4 py-2 text-left text-gray-200 hover:bg-neutral/10">
                        New File
                    </button>
                    <button id="create-folder"
                        class="block w-full px-4 py-2 text-left text-gray-200 hover:bg-neutral/10">
                        New Folder
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- main file list area -->
    <div id="files-container" class="flex-1 w-full min-h-[790]
               overflow-auto">
    </div>

</section>

<!-- modal editor (hidden by default) -->
<div id="editor-modal" class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-black/40 hidden z-[9999]">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-4 h-3/4 flex flex-col w-3/4 mx-4 relative">
        <!-- spinner overlay while loading editor content -->
        <div id="editor-spinner"
            class="absolute inset-0 flex items-center justify-center
                   bg-black/50 z-10 hidden">
            <div class="w-12 h-12 border-4 border-t-blue-500 border-gray-600 rounded-full animate-spin"></div>
        </div>
        <div class="flex justify-between items-center mb-2">
            <h4 id="editor-filename" class="text-gray-900 dark:text-gray-200"></h4>
            <button id="editor-close" class="text-gray-500 dark:text-gray-200 hover:text-red-500 text-3xl leading-none">&times;</button>
        </div>
        <div id="file-editor" class="flex-1"></div>
        <div class="mt-2 flex justify-end gap-2">
            <button id="editor-save" class="px-4 py-1 bg-blue-600 rounded">Save</button>
            <button id="editor-cancel" class="px-4 py-1 bg-gray-600 rounded">Cancel</button>
        </div>
    </div>
</div>

<!-- Add this custom create modal below your editor modal -->
<div id="create-modal" class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-black/40 hidden z-[9999]">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-4 w-80 mx-4 relative">
        <h3 id="create-title" class="text-gray-900 dark:text-gray-200 text-lg mb-2">New Item</h3>
        <input id="create-input" type="text" placeholder="Enter name…"
            class="w-full px-3 py-1 mb-4 bg-gray-100 dark:bg-gray-800 rounded text-gray-900 dark:text-gray-200 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        <div class="flex justify-end gap-2">
            <button id="create-cancel" class="px-4 py-1 bg-gray-600 rounded hover:bg-gray-700">Cancel</button>
            <button id="create-confirm" class="px-4 py-1 bg-blue-600 rounded hover:bg-blue-700">Create</button>
        </div>
    </div>
</div>

<!-- add this right below your create-modal, before the @script -->
<div id="file-context-menu"
    class="hidden backdrop-blur-lg bg-gray-900/80 border border-neutral text-gray-200 rounded-lg shadow-xl
           overflow-hidden divide-y divide-gray-800"
    style="position: fixed; z-index: 9999; min-width: 180px;">
    <ul class="divide-y divide-neutral/50">
        <li id="ctx-open" class="px-4 py-2 hover:bg-neutral/20 cursor-pointer hidden">Edit</li>
        <li id="ctx-download" class="px-4 py-2 hover:bg-neutral/20 cursor-pointer">Download</li>
        <li id="ctx-rename" class="px-4 py-2 hover:bg-neutral/20 cursor-pointer">Rename</li>
        <li id="ctx-copy" class="px-4 py-2 hover:bg-neutral/20 cursor-pointer">Copy</li>
        <li id="ctx-compress" class="px-4 py-2 hover:bg-neutral/20 cursor-pointer">Compress</li>
        <li id="ctx-decompress" class="px-4 py-2 hover:bg-neutral/20 cursor-pointer">Decompress</li>
        <li id="ctx-delete" class="px-4 py-2 hover:bg-red-600 cursor-pointer">Delete</li>
    </ul>
</div>

<!-- hidden file input for picker upload -->
<input id="upload-input" type="file" multiple class="hidden" />

<!-- Toast container -->
<div id="toast-container" class="fixed top-4 right-4 space-y-2 z-50 pointer-events-none"></div>

@script
    <script type="module">
        (async () => {
            const serviceId = {{ $service->id }};

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
                const bgColor = type === 'success' 
                    ? 'bg-green-500' 
                    : type === 'error' 
                        ? 'bg-red-500' 
                        : 'bg-gray-800';

                toast.className = [
                    bgColor,
                    'text-white px-4 py-2 rounded shadow',
                    'pointer-events-auto transition-opacity duration-500'
                ].join(' ');

                toast.textContent = message;
                container.appendChild(toast);

                // Fade out and remove after 3.5s
                setTimeout(() => {
                    toast.classList.add('opacity-0');
                    setTimeout(() => toast.remove(), 500);
                }, 3000);
            }

            // --- Event Listener Tracking ---
            let eventListeners = [];

            // Helper function to add tracked event listeners
            function addTrackedEventListener(element, type, listener) {
                if (!element) return;
                
                element.addEventListener(type, listener);
                eventListeners.push({ element, type, listener });
            }

            // Function to cleanup event listeners
            function cleanupEventListeners() {
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
            }

            const ICONS = {
                file: `
  <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-base/50" fill="none"
	   viewBox="0 0 24 24" stroke="currentColor">
	<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
		  d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z" />
	<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
		  d="M14 2v6h6" />
  </svg>`,
                folder: `
  <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-400" fill="none"
	   viewBox="0 0 24 24" stroke="currentColor">
	<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
		  d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v1H3V7z" />
	<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
		  d="M3 10h18v9a2 2 0 01-2 2H5a2 2 0 01-2-2v-9z" />
  </svg>`,
            };
            const modes = {
                'js': 'javascript',
                'py': 'python',
                'java': 'java',
                'html': 'html',
                'css': 'css',
                'json': 'json',
                'txt': 'text',
                'md': 'markdown',
                'yml': 'yaml',
                'xml': 'xml',
                "php": 'php',
                'cpp': 'c_cpp',
                'c': 'c_cpp',
                'go': 'golang',
                'rb': 'ruby',
                'sh': 'sh',
                'rs': 'rust',
                'lua': 'lua',
                'kt': 'kotlin',
                'pl': 'perl',
                'ps1': 'powershell',
                'bat': 'batchfile',
                'csv': 'csv',
                'log': 'text',
                'mdx': 'markdown', // MDX files
                'vue': 'vue',
                'svelte': 'svelte',
                'ts': 'typescript',
                'tsx': 'typescript',

                // add more as needed
            };

            function AceModeForExtension(ext) {

                return modes[ext] || 'text'; // default to plain text if unknown
            }

            function formatBytes(bytes, dec = 2) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024,
                    dm = Math.max(0, dec),
                    sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
            }

            class ApiClient {
                constructor(serviceId) {
                    this.serviceId = serviceId;
                    this.baseUrl = `/services/${serviceId}/files`;
                }

                async request(endpoint, method = 'GET', body = null, raw = false) {
                    // show spinner on every API call
                    const spinner = document.getElementById('files-spinner');
                    if (spinner) spinner.classList.remove('hidden');
                    
                    try {
                        // build headers
                        const headers = {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                        };
                        if (!endpoint.startsWith('/upload')) {
                            headers['Content-Type'] = 'application/json';
                        }

                        // perform request
                        const response = await fetch(`${this.baseUrl}${endpoint}`, {
                            method,
                            headers,
                            body,
                        });
                        
                        if (!response.ok) {
                            const errorText = await response.text();
                            throw new Error(`API error: ${response.status} ${response.statusText} - ${errorText}`);
                        }
                        
                        if (raw) return response;
                        
                        const data = await response.json();
                        if (data.error) {
                            throw new Error(`API error: ${data.error}`);
                        }
                        
                        return data;
                    } catch (error) {
                        console.error('API request failed:', error);
                        showToast(`Request failed: ${error.message}`, 'error');
                        throw error;
                    } finally {
                        // hide spinner after request completes or errors
                        if (spinner) spinner.classList.add('hidden');
                    }
                }

                listFiles(path = '/') {
                    return this.request(`?directory=${encodeURIComponent(path)}`);
                }

                createFile(path) {
                    return this.request(`/write?file=${encodeURIComponent(path)}`, 'POST', "");
                }

                createFolder(name, path) {
                    return this.request('/create-folder', 'POST', JSON.stringify({
                        root: path,
                        name: name
                    }));
                }

                deleteFiles(path = '/', files = []) {
                    return this.request('/delete', 'POST', JSON.stringify({
                        root: path,
                        files: files
                    }));
                }

                readFile(path) {
                    return this.request(`/content?file=${encodeURIComponent(path)}`, 'GET', null, true)
                        .then(response => response.text());
                }

                writeFile(path, content) {
                    return this.request(`/write?file=${encodeURIComponent(path)}`, 'POST', content);
                }

                async uploadFile(file, path) {
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('root', path);

                    const uploadResponse = await this.request('/upload', 'POST', formData);



                    if (!uploadResponse.ok) {
                        throw new Error(`File upload failed: ${uploadResponse.statusText}`);
                    }

                    return uploadResponse;
                }

                compressFiles(files, path) {
                    return this.request('/compress', 'POST', JSON.stringify({
                        files: files,
                        root: path
                    }));
                }

                decompressFile(file, path) {
                    return this.request('/decompress', 'POST', JSON.stringify({
                        file: file,
                        root: path
                    }));
                }
                copyFile(source) {
                    return this.request('/copy', 'POST', JSON.stringify({
                        location: source,
                    }));
                }

                getDownloadLink(filePath) {
                    return this.request(`/download?file=${encodeURIComponent(filePath)}`);
                }

                renameFile(old, newp, root) {
                    return this.request('/rename', 'PUT', JSON.stringify({
                        old: old,
                        new: newp,
                        root: root
                    }));
                }
            }

            const apiClient = new ApiClient(serviceId);

            // -- File List Management -------------------------------------------

            // --- Mount / Unmount System with Event Listener Tracking ---
            let mountedFiles = false;
            let mounting = false;

            function detectFiles() {
                const alive = !!document.getElementById('files-container');
                if (alive && !mountedFiles && !mounting) {
                    mountFiles().catch(error => {
                        console.error('Failed to mount files:', error);
                        showToast('Failed to initialize file manager', 'error');
                    });
                }
                if (!alive && mountedFiles) {
                    unmountFiles();
                }
            }
            
            detectFiles();
            setInterval(detectFiles, 1000);

            async function mountFiles() {
                if (mountedFiles || mounting) {
                    console.log('Mount files called but already mounted/mounting:', { mountedFiles, mounting });
                    return;
                }

                console.log('Starting files mount...');
                mounting = true;

                try {

                // --- state ---
                let cwd = '/';
                let files = [];
                let viewMode = 'list'; // default view
                const selected = new Set();
                // track last-clicked index for shift‐range selection
                let lastSelectedIndex = null;

                // helper to persist per-directory view
                const getView = dir => localStorage.getItem(`viewMode:${dir}`) || 'list';
                const setView = (dir, mode) => localStorage.setItem(`viewMode:${dir}`, mode);

                // --- DOM refs ---
                const container = document.getElementById('files-container');
                const spinner = document.getElementById('files-spinner');
                const dropOverlay = document.getElementById('files-drop-overlay');
                const breadcrumb = document.getElementById('files-breadcrumb');
                const searchInput = document.getElementById('file-search');
                const btnGrid = document.getElementById('view-grid');
                const btnList = document.getElementById('view-list');
                const createMenu = document.getElementById('create-menu');
                const createItems = document.getElementById('create-menu-items');
                const createModal = document.getElementById('create-modal');
                const createTitle = document.getElementById('create-title');
                const createInput = document.getElementById('create-input');
                const createOK = document.getElementById('create-confirm');
                const createCancel = document.getElementById('create-cancel');
                const contextMenu = document.getElementById('file-context-menu');
                const ctxDownload = document.getElementById('ctx-download');
                const ctxOpen = document.getElementById('ctx-open');
                const ctxRename = document.getElementById('ctx-rename');
                const ctxCopy = document.getElementById('ctx-copy');
                const ctxCompress = document.getElementById('ctx-compress');
                const ctxDecompress = document.getElementById('ctx-decompress');
                const ctxDelete = document.getElementById('ctx-delete');
                let ctxTargets = [];

                // hide on click elsewhere
                document.addEventListener('click', () => contextMenu.classList.add('hidden'));

                function showContextMenu(event, name) {
                    // if right‐clicked item isn’t in selection, reset to that one
                    if (!selected.has(name)) {
                        selected.clear();
                        selected.add(name);
                        renderFiles();
                    }
                    ctxTargets = Array.from(selected);
                    // show "Edit" only when exactly one file is selected
                    const openOpt = document.getElementById('ctx-open');
                    const selName = ctxTargets[0];
                    const fileObj = files.find(f => f.attributes.name === selName);
                    if (ctxTargets.length === 1 && fileObj?.attributes.is_file) {
                        openOpt.classList.remove('hidden');
                    } else {
                        openOpt.classList.add('hidden');
                    }

                    // disable download for folders
                    const dlOpt = document.getElementById('ctx-download');
                    if (fileObj?.attributes.is_file) {
                        dlOpt.classList.remove('hidden');
                    } else {
                        dlOpt.classList.add('hidden');
                    }

                    // Calculate position relative to the files container
                    const filesSection = document.getElementById('files-section');
                    const sectionRect = filesSection.getBoundingClientRect();
                    
                    console.log('Debug positioning:', {
                        mouse: { clientX: event.clientX, clientY: event.clientY },
                        section: sectionRect,
                        calculated: {
                            left: event.clientX - sectionRect.left,
                            top: event.clientY - sectionRect.top
                        }
                    });
                    
                    // Position relative to the files section
                    contextMenu.style.position = 'absolute';
                    contextMenu.style.zIndex = '9999';
                    contextMenu.style.left = (event.clientX - sectionRect.left + 2) + 'px';
                    contextMenu.style.top = (event.clientY - sectionRect.top + 2) + 'px';
                    
                    contextMenu.classList.remove('hidden');
                }

                // --- helpers ---
                const showSpinner = () => spinner.classList.remove('hidden');
                const hideSpinner = () => spinner.classList.add('hidden');

                function updateControls() {}

                function renderBreadcrumbs() {
                    // normalize cwd and split into segments
                    cwd = (cwd.replace(/\/\/+/g, '/').replace(/\/+$/, '') || '/');
                    breadcrumb.innerHTML = '';
                    // derive parts for breadcrumb (empty array at root)
                    const parts = cwd === '/' ? [] : cwd.slice(1).split('/');

                    // root “home” button
                    const home = document.createElement('button');
                    home.textContent = 'home';
                    home.className =
                        `px-1 ${parts.length === 0 ? 'text-base font-bold' : 'text-base hover:text-base-50'}`;
                    home.onclick = () => {
                        cwd = '/';
                        load();
                    };

                    // allow dropping onto “home” to move files to root
                    home.addEventListener('dragenter', e => {
                        if (!Array.from(e.dataTransfer.types).includes('application/json')) return;
                        e.preventDefault();
                        home.classList.add('bg-blue-500/20');
                    });
                    home.addEventListener('dragover', e => {
                        if (!Array.from(e.dataTransfer.types).includes('application/json')) return;
                        e.preventDefault();
                    });
                    home.addEventListener('dragleave', () => home.classList.remove('bg-blue-500/20'));
                    home.addEventListener('drop', async e => {
                        if (!Array.from(e.dataTransfer.types).includes('application/json')) return;
                        e.preventDefault();
                        home.classList.remove('bg-blue-500/20');
                        const names = JSON.parse(e.dataTransfer.getData('application/json'));
                        for (const n of names) {
                            const old = cwd.endsWith('/') ? cwd : `${cwd}/${n}`;
                            // add ../ depending on how many segments we have
                            let newp = '';
                            // if cwd is /a/b/c and we drop on home, newp will be /n
                            for (let j = 0; j < parts.length; j++) {
                                newp += '../';
                            }
                            // newp is now the full path to the root


                            await apiClient.renameFile(old, `${newp}${n}`, "/");
                        }
                        await load();
                    });

                    breadcrumb.appendChild(home);

                    // render each segment
                    if (cwd !== '/') {
                        parts.forEach((p, i) => {
                            const sep = document.createElement('span');
                            sep.textContent = ' / ';
                            sep.className = 'text-gray-600';
                            breadcrumb.appendChild(sep);

                            const btn = document.createElement('button');
                            btn.textContent = p;
                            btn.className =
                                `px-1 ${i === parts.length - 1 ? 'text-base font-bold' : 'text-base hover:text-base'}`;
                            btn.onclick = () => {
                                cwd = '/' + parts.slice(0, i + 1).join('/');
                                load();
                            };
                            // allow dropping onto this crumb to move files here
                            btn.addEventListener('dragenter', e => {
                                if (!Array.from(e.dataTransfer.types).includes(
                                        'application/json')) return;
                                e.preventDefault();
                                btn.classList.add('bg-blue-500/20');
                            });
                            btn.addEventListener('dragover', e => {
                                if (!Array.from(e.dataTransfer.types).includes(
                                        'application/json')) return;
                                e.preventDefault();
                            });
                            btn.addEventListener('dragleave', () => btn.classList.remove(
                                'bg-blue-500/20'));
                            btn.addEventListener('drop', async e => {
                                if (!Array.from(e.dataTransfer.types).includes(
                                        'application/json')) return;
                                e.preventDefault();
                                btn.classList.remove('bg-blue-500/20');
                                const names = JSON.parse(e.dataTransfer.getData(
                                    'application/json'));
                                //const target = '/' + parts.slice(0, i + 1).join('/');
                                // prefix new path with "../"depending on current cwd and how much folder we go down
                                let target = ""
                                // add .. for each segment up to the current crumb
                                for (let j = 0; j <= i; j++) {
                                    target += '../';
                                }
                                // target is now the full path to the folder
                                // e.g. if cwd is /a/b/c and we drop on /a/b, target will be /a/b/
                                // move each file to this path

                                for (const n of names) {
                                    await apiClient.renameFile(n, `${target}/${n}`, cwd);
                                }
                                await load();
                            });

                            breadcrumb.appendChild(btn);
                        });
                    }
                }

                function renderFiles() {
                    const term = searchInput.value.toLowerCase();
                    container.innerHTML = '';
                    // layout container — add extra spacing between items
                    container.classList.remove(
                        'grid', 'grid-cols-[repeat(auto-fill,minmax(160px,1fr))]', 'gap-6',
                        'flex', 'flex-col', 'divide-y', 'divide-gray-700', 'space-y-4'
                    );
                    if (viewMode === 'grid') {
                        container.classList.add(
                            'grid',
                            'auto-rows-min', // allow each row to size to its own content
                            'grid-cols-[repeat(auto-fill,minmax(160px,1fr))]', // min width 160px
                            'gap-6' // increased gap for grid cards
                        );
                    } else {
                        container.classList.add(
                            'flex', 'flex-col',
                            'divide-y', 'divide-gray-700',
                            'space-y-4' // add vertical margin between list rows
                        );
                    }
                    files
                        .filter(f => f.attributes.name.toLowerCase().includes(term))
                        .forEach((f, idx) => {
                            const {
                                name,
                                is_file,
                                size,
                                modified_at
                            } = f.attributes;
                            const el = document.createElement('div');
                            // card vs row styling
                            // base (add cursor-pointer for file/folder entries)
                            const common =
                                'flex items-center px-4 py-2 rounded-lg transition cursor-pointer';
                            // if selected, override pastel Bg with solid tinted blue
                            const selBg = selected.has(name)
                                // stronger blue tint, thicker ring and offset for contrast
                                ?
                                'bg-blue-400/50 ring-4 ring-blue-400 ring-offset-2 ring-offset-[#1c1c1c]' :
                                '';
                            // card vs row (including hover & blur only when not selected)
                            // for grid cards, constrain height and hide overflow
                            const modeCls = viewMode === 'grid' ?
                                `flex-col gap-2 text-center max-h-48 overflow-hidden ${
                                  selected.has(name)
                                    ? '' // no blur when selected
                                    : 'bg-gray-900/70 bg-white/5 hover:bg-white/10 backdrop-blur-sm'
                                }` :
                                `w-full ${
                                  selected.has(name)
                                    ? ''
                                    : 'bg-white/5 hover:bg-white/10 backdrop-blur-sm'
                                }`;

                            el.className = [common, modeCls, selBg].filter(Boolean).join(' ');
                            // metadata + icon + name
                            const iconHtml = is_file ? ICONS.file : ICONS.folder;
                            if (viewMode === 'grid') {
                                el.innerHTML = `
                                    <div class="mb-2">${iconHtml}</div>
                                    <div class="font-medium w-full overflow-hidden whitespace-nowrap truncate">
                                        ${name}
                                    </div>
                                    <div class="text-xs text-base/50 mt-1">
                                        ${formatBytes(size)} · ${new Date(modified_at).toLocaleDateString()}
                                    </div>
                                `;
                            } else {
                                el.innerHTML = `
                                    <div class="flex w-full items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <div class="mr-2">${iconHtml}</div>
                                            <div class="truncate">${name}</div>
                                        </div>
                                        <div class="flex items-center space-x-4 text-xs text-base/50">
                                            <div>${formatBytes(size)}</div>
                                            <div>${new Date(modified_at).toLocaleDateString()}</div>
                                        </div>
                                    </div>
                                `;
                            }
                            el.onclick = e => {
                                if (e.shiftKey && lastSelectedIndex !== null) {
                                    // shift‐click: select range between last and current
                                    const visible = files
                                        .filter(x => x.attributes.name.toLowerCase().includes(term));
                                    const start = Math.min(lastSelectedIndex, idx);
                                    const end = Math.max(lastSelectedIndex, idx);
                                    selected.clear();
                                    for (let i = start; i <= end; i++) {
                                        selected.add(visible[i].attributes.name);
                                    }
                                } else if (e.ctrlKey) {
                                    // ctrl‐click toggles individual
                                    selected.has(name) ?
                                        selected.delete(name) :
                                        selected.add(name);
                                } else {
                                    // normal click: single select
                                    selected.clear();
                                    selected.add(name);
                                }
                                lastSelectedIndex = idx;
                                renderFiles();
                            };
                            el.ondblclick = async () => {
                                if (!is_file) {
                                    cwd = cwd.endsWith('/') ? cwd + name : `${cwd}/${name}`;
                                    await load();
                                } else {
                                    try {
                                        // Try to load into ACE editor
                                        cwd = cwd.endsWith('/') ? cwd : `${cwd}/`;

                                        const res = await apiClient.readFile(`${cwd}${name}`);
                                        const content = res
                                        const modal = document.getElementById('editor-modal');
                                        document.getElementById('editor-filename').textContent =
                                            name;
                                        // show modal
                                        modal.classList.remove('hidden');
                                        // initialize ACE
                                        const editorEl = document.getElementById('file-editor');
                                        // initialize ACE editor only once and reuse it
                                        if (!window.fileAceEditor) {
                                            window.fileAceEditor = ace.edit(editorEl);
                                            window.fileAceEditor.setTheme('ace/theme/monokai');
                                            window.fileAceEditor.setOptions({
                                                fontSize: "16pt",
                                                showPrintMargin: false
                                            });
                                            // Glass effect: transparent underlay + blurred scrollable area
                                            editorEl.style.background = 'transparent';
                                            window.fileAceEditor.renderer.setScrollMargin(12, 12);
                                            window.fileAceEditor.renderer.setPadding(12);
                                            // Override Ace scroller bg
                                            const scroller = editorEl.querySelector(
                                                '.ace_scroller');
                                            if (scroller) {
                                                scroller.style.background = 'rgba(28,28,28,0.5)';
                                                scroller.style.backdropFilter = 'blur(8px)';
                                            }
                                        }
                                        const aceEditor = window.fileAceEditor;
                                        // set the correct syntax mode and load content
                                        const ext = name.split('.').pop();
                                        aceEditor.session.setMode(
                                            `ace/mode/${AceModeForExtension(ext)}`);
                                        aceEditor.setValue(content, -1);
                                        // wire up save/close
                                        document.getElementById('editor-save').onclick =
                                            async () => {
                                                const updated = aceEditor.getValue();
                                                await apiClient.writeFile(`${cwd}/${name}`,
                                                    updated);
                                                modal.classList.add('hidden');
                                                await load();
                                            };
                                        document.getElementById('editor-close').onclick = () =>
                                            modal.classList.add('hidden');
                                        document.getElementById('editor-cancel').onclick = () =>
                                            modal.classList.add('hidden');
                                    } catch (err) {
                                        // fallback to download
                                        if (confirm('Cannot open in editor. Download file?')) {
                                            const {
                                                attributes: {
                                                    url
                                                }
                                            } =
                                            await apiClient.getDownloadLink(`${cwd}/${name}`);
                                            window.open(url, '_blank');
                                        }
                                    }
                                }
                            };
                            el.addEventListener('contextmenu', e => {
                                e.preventDefault();
                                // Use clientX/clientY directly - these are viewport coordinates perfect for fixed positioning
                                showContextMenu(e, name);
                            });

                            // enable dragging of file(s)
                            el.draggable = true;
                            el.addEventListener('dragstart', e => {
                                // if multiple selected, drag all; else just this one
                                const toMove = selected.has(name) ? Array.from(selected) : [name];
                                const payload = JSON.stringify(toMove);
                                // support both JSON and text/plain types so dragenter sees it
                                e.dataTransfer.setData('application/json', payload);
                                e.dataTransfer.setData('text/plain', payload);
                                e.dataTransfer.effectAllowed = 'move';
                            });

                            // if this entry is a folder, allow dropping files onto it to move
                            if (!is_file) {
                                el.addEventListener('dragenter', e => {
                                    const types = Array.from(e.dataTransfer.types || []);
                                    if (!types.includes('application/json') && !types.includes(
                                            'text/plain')) return;
                                    e.preventDefault();
                                    el.classList.remove('bg-white/5');
                                    el.classList.add('bg-blue-400/50');
                                });
                                el.addEventListener('dragover', e => {
                                    const types = Array.from(e.dataTransfer.types || []);
                                    if (!types.includes('application/json') && !types.includes(
                                            'text/plain')) return;
                                    e.preventDefault();
                                    // check if the clas is applied
                                    if (!el.classList.contains('bg-blue-400/50')) {
                                        el.classList.remove('bg-white/5');
                                        el.classList.add('bg-blue-400/50');
                                    }
                                    e.dataTransfer.dropEffect = 'move';
                                });
                                el.addEventListener('dragleave', e => {
                                    el.classList.remove('bg-blue-400/50');
                                    el.classList.add('bg-white/5');
                                });
                                el.addEventListener('drop', async e => {
                                    const types = Array.from(e.dataTransfer.types || []);
                                    if (!types.includes('application/json') && !types.includes('text/plain')) return;
                                    e.preventDefault();
                                    el.classList.remove('bg-blue-400/50');
                                    el.classList.add('bg-white/5');
                                    // read dropped names
                                    const json = e.dataTransfer.getData('application/json') 
                                              || e.dataTransfer.getData('text/plain');
                                    const names = JSON.parse(json);
                                    // prevent moving into itself
                                    const target = name;  // the folder you dropped onto
                                    if (names.includes(target)) {
                                        console.warn(`Cannot move "${target}" into itself, skipping.`);
										return;
                                    }
                                    for (const n of names) {
                                        if (n === target) continue;
                                        try {
                                            await apiClient.renameFile(n, `/${target}/${n}`, cwd);
                                        } catch (err) {
                                            console.error('Move failed', n, err);
                                        }
                                    }
                                    await load();
                                });
                            }

                            container.append(el);
                        });
                    updateControls();
                }

                async function load() {
                    showSpinner();
                    selected.clear();
                    renderBreadcrumbs();
                    // load saved preference for this directory
                    viewMode = getView(cwd);
                    try {
                        const res = await apiClient.listFiles(cwd);
                        files = res.data;
                    } catch (e) {
                        files = [];
                    }
                    hideSpinner();
                    renderFiles();
                }

                // --- events ---
                btnGrid.onclick = () => {
                    viewMode = 'grid';
                    setView(cwd, viewMode);
                    renderFiles();
                };
                btnList.onclick = () => {
                    viewMode = 'list';
                    setView(cwd, viewMode);
                    renderFiles();
                };
                searchInput.oninput = () => renderFiles();

                createMenu.onclick = () => createItems.classList.toggle('hidden');
                document.getElementById('upload-file').onclick = () => {
                    const uploadInput = document.getElementById('upload-input');
                    uploadInput.click();
                    uploadInput.onchange = async () => {
                        const files = Array.from(uploadInput.files);
                        for (const file of files) {
                            await apiClient.uploadFile(file, cwd);
                        }
                        await load();
                    };
                };
                document.getElementById('create-file').onclick = () => {
                    createTitle.textContent = 'New File';
                    createInput.value = '';
                    createItems.classList.add('hidden');
                    createModal.classList.remove('hidden');
                };
                document.getElementById('create-folder').onclick = () => {
                    createTitle.textContent = 'New Folder';
                    createInput.value = '';
                    createItems.classList.add('hidden');
                    createModal.classList.remove('hidden');
                };
                createCancel.onclick = () => createModal.classList.add('hidden');
                createOK.onclick = async () => {
                    const name = createInput.value.trim();
                    if (!name) return;
                    try {
                        if (createTitle.textContent === 'New File')
                            await apiClient.createFile(`${cwd}/${name}`);
                        else
                            await apiClient.createFolder(name, cwd);
                        await load();
                    } catch (e) {}
                    createModal.classList.add('hidden');
                };

                ctxDownload.onclick = async () => {
                    contextMenu.classList.add('hidden');
                    for (const name of ctxTargets) {
                        const {
                            attributes: {
                                url
                            }
                        } = await apiClient.getDownloadLink(`${cwd}/${name}`);
                        window.open(url, '_blank');
                    }
                };

                // open selected file(s) in ACE editor
                ctxOpen.onclick = async () => {
                    contextMenu.classList.add('hidden');
                    if (ctxTargets.length !== 1) {
                        alert('Please select exactly one file to open.');
                        return;
                    }
                    const name = ctxTargets[0];
                    const modal = document.getElementById('editor-modal');
                    document.getElementById('editor-filename').textContent = name;
                    modal.classList.remove('hidden');

                    // load file content
                    showSpinner();
                    try {
                        const content = await apiClient.readFile(`${cwd}${name}`);
						console.log(`Loading file for editing: ${cwd}${name}`);
                        let aceEditor = window.fileAceEditor;
                        if (!aceEditor) {
                            // initialization happens in your existing ondblclick logic
                            document.getElementById('editor-close')
                                .onclick(); // ensure modal DOM is ready
                        }
                        aceEditor = window.fileAceEditor;
                        const ext = name.split('.').pop();

                        aceEditor.session.setMode(`ace/mode/${ext || 'text'}`);
						console.log(`Setting mode for ${name} to ace/mode/${ext || 'text'}`,
							`(${AceModeForExtension(ext)})`);
                        aceEditor.setValue(content, -1);
						console.log(`Loaded content for ${name}`,content);
                    } catch (e) {
                        alert('Failed to load file for editing.');
                        modal.classList.add('hidden');
                    } finally {
                        hideSpinner();
                    }
                };

                ctxRename.onclick = async () => {
                    contextMenu.classList.add('hidden');
                    if (ctxTargets.length !== 1) {
                        alert('Please select exactly one item to rename.');
                        return;
                    }
                    const oldName = ctxTargets[0];
                    const newName = prompt('Rename to:', oldName);
                    if (!newName || newName === oldName) return;
                    await apiClient.renameFile(`${oldName}`, `${newName}`, cwd);
                    await load();
                };

                ctxCopy.onclick = async () => {
                    contextMenu.classList.add('hidden');
                    const dest = prompt('Copy to (path):', cwd);
                    if (!dest || !ctxTargets.length) return;
                    for (const name of ctxTargets) {
                        await apiClient.copyFile(`${cwd}/${name}`, dest);
                    }
                    await load();
                };

                ctxCompress.onclick = async () => {
                    contextMenu.classList.add('hidden');
                    if (!ctxTargets.length) return;
                    await apiClient.compressFiles(ctxTargets, cwd);
                    await load();
                };

                ctxDecompress.onclick = async () => {
                    contextMenu.classList.add('hidden');
                    if (!ctxTargets.length) return;
                    for (const name of ctxTargets) {
                        await apiClient.decompressFile(name, cwd);
                    }
                    await load();
                };

                ctxDelete.onclick = async () => {
                    contextMenu.classList.add('hidden');
                    if (!ctxTargets.length) return;
                    if (!confirm(`Delete ${ctxTargets.length} item(s)?`)) return;
                    await apiClient.deleteFiles(cwd, ctxTargets);
                    await load();
                };

                // --- Upload Enhancements ---
                const uploadBtn = document.getElementById('upload-file');
                const uploadInput = document.getElementById('upload-input');

                // open file picker when clicking Upload
                uploadBtn.onclick = () => {
                    createItems.classList.add('hidden');
                    uploadInput.click();
                };

                // handle picker selection
                uploadInput.onchange = async e => {
                    const filesToUpload = Array.from(e.target.files);
                    if (!filesToUpload.length) return;
                    showSpinner();
                    for (const f of filesToUpload) {
                        try {
                            await apiClient.uploadFile(f, cwd);
                        } catch (err) {
                            console.error(err);
                        }
                    }
                    hideSpinner();
                    uploadInput.value = null;
                    await load();
                };

                // drag & drop upload across entire files-section (ignore internal drags)
                const dropArea = document.getElementById('files-section');
                for (const evt of ['dragover', 'dragenter']) {
                    dropArea.addEventListener(evt, e => {
                        e.preventDefault();
                        // only show overlay for external file drags
                        const types = Array.from(e.dataTransfer.types || []);
                        if (!types.includes('application/json')) {
                            dropOverlay.classList.remove('hidden');
                        }
                    });
                }
                dropArea.addEventListener('dragleave', e => {
                    e.preventDefault();
                    dropOverlay.classList.add('hidden');
                });
                dropArea.addEventListener('drop', async e => {
                    e.preventDefault();
                    dropOverlay.classList.add('hidden');
                    // if internal move, skip upload logic
                    const types = Array.from(e.dataTransfer.types || []);
                    if (types.includes('application/json')) return;
                    const dropped = Array.from(e.dataTransfer.files);
                    if (!dropped.length) return;
                    showSpinner();
                    for (const f of dropped) {
                        try {
                            await apiClient.uploadFile(f, cwd);
                        } catch (err) {
                            console.error(err);
                        }
                    }
                    hideSpinner();
                    await load();
                });

                // Set mounted flag and load initial data
                mountedFiles = true;
                await load();

                console.log('Files mounted successfully');
            } catch (error) {
                console.error('Failed to mount files:', error);
                showToast('Failed to initialize file manager', 'error');
            } finally {
                mounting = false;
            }
        }

            async function unmountFiles() {
                if (!mountedFiles) {
                    console.log('Unmount files called but not mounted');
                    return;
                }

                console.log('Unmounting files...');
                mountedFiles = false;

                try {
                    // Clean up event listeners
                    cleanupEventListeners();

                    // destroy any existing ACE editor instance
                    const editorEl = document.getElementById('file-editor');
                    if (window.fileAceEditor) {
                        try {
                            window.fileAceEditor.destroy();
                        } catch (e) {
                            console.warn('Error destroying ACE editor:', e);
                        }
                        delete window.fileAceEditor;
                    }
                    if (editorEl) {
                        editorEl.innerHTML = '';
                    }

                    // clear UI
                    const filesContainer = document.getElementById('files-container');
                    if (filesContainer) filesContainer.innerHTML = '';
                    
                    const breadcrumb = document.getElementById('files-breadcrumb');
                    if (breadcrumb) breadcrumb.innerHTML = '';
                    
                    const searchInput = document.getElementById('file-search');
                    if (searchInput) searchInput.value = '';

                    // hide any open modals
                    const modals = ['editor-modal', 'create-modal', 'file-context-menu'];
                    modals.forEach(modalId => {
                        const modal = document.getElementById(modalId);
                        if (modal) modal.classList.add('hidden');
                    });

                    console.log('Files unmounted successfully');
                } catch (error) {
                    console.error('Error during files unmount:', error);
                }
            }

        })();
    </script>
@endscript
