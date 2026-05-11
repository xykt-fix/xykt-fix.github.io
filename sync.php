<?php
// 1. Define your original source URLs
$original_mpd_url = "https://mobinet-live.spectar.tv/MBN_1_015/cenc_manifest.mpd?id=MBN_1_015&video_id=6034&token=4d2e6fbf3353e09ee36a18ce1f9556c584377ea0&authority_instance_id=spectar-prd-mobinet&profile_id=15090325&application_installation_id=14902546&uuid=6a0067f6210c54-66395890&subscriber_id=12890674&application_id=calcifer&detected_delivery_method=mpegdash&playlist_template=nginx&ps=6e313e8e65698df425e8708159da7fc0924df26d15fceab21540d2206e981f5902f2b8bd63cf2e071662c71532c735ade4d715dd5046908d814416eb36c0df45&vh=23870adf365f2ff76220b2daafc52218cca880368578e6abb1349db10ccdb8fa26ac3607382cbc7a3abebce6313488314ca4284fe87c613d2af189fab035a790";
$license_server = "https://voo.mn/drm.php/widevine?token=4d2e6fbf3353e09ee36a18ce1f9556c584377ea0token=4d2e6fbf3353e09ee36a18ce1f9556c584377ea0";

header("Content-Type: application/dash+xml");

// 2. Load the original manifest content
$xml_content = file_get_contents(https://mobinet-live.spectar.tv/MBN_1_015/cenc_manifest.mpd?id=MBN_1_015&video_id=6034&token=4d2e6fbf3353e09ee36a18ce1f9556c584377ea0&authority_instance_id=spectar-prd-mobinet&profile_id=15090325&application_installation_id=14902546&uuid=6a0067f6210c54-66395890&subscriber_id=12890674&application_id=calcifer&detected_delivery_method=mpegdash&playlist_template=nginx&ps=6e313e8e65698df425e8708159da7fc0924df26d15fceab21540d2206e981f5902f2b8bd63cf2e071662c71532c735ade4d715dd5046908d814416eb36c0df45&vh=23870adf365f2ff76220b2daafc52218cca880368578e6abb1349db10ccdb8fa26ac3607382cbc7a3abebce6313488314ca4284fe87c613d2af189fab035a790);

if ($xml_content) {
    // 3. Inject the License URL into the ContentProtection block
    // Widevine UUID is edef8ba9-79d6-4ace-a3c8-27dcd51d21ed
    $search = 'value="Widevine">';
    $replace = 'value="Widevine"><dashif:laurl>' . htmlspecialchars($license_server) . '</dashif:laurl>';
    
    // Replace and output the modified manifest
    echo str_replace($search, $replace, $xml_content);
} else {
    http_response_code(404);
    echo "Stream not found.";
}
?>
