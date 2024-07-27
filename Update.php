<?php

// Configuration
$whm_url = 'https://your-whm-server.com'; // Replace with your WHM server URL
$whm_username = 'your-whm-username'; // Replace with your WHM username
$whm_password = 'your-whm-password'; // Replace with your WHM password
$wordpress_installation_id = 'your-wordpress-installation-id'; // Replace with your WordPress installation ID

// Initialize curl
$curl = curl_init();

// Set curl options
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

// Login to WHM
curl_setopt($curl, CURLOPT_URL, $whm_url . '/login');
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, 'user=' . $whm_username . '&pass=' . $whm_password);
$response = curl_exec($curl);

// Get the security token
preg_match('/<input type="hidden" name="token" value="([^"]+)"/', $response, $matches);
$security_token = $matches[1];

// Update plugins
curl_setopt($curl, CURLOPT_URL, $whm_url . '/plugins/wp-toolkit/index.cgi?token=' . $security_token);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, 'action=update_plugins&installation_id=' . $wordpress_installation_id);
$response = curl_exec($curl);

// Write logs
$log_file = 'wp-toolkit-update-plugins.log';
$log_entry = date('Y-m-d H:i:s') . ' - Updated plugins for WordPress installation ' . $wordpress_installation_id . "\n";
file_put_contents($log_file, $log_entry, FILE_APPEND);

// Change auto-update setting and/or status for plugins
curl_setopt($curl, CURLOPT_URL, $whm_url . '/plugins/wp-toolkit/index.cgi?token=' . $security_token);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, 'action=change_auto_update_settings&installation_id=' . $wordpress_installation_id . '&auto_update=1');
$response = curl_exec($curl);

// Write logs
$log_entry = date('Y-m-d H:i:s') . ' - Changed auto-update setting and/or status for plugins for WordPress installation ' . $wordpress_installation_id . "\n";
file_put_contents($log_file, $log_entry, FILE_APPEND);

// Close curl
curl_close($curl);

// Display success message
echo 'Plugins updated and logs written successfully!';

?>
