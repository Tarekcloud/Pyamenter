import { Livewire, Alpine } from '../../../vendor/livewire/livewire/dist/livewire.esm';

window.appInit = window.appInit || {
    intervals: new Map(),
    observers: new Map(),
    timeouts: new Map(),
    cleanupFunctions: [],
    
    cleanup() {
        this.intervals.forEach((interval, key) => {
            clearInterval(interval);
            console.log(`Cleared interval: ${key}`);
        });
        this.intervals.clear();
        
        this.observers.forEach((observer, key) => {
            observer.disconnect();
            console.log(`Disconnected observer: ${key}`);
        });
        this.observers.clear();
        
        this.timeouts.forEach((timeout, key) => {
            clearTimeout(timeout);
            console.log(`Cleared timeout: ${key}`);
        });
        this.timeouts.clear();
        
        this.cleanupFunctions.forEach(fn => {
            try {
                fn();
            } catch (error) {
                console.error('Error in cleanup function:', error);
            }
        });
        this.cleanupFunctions = [];
    }
};

const localeMapping = {
    'ar': 'ar-SA', 'de': 'de-DE', 'en': 'en-US', 'es': 'es-ES',
    'fi': 'fi-FI', 'fr': 'fr-FR', 'it': 'it-IT', 'sv': 'sv-SE',
    'uk': 'uk-UA', 'ko': 'ko-KR', 'lv': 'lv-LV', 'nl': 'nl-NL',
    'no': 'no-NO', 'pt': 'pt-PT', 'sr': 'sr-RS', 'id': 'id-ID'
};

let cachedLocale = null;
function getLocale() {
    if (!cachedLocale) {
        const laravelLocale = document.documentElement.lang || 'en';
        cachedLocale = localeMapping[laravelLocale] || 'en-US';
    }
    return cachedLocale;
}

function updateDateTime() {
    const timeElement = document.getElementById('current-time');
    const dateElement = document.getElementById('current-date');
    
    if (!timeElement && !dateElement) {
        return;
    }
    
    const now = new Date();
    const locale = getLocale();
    
    try {
        if (timeElement) {
            const newTime = now.toLocaleTimeString(locale, {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
            if (timeElement.textContent !== newTime) {
                timeElement.textContent = newTime;
            }
        }

        if (dateElement) {
            const newDate = now.toLocaleDateString(locale, {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            if (dateElement.textContent !== newDate) {
                dateElement.textContent = newDate;
            }
        }
    } catch (error) {
        console.error('DateTime update failed, using fallback:', error);
        
        if (timeElement) {
            timeElement.textContent = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        }
        if (dateElement) {
            dateElement.textContent = now.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    }
}

function initDateTime() {
    const existingInterval = appInit.intervals.get('datetime');
    if (existingInterval) {
        clearInterval(existingInterval);
        appInit.intervals.delete('datetime');
    }
    
    updateDateTime(); 
    
    const interval = setInterval(updateDateTime, 1000);
    appInit.intervals.set('datetime', interval);
    
    console.log('DateTime interval initialized');
}

function initDateTimeMutationObserver() {
    const existingObserver = appInit.observers.get('datetime-mutation');
    if (existingObserver) {
        existingObserver.disconnect();
    }
    
    const observer = new MutationObserver((mutations) => {
        let shouldUpdate = false;
        
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === Node.ELEMENT_NODE) {
                    const hasTimeElement = node.id === 'current-time' || node.querySelector?.('#current-time');
                    const hasDateElement = node.id === 'current-date' || node.querySelector?.('#current-date');
                    
                    if (hasTimeElement || hasDateElement) {
                        shouldUpdate = true;
                    }
                }
            });
        });
        
        if (shouldUpdate) {
            console.log('DateTime elements detected, updating immediately');
            setTimeout(updateDateTime, 10);
        }
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    appInit.observers.set('datetime-mutation', observer);
    console.log('DateTime mutation observer initialized');
}

function showPasswordSuccess() {
    const successMsg = document.getElementById('password-success');
    if (successMsg) {
        successMsg.classList.remove('hidden');
        
        const timeoutId = setTimeout(() => {
            successMsg.classList.add('hidden');
        }, 3000);
        
        appInit.timeouts.set('password-success', timeoutId);
    }
}


document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ fail }) => {
        fail(({ status, preventDefault }) => {
            if (status === 419) {
                window.location.reload();
                preventDefault();
            }
        });
    });
    
    Livewire.on('password-changed', () => {
        showPasswordSuccess();
    });
});

Alpine.store('notifications', {
    init () {
        Livewire.on('notify', e => {
            Alpine.store('notifications').addNotification(e)
        })
    },
    notifications: [],
    addNotification (notification) {
        notification = notification[0]
        notification.show = false
        notification.id = Date.now() + Math.floor(Math.random() * 1000)
        this.notifications.push(notification)

        Alpine.nextTick(() => {
            this.notifications = this.notifications.map(n => {
                if (n.id === notification.id) {
                    n.show = true
                }
                return n
            })
        })

        setTimeout(() => {
            this.removeNotification(notification.id)
        }, notification.timeout || 5000)
    },
    removeNotification (id) {
        const notification = this.notifications.find(n => n.id === id);
        if (notification) {
            notification.show = false;
        }

        setTimeout(() => {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }, 300); // Waktu untuk transisi keluar
    }
});

Alpine.store('confirmation', {
    show: false,
    loading: false,
    title: '',
    message: '',
    confirmText: 'Confirm',
    cancelText: 'Cancel',
    callback: null,

    confirm (options) {
        this.show = true
        this.loading = false
        this.title = options.title
        this.message = options.message
        this.confirmText = options.confirmText || 'Confirm'
        this.cancelText = options.cancelText || 'Cancel'
        this.callback = options.callback
    },

    async execute () {
        if (this.loading) return

        this.loading = true

        try {
            if (this.callback) {
                await this.callback()
                this.loading = false
            }
            this.close()
        } catch (error) {
            console.error('Callback failed:', error)
            this.close()
        }
    },

    close () {
        if (this.loading) return

        this.show = false
        this.loading = false
        this.callback = null
    }
});

if ('serviceWorker' in navigator) {
    navigator.serviceWorker
        .register('/service-worker.js')
        .then(function (registration) {
            console.log(
                'Service Worker registered with scope:',
                registration.scope
            )
        })
        .catch(function (error) {
            console.log('Service Worker registration failed:', error)
        })

    navigator.serviceWorker.onmessage = function (event) {
        if (event.data && event.data.type === 'SHOW_NOTIFICATION') {
            Livewire.dispatch('notification-added', [event.data.notification])
            window.dispatchEvent(new CustomEvent('new-notification'))
        }
    }
}

function initializeApp() {
    console.log('Initializing application...');
    
    appInit.cleanup();
    
    initDateTime();
    initDateTimeMutationObserver();
    
    console.log('Application initialized');
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApp);
} else {
    initializeApp();
}

document.addEventListener('livewire:navigated', () => {
    cachedLocale = null;
    initializeApp();
});

document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        updateDateTime();
    }
});

window.addEventListener('beforeunload', () => {
    appInit.cleanup();
});

console.log('%c🚀 Powered with Lucent', 'color: #006effff; font-size: 16px; font-weight: bold;');

Livewire.start();