import http.server
import urllib.request
import time

PORT = 1782
# Replace this with the actual base URL of your SkyGo CDN stream
BASE_CDN_URL = "https://cdn4.skygo.mn/live/disk1/Dreambox/HLSv3-FTA/Dreambox.m3u8" 

class DelayedProxy(http.server.SimpleHTTPRequestHandler):
    def do_GET(self):
        # Check if the player is asking for a video chunk (.ts file)
        if ".ts" in self.path:
            print(f"Loading chunk: {self.path} - Intentionally slowing down...")
            # FORCE A DELAY: Sleep for 5 seconds before downloading
            time.sleep(5.0) 
            
        # Fetch the real file from the SkyGo CDN
        real_url = BASE_CDN_URL + self.path
        try:
            req = urllib.request.Request(real_url, headers={'User-Agent': 'Mozilla/5.0'})
            with urllib.request.urlopen(req) as response:
                self.send_response(200)
                # Copy headers
                for key, val in response.headers.items():
                    self.send_header(key, val)
                self.end_headers()
                # Stream the data to your video player
                self.wfile.write(response.read())
        except Exception as e:
            self.send_error(500, f"Error fetching chunk: {e}")

print(f"Proxy server running on http://localhost:{PORT}")
http.server.HTTPServer(('tvmon.jk', 1782), DelayedProxy).serve_forever()
