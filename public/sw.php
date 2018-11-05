<?php
/**
 * Service worker related functions of PWA Pro
 *
 * @since 1.0
 * 
 * @function	pwapro_sw()					Service worker filename, absolute path and link
 * @function	pwapro_generate_sw()			Generate and write service worker into sw.js
 * @function	pwapro_sw_template()			Service worker tempalte
 * @function	pwapro_register_sw()			Register service worker
 * @function	pwapro_delete_sw()			Delete service worker
 * @function 	pwapro_offline_page_images()	Add images from offline page to filesToCache
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Service worker filename, absolute path and link
 *
 * For Multisite compatibility. Used to be constants defined in pwapro.php
 * On a multisite, each sub-site needs a different service worker.
 *
 * @param $arg 	filename for service worker filename (replaces PWAPRO_SW_FILENAME)
 *				abs for absolute path to service worker (replaces PWAPRO_SW_ABS)
 *				src for relative link to service worker (replaces PWAPRO_SW_SRC). Default value
 *
 * @return (string) filename, absolute path or link to manifest.
 * 
 * @since 1.6
 * @since 1.7 src to service worker is made relative to accomodate for domain mapped multisites.
 * @since 1.8 Added filter pwapro_sw_filename.
 */
function pwapro_sw( $arg = 'src' ) {
	
	$sw_filename = apply_filters( 'pwapro_sw_filename', 'pwapro-sw' . pwapro_multisite_filename_postfix() . '.js' );
	
	switch( $arg ) {
		
		// Name of service worker file
		case 'filename':
			return $sw_filename;
			break;
		
		// Absolute path to service worker. SW must be in the root folder	
		case 'abs':
			return trailingslashit( ABSPATH ) . $sw_filename;
			break;
		
		// Link to service worker
		case 'src':
		default:
			return parse_url( trailingslashit( network_site_url() ) . $sw_filename, PHP_URL_PATH );
			break;
	}
}

/**
 * Generate and write service worker into pwapro-sw.js
 *
 * @return (boolean) true on success, false on failure.
 * 
 * @since 1.0
 */
function pwapro_generate_sw() {
	
	// Get Settings
	$settings = pwapro_get_settings();
	
	// Get the service worker tempalte
	$sw = pwapro_sw_template();
	
	// Delete service worker if it exists
	pwapro_delete_sw();
	
	if ( ! pwapro_put_contents( pwapro_sw( 'abs' ), $sw ) ) {
		return false;
	}
	
	return true;
}

/**
 * Service Worker Tempalte
 *
 * @return (string) Contents to be written to pwapro-sw.js
 * 
 * @since 1.0
 * @since 1.7 added filter pwapro_sw_template
 * @since 1.9 added filter pwapro_sw_files_to_cache
 */
function pwapro_sw_template() {
	
	// Get Settings
	$settings = pwapro_get_settings();
	
	// Start output buffer. Everything from here till ob_get_clean() is returned
	ob_start();  ?>

'use strict';

/**
 * Service Worker of PWA Pro
 * To learn more and add one to your website, visit - https://pwapro.com
 */
 
//This is an advanced service worker with dynamic caching through site message communication
var CACHE = 'ViXiV-DynamicCache';
const OFFLINE = 'offline.html';

var network = ['data:', 'subscriptions', 'ping', 'mailbox/api/ping', 'piwik', 'google', 'youtube', 'facebook', 'admin', 'login', 'logout', 'sign-out', 'sign-in', 'simplicity', 'base', 'index', 'main', 
'dashboard', 'simplicity', 'upload', 'submit', 'post', 'delete', 'latest', 'return', 'ytimg'];

var blacklist = ['oxpwa.js', 'service_worker.js', 'manifest'];
var CacheFiles = [];

CacheOriginal = CacheFiles;

self.addEventListener('message', function(event) {
	data = event.data;
	CacheFiles = CacheOriginal;
        data.Cache_Files.forEach(function(item) {
		var l1 = 0; l2 = 0;
		for (l1 = 0; l1 < blacklist.length; l1++) {
			if (item.indexOf(blacklist[l1]) !== -1) { return; }
		}
                for (l2 = 0; l2 < network.length; l2++) {
                        if (item.indexOf(network[l2]) !== -1) { return; }
                }
		CacheFiles.push(item);
	});
	cache();
});
self.addEventListener('install', function(event) {
  event.waitUntil(cache().then(function() {
    return self.skipWaiting();
  }));
});
self.addEventListener('activate', function(event) {
  return self.clients.claim();
});
self.addEventListener('fetch', function(event) {
    var responseBody = {
      body: '',
      id: event.request.url
    };

    var responseInit = {
      	status: 200,
	ok:'true',
	type:'basic',
	url:event.request.url,
	redirected:'false',
      	statusText: 'OK',
      	headers: {
        'Content-Type': 'application/json',
        'X-Mock-Response': 'yes'
      }
    };
    var mockResponse = new Response(JSON.stringify(responseBody), responseInit);
    var item; var requested = 0;
    for (item = 0; item < blacklist.length; item++) {
	if (event.request.url.indexOf(blacklist[item]) !== -1) {
		/* return mockResponse;*/
		//return fromServer(event.request); }
		requested = 1;
		event.respondWith(fromServer(event.request.clone()).catch(function(error){}));
		return;
	}
    }
    for (item = 0; item < network.length; item++) {
	if (event.request.url.indexOf(network[item]) !== -1) {
                /* return mockResponse;*/
                //return fromServer(event.request); }
                requested = 1;
                event.respondWith(fromServer(event.request.clone()).catch(function(error){}));
                return;
        }

    }
    if(requested !== 1){
    event.respondWith(fromCache(event.request).catch(fromServer(event.request.clone())));
    event.waitUntil(update(event.request.clone()).catch(function(error){ return caches.match(OFFLINE); }));
    } else { return fromServer(event.request.clone()); }
});

function cache() {
  return caches.open(CACHE).then(function (cache) {
    var UniqueFiles = CacheFiles.filter(function(item, index){
	return CacheFiles.indexOf(item) >= index;
    });
    //cache.addAll(UniqueFiles);
    UniqueFiles.forEach(function(item) {
	let request = new Request(item);
	cache.add(item);
    	update(request);

    });
    return;
  });
}

function fromCache(request) {
  return caches.open(CACHE).then(function (cache) {
    return cache.match(request, {
      ignoreSearch: true
    }).then(function (matching) {
        if(matching){
                return matching;
        } else {
	        throw Error('Forwarding Unmatched Request to Network...');
	}
    }).catch(function(e) {
	return fromServer(request);
//        return update(request);
     });

  });
}

function update(request) {
  if(request.method !== 'GET'){ return; }
  return caches.open(CACHE).then(function (cache) {
    return fetch(request).then(function (response) {
    var item; var blocked = 0;
    for (item = 0; item < blacklist.length; item++) {
        if (request.url.indexOf(blacklist[item]) !== -1) {
		blocked = 1;
        }
    }
    for (item = 0; item < network.length; item++) {
        if (request.url.indexOf(network[item]) !== -1) {
                blocked = 1;
        }
    }
    if(blocked !== 1) {
    	if(request.method === 'GET'){
		cache.put(request, response);
	   	return response;
      	}
     } else { return; }
    });
  });
}

function fromServer(request){
  return fetch(request).then(function(response){
  	if(response){
		return response;
  	} else {
		throw Error('Offline');
  	}
  }).catch(function(error){ return caches.match(OFFLINE); });
}
self.addEventListener('push', function(event) {
  let notificationTitle = 'Notification';
  const notificationOptions = {
    icon: '/icon-192x192.png',
    badge: '/icon-72x72.png',
    data: {
      url: origin,
    },
  };

  if (event.data) {
    const dataText = event.data.text();
    notificationTitle = `${dataText}`;
    notificationOptions.tag = `${dataText}`;
    notificationOptions.body = `${dataText}`;
    notificationOptions.data.url = origin;
  }

  event.waitUntil(
    Promise.all([
      self.registration.showNotification(
        notificationTitle, notificationOptions),
      self.analytics.trackEvent('push-received'),
    ])
  );
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();

  let clickResponsePromise = Promise.resolve();
  if (event.notification.data && event.notification.data.url) {
    clickResponsePromise = clients.openWindow(event.notification.data.url);
  }

  event.waitUntil(
    Promise.all([
      clickResponsePromise,
      self.analytics.trackEvent('notification-click'),
    ])
  );
});

self.addEventListener('notificationclose', function(event) {
  event.waitUntil(
    Promise.all([
      self.analytics.trackEvent('notification-close'),
    ])
  );
});

<?php return apply_filters( 'pwapro_sw_template', ob_get_clean() );
}

/**
 * Register service worker
 *
 * @refer https://developers.google.com/web/fundamentals/primers/service-workers/registration#conclusion
 * 
 * @since 1.0
 */
function pwapro_register_sw() {
	
	wp_enqueue_script( 'pwapro-register-sw', PWAPRO_PATH_SRC . 'public/js/register-sw.js', array(), null, true );
	wp_localize_script( 'pwapro-register-sw', 'pwapro_sw', array(
			'url' => pwapro_sw( 'src' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'pwapro_register_sw' );

/**
 * Delete Service Worker
 *
 * @return true on success, false on failure
 * 
 * @since 1.0
 */
function pwapro_delete_sw() {
	return pwapro_delete( pwapro_sw( 'abs' ) );
}

/**
 * Add images from offline page to filesToCache
 * 
 * If the offlinePage set by the user contains images, they need to be cached during sw install. 
 * For most websites, other assets (css, js) would be same as that of startPage which would be cached
 * when user visits the startPage the first time. If not pwapro_sw_files_to_cache filter can be used.
 * 
 * @param (string) $files_to_cache Comma separated list of files to cache during service worker install
 * 
 * @return (string) Comma separated list with image src's appended to $files_to_cache
 * 
 * @since 1.9
 */
function pwapro_offline_page_images( $files_to_cache ) {
	
	// Get Settings
	$settings = pwapro_get_settings();
	
	// Retrieve the post
	$post = get_post( $settings['offline_page'] );
	
	// Return if the offline page is set to default
	if( $post === NULL ) {
		return $files_to_cache;
	}
	
	// Match all images
	preg_match_all( '/<img[^>]+src="([^">]+)"/', $post->post_content, $matches );
	
	// $matches[1] will be an array with all the src's
	if( ! empty( $matches[1] ) ) {
		return pwapro_httpsify( $files_to_cache . ', \'' . implode( '\', \'', $matches[1] ) . '\'' );
	}
	
	return $files_to_cache;
}
add_filter( 'pwapro_sw_files_to_cache', 'pwapro_offline_page_images' );
