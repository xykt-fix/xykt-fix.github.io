
const fs = require('fs');
const path = require('path');

// CONFIGURATION
const DOMAIN = 'https://github.io'; // Replace with your exact .io domain URL
const MEDIA_DIR = './media';                 // The folder where your .ts files are pushed
const OUTPUT_FILE = './playlist.m3u8';
const PLAYLIST_SIZE = 5;                     // How many segments are queued in the live window at once

function generateLiveM3u8() {
    try {
        // 1. Read and sort your .ts files numerically or by creation time
        const files = fs.readdirSync(MEDIA_DIR)
            .filter(file => file.endsWith('.ts'))
            .sort((a, b) => {
                // Extracts numbers from filename (e.g., 'm3udevdreamtv-1.ts' -> 1)
                const numA = parseInt(a.match(/\d+/)) || 0;
                const numB = parseInt(b.match(/\d+/)) || 0;
                return numA - numB;
            });

        if (files.length === 0) {
            console.log("No .ts files found in media directory.");
            return;
        }

        // 2. Queue the last 'X' segments to represent a shifting live queue
        const activeSegments = files.slice(-PLAYLIST_SIZE);
        
        // Use a generic sequence counter that increments as files grow
        const mediaSequence = 35642455 + (files.length - activeSegments.length);

        let m3u8Content = `#EXTM3U\n`;
        m3u8Content += `#EXT-X-VERSION:5\n`;
        m3u8Content += `#EXT-X-TARGETDURATION:45\n`; // High target overhead to absorb 20-44s segments safely
        m3u8Content += `#EXT-X-MEDIA-SEQUENCE:${mediaSequence}\n`;
        m3u8Content += `#EXT-X-INDEPENDENT-SEGMENTS\n`;

        activeSegments.forEach((file) => {
            // NOTE: For varying durations, an exact calculation is preferred. 
            // We use a safe estimate here, or you can parse the file metadata.
            m3u8Content += `#EXTINF:44.0,\n`; 
            m3u8Content += `${DOMAIN}/media/${file}\n`;
        });

        fs.writeFileSync(OUTPUT_FILE, m3u8Content);
        console.log(`Successfully generated live queue inside ${OUTPUT_FILE}`);
    } catch (err) {
        console.error("Error creating queue: ", err);
    }
}

generateLiveM3u8();
