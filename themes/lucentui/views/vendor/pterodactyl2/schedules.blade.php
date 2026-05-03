<section id="schedules-section"
    class="relative flex flex-col h-full w-full
           p-6 rounded-xl mx-auto
           overflow-hidden">

    <!-- spinner overlay while loading schedules -->
    <div id="schedules-spinner"
         class="absolute inset-0 flex items-center justify-center
                bg-[#1c1c1c]/[var(--bg-opacity)] [--bg-opacity:25%] z-20 hidden">
        <div class="w-12 h-12 border-4 border-t-blue-500 border-gray-600 rounded-full animate-spin"></div>
    </div>

    <!-- header with title and create button -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-semibold text-base">Server Schedules</h2>
        <button id="create-schedule-btn" 
                class="px-4 py-2 bg-green-600 font-bold text-white rounded-xl hover:bg-green-700 transition-colors">
            Create Schedule
        </button>
    </div>

    <!-- schedules list container -->
    <div id="schedules-container" class="flex-1 space-y-4 overflow-auto">
        <!-- schedules will be dynamically inserted here -->
    </div>

</section>

<!-- Create/Edit Schedule Modal -->
<div id="schedule-modal" class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-black/40 hidden z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-2xl mx-4 relative">
        <!-- modal spinner -->
        <div id="modal-spinner"
             class="absolute inset-0 flex items-center justify-center bg-black/50 z-10 hidden">
            <div class="w-8 h-8 border-4 border-t-blue-500 border-gray-600 rounded-full animate-spin"></div>
        </div>

        <div class="flex justify-between items-center mb-4">
            <h3 id="modal-title" class="text-lg font-semibold text-gray-900 dark:text-white">Create Schedule</h3>
            <button id="modal-close" class="text-gray-500 dark:text-base/50 hover:text-red-500 text-2xl">&times;</button>
        </div>

        <form id="schedule-form" class="space-y-4">
            <!-- Schedule Name -->
            <div>
                <label class="block text-gray-700 dark:text-base/40 font-medium mb-2">Schedule Name</label>
                <input id="schedule-name" type="text" required
                       class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl border border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:outline-none"
                       placeholder="e.g., Daily Restart">
            </div>

            <!-- Cron Expression Grid -->
            <div class="grid grid-cols-5 gap-4">
                <div>
                    <label class="block text-gray-700 dark:text-base/40 text-sm mb-1">Minute (0-59)</label>
                    <input id="cron-minute" type="text" required value="0"
                           class="w-full px-2 py-1 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded border border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:outline-none text-center"
                           placeholder="0">
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-base/40 text-sm mb-1">Hour (0-23)</label>
                    <input id="cron-hour" type="text" required value="6"
                           class="w-full px-2 py-1 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded border border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:outline-none text-center"
                           placeholder="6">
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-base/40 text-sm mb-1">Day (1-31)</label>
                    <input id="cron-day" type="text" required value="*"
                           class="w-full px-2 py-1 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded border border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:outline-none text-center"
                           placeholder="*">
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-base/40 text-sm mb-1">Month (1-12)</label>
                    <input id="cron-month" type="text" required value="*"
                           class="w-full px-2 py-1 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded border border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:outline-none text-center"
                           placeholder="*">
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-base/40 text-sm mb-1">Weekday (0-6)</label>
                    <input id="cron-weekday" type="text" required value="*"
                           class="w-full px-2 py-1 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded border border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:outline-none text-center"
                           placeholder="*">
                </div>
            </div>

            <!-- Cron Helper Text -->
            <div class="text-xs text-gray-500 italic">
                Use * for "any value", numbers for specific values, or ranges like 0-5. 
                <span class="text-blue-400 cursor-pointer hover:underline" id="cron-help">Need help?</span>
            </div>

            <!-- Options -->
            <div class="grid grid-cols-2 gap-4">
                <label class="flex items-center space-x-2">
                    <input id="schedule-active" type="checkbox" checked
                           class="w-4 h-4 text-blue-600 bg-background-secondary/50 border-gray-600 rounded focus:ring-blue-500">
                    <span class="text-gray-700 dark:text-base/40">Active</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input id="schedule-online-only" type="checkbox"
                           class="w-4 h-4 text-blue-600 bg-background-secondary/50 border-gray-600 rounded focus:ring-blue-500">
                    <span class="text-gray-700 dark:text-base/40">Only when online</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" id="modal-cancel" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-background-secondary/50 transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                    Save Schedule
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Task Modal -->
<div id="task-modal" class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-black/40 hidden z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-lg mx-4 relative">
        <div class="flex justify-between items-center mb-4">
            <h3 id="task-modal-title" class="text-lg font-semibold text-gray-900 dark:text-white">Add Task</h3>
            <button id="task-modal-close" class="text-gray-500 dark:text-base/50 hover:text-red-500 text-2xl">&times;</button>
        </div>

        <form id="task-form" class="space-y-4">
            <!-- Task Action -->
            <div>
                <label class="block text-gray-700 dark:text-base/40 font-medium mb-2">Action</label>
                <select id="task-action" required
                        class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl border border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:outline-none">
                    <option value="power">Power Action</option>
                    <option value="command">Send Command</option>
                    <option value="backup">Create Backup</option>
                </select>
            </div>

            <!-- Task Payload -->
            <div>
                <label class="block text-gray-700 dark:text-base/40 font-medium mb-2">Payload</label>
                <input id="task-payload" type="text" 
                       class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl border border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:outline-none"
                       placeholder="e.g., restart, stop, say Hello">
                <div class="text-xs text-gray-500 mt-1">
                    Power: start/stop/restart/kill | Command: any server command | Backup: leave empty
                </div>
            </div>

            <!-- Time Offset -->
            <div>
                <label class="block text-gray-700 dark:text-base/40 font-medium mb-2">Time Offset (seconds)</label>
                <input id="task-offset" type="number" required value="0" min="0"
                       class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl border border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:outline-none">
                <div class="text-xs text-gray-500 mt-1">
                    Delay in seconds after the schedule triggers
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" id="task-modal-cancel" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-background-secondary/50 transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                    Save Task
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Confirm Action Modal -->
<div id="confirm-modal" class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-black/40 hidden z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm mx-4">
        <p id="confirm-message" class="text-gray-800 dark:text-gray-100 mb-4">Confirm action?</p>
        <div class="flex justify-end gap-2">
            <button id="confirm-cancel" class="px-4 py-2 bg-gray-200 dark:bg-background-secondary/50 text-gray-800 dark:text-gray-100 rounded hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
            <button id="confirm-ok" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Confirm</button>
        </div>
    </div>
</div>

<!-- Toast container -->
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

    // --- API Client ---
    class ApiClient {
        constructor(serviceId) {
            this.serviceId = serviceId;
            this.baseUrl = `/services/${serviceId}/schedules`;
        }

        async request(endpoint = '', method = 'GET', body = null) {
            const spinner = document.getElementById('schedules-spinner');
            if (spinner) spinner.classList.remove('hidden');

            try {
                const headers = {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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

        getSchedules() {
            return this.request();
        }

        getSchedule(scheduleId) {
            return this.request(`/${scheduleId}`);
        }

        createSchedule(data) {
            return this.request('', 'POST', JSON.stringify(data));
        }

        updateSchedule(scheduleId, data) {
            return this.request(`/${scheduleId}`, 'POST', JSON.stringify(data));
        }

        deleteSchedule(scheduleId) {
            return this.request(`/${scheduleId}`, 'DELETE');
        }

        executeSchedule(scheduleId) {
            return this.request(`/${scheduleId}/execute`, 'POST');
        }

        getScheduleTasks(scheduleId) {
            return this.request(`/${scheduleId}/tasks`);
        }

        createScheduleTask(scheduleId, data) {
            return this.request(`/${scheduleId}/tasks`, 'POST', JSON.stringify(data));
        }

        updateScheduleTask(scheduleId, taskId, data) {
            return this.request(`/${scheduleId}/tasks/${taskId}`, 'POST', JSON.stringify(data));
        }

        deleteScheduleTask(scheduleId, taskId) {
            return this.request(`/${scheduleId}/tasks/${taskId}`, 'DELETE');
        }
    }

    // --- Mount / Unmount System ---
    let isMounted = false;

    async function mountSchedules() {
        if (isMounted) return;
        isMounted = true;

        const api = new ApiClient({{ $service->id }});
        const container = document.getElementById('schedules-container');
        
        // DOM elements
        const createBtn = document.getElementById('create-schedule-btn');
        const modal = document.getElementById('schedule-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalClose = document.getElementById('modal-close');
        const modalCancel = document.getElementById('modal-cancel');
        const scheduleForm = document.getElementById('schedule-form');
        
        const taskModal = document.getElementById('task-modal');
        const taskModalClose = document.getElementById('task-modal-close');
        const taskModalCancel = document.getElementById('task-modal-cancel');
        const taskForm = document.getElementById('task-form');

        const confirmModal = document.getElementById('confirm-modal');
        const confirmMessageEl = document.getElementById('confirm-message');
        const confirmCancelBtn = document.getElementById('confirm-cancel');
        const confirmOkBtn = document.getElementById('confirm-ok');

        let currentScheduleId = null;
        let currentTaskId = null;
        let editMode = false;

        // Close modals
        function closeModal() {
            modal.classList.add('hidden');
            resetForm();
        }

        function closeTaskModal() {
            taskModal.classList.add('hidden');
            resetTaskForm();
        }

        function closeConfirmModal() { confirmModal.classList.add('hidden'); }

        function resetForm() {
            scheduleForm.reset();
            document.getElementById('schedule-name').value = '';
            document.getElementById('cron-minute').value = '0';
            document.getElementById('cron-hour').value = '6';
            document.getElementById('cron-day').value = '*';
            document.getElementById('cron-month').value = '*';
            document.getElementById('cron-weekday').value = '*';
            document.getElementById('schedule-active').checked = true;
            document.getElementById('schedule-online-only').checked = false;
            currentScheduleId = null;
            editMode = false;
        }

        function resetTaskForm() {
            taskForm.reset();
            document.getElementById('task-action').value = 'power';
            document.getElementById('task-payload').value = '';
            document.getElementById('task-offset').value = '0';
            currentTaskId = null;
        }

        // Event listeners
        createBtn.onclick = () => {
            modalTitle.textContent = 'Create Schedule';
            resetForm();
            modal.classList.remove('hidden');
        };

        modalClose.onclick = closeModal;
        modalCancel.onclick = closeModal;
        taskModalClose.onclick = closeTaskModal;
        taskModalCancel.onclick = closeTaskModal;
        confirmCancelBtn.onclick = closeConfirmModal;        // Format cron expression for display
        function formatCron(schedule) {
            const cron = schedule.cron || schedule; // Handle both nested and flat structure
            const minute = cron.minute || '*';
            const hour = cron.hour || '*';
            const day = cron.day_of_month || '*';
            const month = cron.month || '*';
            const weekday = cron.day_of_week || '*';
            return `${minute} ${hour} ${day} ${month} ${weekday}`;
        }        // Format next run time
        function formatNextRun(schedule) {
            const cron = schedule.cron || schedule; // Handle both nested and flat structure
            const minute = cron.minute || '*';
            const hour = cron.hour || '*';
            const day = cron.day_of_month || '*';
            // This is a simplified version - in a real app you'd want a proper cron parser
            if (minute === '*' && hour === '*') return 'Every minute';
            if (minute !== '*' && hour === '*') return `Every hour at minute ${minute}`;
            if (hour !== '*' && day === '*') return `Daily at ${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
            return 'Custom schedule';
        }

        // Render schedules
        async function loadSchedules() {
            try {
                const response = await api.getSchedules();
                const schedules = response.data || [];
                
                if (schedules.length === 0) {
                    container.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-12 text-base/50">
                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-bold mb-2">No schedules found</h3>
                            <p class="text-sm">Create your first schedule to automate server tasks.</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = schedules.map(schedule => {
                    const attr = schedule.attributes;
                    const statusClass = attr.is_active ? 'bg-green-500' : 'bg-gray-500';
                    const statusText = attr.is_active ? 'Active' : 'Inactive';

                    return `
                        <div class="bg-gray-900/25 p-6 rounded-xl border border-neutral">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${attr.name}</h3>
                                        <span class="${statusClass} text-white text-xs px-2 py-1 rounded-full">${statusText}</span>
                                        ${attr.only_when_online ? '<span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Online Only</span>' : ''}
                                    </div>                                    <div class="text-base/50 text-sm space-y-1">
                                        <div>Schedule: <code class="bg-background-secondary/50 px-2 py-1 rounded text-xs">${formatCron(attr)}</code></div>
                                        <div>Next: ${formatNextRun(attr)}</div>
                                        <div>Last run: ${attr.last_run_at ? new Date(attr.last_run_at).toLocaleString() : 'Never'}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button onclick="executeSchedule(${attr.id})" class="p-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors" title="Run Now">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1m-6-8h8M7 14h1m4 0h1M3 5l5.5 7L3 19h4.5L12 12l4.5 7H21l-5.5-7L21 5h-4.5L12 12 7.5 5H3z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="editSchedule(${attr.id})" class="p-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="deleteSchedule(${attr.id})" class="p-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-base/40">Tasks (${attr.relationships?.tasks?.data?.length || 0})</h4>
                                    <button onclick="addTask(${attr.id})" class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 transition-colors">
                                        Add Task
                                    </button>
                                </div>
                                <div id="tasks-${attr.id}" class="space-y-1">
                                    ${(attr.relationships?.tasks?.data || []).map(task => `
                                        <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-800 p-2 rounded text-sm">
                                            <div class="text-gray-700 dark:text-base/40">
                                                <span class="font-medium">${task.attributes.action}</span>
                                                ${task.attributes.payload ? `<span class="text-gray-500">: ${task.attributes.payload}</span>` : ''}
                                                <span class="text-gray-500">(+${task.attributes.time_offset}s)</span>
                                            </div>
                                            <div class="flex gap-1">
                                                <button onclick="editTask(${attr.id}, ${task.attributes.id})" class="text-blue-400 hover:text-blue-300">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button onclick="deleteTask(${attr.id}, ${task.attributes.id})" class="text-red-400 hover:text-red-300">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

            } catch (error) {
                console.error('Failed to load schedules:', error);
                showToast('Failed to load schedules', 'error');
            }
        }

        // Global functions for button clicks
        window.executeSchedule = async (scheduleId) => {
            try {
                await api.executeSchedule(scheduleId);
                showToast('Schedule executed successfully', 'success');
            } catch (error) {
                console.error('Failed to execute schedule:', error);
                showToast('Failed to execute schedule', 'error');
            }
        };        window.editSchedule = async (scheduleId) => {
            try {
                const response = await api.getSchedule(scheduleId);                const schedule = response.attributes;
                const cron = schedule.cron || schedule; // Handle both nested and flat structure
                
                modalTitle.textContent = 'Edit Schedule';
                document.getElementById('schedule-name').value = schedule.name;
                document.getElementById('cron-minute').value = cron.minute || '*';
                document.getElementById('cron-hour').value = cron.hour || '*';
                document.getElementById('cron-day').value = cron.day_of_month || '*';
                document.getElementById('cron-month').value = cron.month || '*';
                document.getElementById('cron-weekday').value = cron.day_of_week || '*';
                document.getElementById('schedule-active').checked = schedule.is_active;
                document.getElementById('schedule-online-only').checked = schedule.only_when_online;
                
                currentScheduleId = scheduleId;
                editMode = true;
                modal.classList.remove('hidden');
            } catch (error) {
                console.error('Failed to load schedule:', error);
                showToast('Failed to load schedule', 'error');
            }
        };

        window.deleteSchedule = async (scheduleId) => {
            confirmMessageEl.innerText = 'Delete this schedule?';
            confirmOkBtn.onclick = async () => {
                closeConfirmModal();
                try {
                    await api.deleteSchedule(scheduleId);
                    showToast('Schedule deleted successfully', 'success');
                    await loadSchedules();
                } catch (error) {
                    console.error('Failed to delete schedule:', error);
                    showToast('Failed to delete schedule', 'error');
                }
            };
            confirmModal.classList.remove('hidden');
        };

        window.addTask = (scheduleId) => {
            currentScheduleId = scheduleId;
            document.getElementById('task-modal-title').textContent = 'Add Task';
            resetTaskForm();
            taskModal.classList.remove('hidden');
        };

        window.editTask = async (scheduleId, taskId) => {
            // For simplicity, we'll just show the add task modal
            // In a real implementation, you'd load the task data first
            currentScheduleId = scheduleId;
            currentTaskId = taskId;
            document.getElementById('task-modal-title').textContent = 'Edit Task';
            taskModal.classList.remove('hidden');
        };

        window.deleteTask = async (scheduleId, taskId) => {
            confirmMessageEl.innerText = 'Delete this task?';
            confirmOkBtn.onclick = async () => {
                closeConfirmModal();
                try {
                    await api.deleteScheduleTask(scheduleId, taskId);
                    showToast('Task deleted successfully', 'success');
                    await loadSchedules();
                } catch (error) {
                    console.error('Failed to delete task:', error);
                    showToast('Failed to delete task', 'error');
                }
            };
            confirmModal.classList.remove('hidden');
        };

        // Form submissions
        scheduleForm.onsubmit = async (e) => {
            e.preventDefault();
            
            const data = {
                name: document.getElementById('schedule-name').value,
                cron_minute: document.getElementById('cron-minute').value,
                cron_hour: document.getElementById('cron-hour').value,
                cron_day_of_month: document.getElementById('cron-day').value,
                cron_month: document.getElementById('cron-month').value,
                cron_day_of_week: document.getElementById('cron-weekday').value,
                is_active: document.getElementById('schedule-active').checked,
                only_when_online: document.getElementById('schedule-online-only').checked
            };

            try {
                if (editMode && currentScheduleId) {
                    await api.updateSchedule(currentScheduleId, data);
                    showToast('Schedule updated successfully', 'success');
                } else {
                    await api.createSchedule(data);
                    showToast('Schedule created successfully', 'success');
                }
                closeModal();
                await loadSchedules();
            } catch (error) {
                console.error('Failed to save schedule:', error);
                showToast('Failed to save schedule', 'error');
            }
        };

        taskForm.onsubmit = async (e) => {
            e.preventDefault();
            
            const data = {
                action: document.getElementById('task-action').value,
                payload: document.getElementById('task-payload').value,
                time_offset: parseInt(document.getElementById('task-offset').value)
            };

            try {
                if (currentTaskId) {
                    await api.updateScheduleTask(currentScheduleId, currentTaskId, data);
                    showToast('Task updated successfully', 'success');
                } else {
                    await api.createScheduleTask(currentScheduleId, data);
                    showToast('Task created successfully', 'success');
                }
                closeTaskModal();
                await loadSchedules();
            } catch (error) {
                console.error('Failed to save task:', error);
                showToast('Failed to save task', 'error');
            }
        };

        // Cron help
        document.getElementById('cron-help').onclick = () => {
            alert(`Cron Expression Help:

* = any value
, = list separator (e.g., 1,3,5)
- = range (e.g., 1-5)
/ = step values (e.g., */2 = every 2)

Examples:
0 6 * * * = Every day at 6:00 AM
0 */4 * * * = Every 4 hours
0 0 * * 0 = Every Sunday at midnight
30 2 1 * * = 2:30 AM on the 1st of every month`);
        };

        // Initial load
        await loadSchedules();
    }

    function unmountSchedules() {
        if (!isMounted) return;
        isMounted = false;

        // Clean up
        const container = document.getElementById('schedules-container');
        if (container) container.innerHTML = '';
        
        // Clean up global functions
        delete window.executeSchedule;
        delete window.editSchedule;
        delete window.deleteSchedule;
        delete window.addTask;
        delete window.editTask;
        delete window.deleteTask;
    }

    function detectSchedules() {
        const exists = Boolean(document.getElementById('schedules-section'));
        if (exists && !isMounted) {
            mountSchedules();
        }
        if (!exists && isMounted) {
            unmountSchedules();
        }
    }

    // Initialize
    detectSchedules();
    setInterval(detectSchedules, 1000);
})();
</script>
@endscript
