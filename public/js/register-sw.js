let Cache_Files = [];
let deferredPrompt;

window.addEventListener("load", () => {

Cache_Files = learn_page();

get_VAPID_FromServer();

});
window.addEventListener('beforeinstallprompt', function(e) {
  e.preventDefault();

  // Stash the event so it can be triggered later.
  deferredPrompt = e;
  deferredPrompt.prompt();

});
function registerServiceWorker(data) {

    vapid = data.public;

    if (!('serviceWorker' in navigator)) {
        console.warn("[ViXiV] Service workers are not supported by this browser");
        return;
    }
    if (navigator.serviceWorker.controller) {

	navigator.serviceWorker.controller.postMessage({Cache_Files});
	push_updateSubscription();
    } else {
        navigator.serviceWorker.register('service_worker.js?v3', {
            scope: './'
        }).then(function(registration) {
            console.log('Service Worker Registered for scope:'+ registration.scope);

if(deferredPrompt !== undefined) {
    deferredPrompt.prompt();

    deferredPrompt.userChoice.then(function(choiceResult) {

      if(choiceResult.outcome == 'dismissed') {} else {}


      deferredPrompt = null;
    });
  }
        });
    }
    if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
        console.warn('[ViXiV] Notifications are not supported by this browser');
    }
    if (!('PushManager' in window)) {
        console.warn('[ViXiV] Push Notifications are not supported by this browser');
    }
    if ('PushManager' in window)
       {
        Notification.requestPermission(function(status) {
        });
        push_subscribe(vapid);
    }
}

function learn_page()
{
var image_links = []; var scripts_links = []; var scripts_source = []; var style_source = []; var link_rel = [];
$('script').each(function( index ) {
    if(this.src){
        scripts_links.push(this.src);
    }
});
$('img').each(function( index ) {
	if(this.src){
		image_links.push(this.src);
	}
});
$('link').each(function( index ) {
    if(this.rel){
        link_rel.push(this.href);
    }
});
var cache_files = [];
image_links.forEach(function(item){
	cache_files.push(item);
});
scripts_links.forEach(function(item){
        cache_files.push(item);
});
link_rel.forEach(function(item){
        cache_files.push(item);
});
return cache_files;
}
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    function push_subscribe(Key) {
        navigator.serviceWorker.ready
        .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(Key),
        }))
        .then(subscription => {
            return push_sendSubscriptionToServer(subscription, 'POST');
        })
        .then(subscription => subscription)
        .catch(e => {
            if (Notification.permission === 'denied') {
                console.warn('Notifications Not Allowed By User!');
            } else {
                console.error('Push Notifications Not Supported', e);
            }
        });
    }

    function push_updateSubscription() {
        navigator.serviceWorker.ready.then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
        .then(subscription => {
            if (!subscription) {
                return;
            }
            return push_sendSubscriptionToServer(subscription, 'PUT');
        })
        .then(subscription => subscription)
        .catch(e => {
            console.error('Error when updating the subscription', e);
        });
    }

    function push_unsubscribe() {
        navigator.serviceWorker.ready
        .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
        .then(subscription => {
            if (!subscription) {
                return;
            }

            return push_sendSubscriptionToServer(subscription, 'DELETE');
        })
        .then(subscription => subscription.unsubscribe())
        .then(() => changePushButtonState('disabled'))
        .catch(e => {
            console.error('Error when unsubscribing the user', e);
        });
    }

    function push_sendSubscriptionToServer(subscription, method) {
        const key = subscription.getKey('p256dh');
        const token = subscription.getKey('auth');
        return fetch('/subscriptions', {
            method,
            body: JSON.stringify({
                endpoint: subscription.endpoint,
                key: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
                token: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null
            }),
        }).then(() => subscription);
    }

    function get_VAPID_FromServer() {
	method = 'GET';
        promise = fetch('subscriptions', {
            method,
//	    headers: {
//            "Content-Type": "application/json; charset=utf-8"
//            }
        })
	.then((response) => response.json())
	.then((data) => { registerServiceWorker(data) })
	.catch((error) => { return; });
	return promise;
    }
