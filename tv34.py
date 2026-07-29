import http.server
import urllib.request
import time

PORT = 5234
# FIX 1: Removed the filename from the end so chunks append correctly
BASE_CDN_URL = "https://cdn4.skygo.mn/live/disk1/Dreambox/HLSv3-FTA" 

class DelayedProxy(http.server.SimpleHTTPRequestHandler):
    def do_GET(self):
        # Handle the playlist file request directly
        if "index.m3u8" in self.path:
            # Point directly to the source manifest
            real_url = f"{BASE_CDN_URL}/Dreambox.m3u8"
        else:
            # Point to the individual .ts chunks
            real_url = BASE_CDN_URL + self.path

        if ".ts" in self.path:
            print(f"Loading chunk: {self.path} - Delaying 8 seconds...")
            time.sleep(8.0) # Keeps your 3 seconds extra delay (5s + 3s)
            
        try:
            req = urllib.request.Request(real_url, headers={'User-Agent': 'Mozilla/5.0'})
            with urllib.request.urlopen(req) as response:
                self.send_response(200)
                for key, val in response.headers.items():
                    self.send_header(key, val)
                self.end_headers()
                self.wfile.write(response.read())
        except Exception as e:
            print(f"Error fetching: {real_url} -> {e}")
            self.send_error(500, f"Error fetching chunk: {e}")

print(f"Proxy server running. Use http://127.0.0.1:{PORT}/index.m3u8 in your player.")
# FIX 2: Switched 'tvmon.jk' to '192.168.1.41' so it accepts local computer connections
http.server.HTTPServer(('192.168.1.41', 5234), DelayedProxy).serve_forever()
