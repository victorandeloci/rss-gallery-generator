![Cover Image](https://i.ibb.co/mhBJRVk/rss-generator.png)

# rss-gallery-generator
Generate a RSS Feed based on a directory containing audio files and automatically save the XML and display the content found in a page/gallery

## Info
The RSS generator part was created based in this amazing [gist](https://gist.github.com/vsoch/4898025919365bf23b6f)

I got info from files based in [getID3](https://github.com/JamesHeinrich/getID3/). It's a submodule, located in feed/getID3

## Usage
Download the last [release .zip](https://github.com/victorandeloci/rss-gallery-generator/releases) and extract it in your server

Put your audio *(just mp3 for now)* in **feed/content/**
* You can put images with the same name as the .mp3 files. The script will recognize it and use as cover images for each item

Access **your_site.xyz/feed/** first, so it will generate the *.xml* rss feed
