<?php
// Unified Stream & DRM Proxy
$manifestUrl = "https://mobinet-live.spectar.tv/MBN_1_015/cenc_manifest.mpd?id=MBN_1_015&video_id=6034&token=4d2e6fbf3353e09ee36a18ce1f9556c584377ea0&authority_instance_id=spectar-prd-mobinet&profile_id=15090325&application_installation_id=14902546&uuid=6a0067f6210c54-66395890&subscriber_id=12890674&application_id=calcifer&detected_delivery_method=mpegdash&playlist_template=nginx&ps=6e313e8e65698df425e8708159da7fc0924df26d15fceab21540d2206e981f5902f2b8bd63cf2e071662c71532c735ade4d715dd5046908d814416eb36c0df45&vh=23870adf365f2ff76220b2daafc52218cca880368578e6abb1349db10ccdb8fa26ac3607382cbc7a3abebce6313488314ca4284fe87c613d2af189fab035a790";
$licenseProxy = "https://voo.mn/drm.php/widevine?token=4d2e6fbf3353e09ee36a18ce1f9556c584377ea0";

// 1. MANIFEST HANDLER: Serve the MPD with embedded license URL
if (strpos($_SERVER['REQUEST_URI'], '.mpd') !== false) {
    header('Content-Type: application/dash+xml');
    
    // Fetch the original manifest
    $mpdContent = file_get_contents($manifestUrl);
    
    // Inject the dashif:laurl into the Widevine ContentProtection block
    // Widevine SystemID: edef8ba9-79d6-4ace-a3c8-27dcd51d21ed
    $search = 'value="Widevine">';
    $replace = 'value="Widevine"><dashif:laurl>' . htmlspecialchars($licenseProxy) . '</dashif:laurl>';
    
    echo str_replace($search, $replace, $mpdContent);
    exit;
}

// 2. LICENSE HANDLER: Forward binary requests to the actual DRM server
if (strpos($_SERVER['REQUEST_URI'], '/widevine') !== false) {
    $widevineRequest = file_get_contents('php://input'); // Get binary payload from player
    
    $ch = curl_init($licenseProxy);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $widevineRequest);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/octet-stream']); //
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    header('Content-Type: application/octet-stream');
    echo $response;
    exit;
}
?>
