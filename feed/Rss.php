<?php

  require_once('getID3/getid3/getid3.php');
  require_once('Writer.php');

  class Rss{

    private $feedName = 'My Audio Feed';
    private $feedDesc = 'Feed for the my audio files in some server folder';
    private $feedURL = 'http://www.mysite.com/audio';
    private $explicit = 'NO'; //explicit content?
    private $coverImg = '//localhost/kaiceph/img/play_cover.png'; //cover image src
    private $copyright = 'Copyright of feed content';
    private $language = 'en-us'; //pt-br, en-us

    //allowed extensions for audio cover image search
    private $imgExts = ['jpg', 'jpeg', 'png'];

    private $allowedExts = [
      'mp3',
      'MP3'
    ];

    private $categories = [
      'Category',
      'Category',
      'Category',
      'Category',
      'Category'
    ];

    private $keywords = [
      'keyword',
      'keyword',
      'keyword',
      'keyword',
      'keyword'
    ];

    private $getID3;
    private $writer;

    public function __construct(){
      $this->getID3 = new getID3;
    }

    public function generateFeed(){

      /* ----------- BEGIN RSS CREATION ----------- */

      $rss .= '<?xml version="1.0"?>';
      $rss .= '<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:media="http://search.yahoo.com/mrss/" version="2.0">';
      $rss .=   '<channel>';
      $rss .=     '<title>' . $this->feedName . '</title>';
      $rss .=     '<name>' . $this->feedName . '</name>';
      $rss .=     '<itunes:name>' . $this->feedName . '</itunes:name>';
      $rss .=     '<link>' . $this->feedURL . '</link>';
      $rss .=     '<description>' . $this->feedDesc . '</description>';
      $rss .=     '<atom:link href="' . $this->feedURL . '" rel="self" type="application/rss+xml" />';
      $rss .=     '<itunes:explicit>' . $this->explicit . '</itunes:explicit>';
      $rss .=     '<itunes:image href="' . $this->coverImg . '"/>';
      $rss .=     '<webMaster>' . $this->feedURL . '</webMaster>';
      $rss .=     '<image>';
      $rss .=       '<title>' . $this->feedName . '</title>';
      $rss .=       '<link>' . $this->feedURL . '</link>';
      $rss .=       '<url>' . $this->coverImg . '</url>';
      $rss .=     '</image>';
      $rss .=     '<copyright>' . $this->copyright . '</copyright>';
      $rss .=     '<language>' . $this->language . '</language>';

      foreach ($this->categories as $cat)
        $rss .= '<itunes:category text="' . $cat . '"/>';

      $rss .= '<itunes:keywords>';
      foreach ($this->keywords as $keyword)
        $rss .= $keyword . ', ';
      $rss .= '</itunes:keywords>';

      define( 'ROOT_DIR', dirname(__FILE__) );

      $content = scandir(ROOT_DIR . '/content/');

      if($content){
        foreach ($content as $file) {

          //reset item cover image
          $fileCoverImg = '';
          $fileCoverImgSrc = '';

          //validate extension
          $ext = pathinfo($file)['extension'];
          if(in_array($ext, $this->allowedExts)){

            $fileInfo = $this->getID3->analyze('./content/' . $file);

            $filename = $fileInfo['filename'];
            $format = $fileInfo['fileformat'];
            $playtime = $fileInfo['playtime_string'];
            $filesize = $fileInfo['filesize'];

            //search for a item image, based on audio file name
            foreach ($this->imgExts as $imgExt) {
              if(file_exists('./content/' . substr_replace($filename , $imgExt, strrpos($filename , '.') + 1))){
                $fileCoverImg = substr_replace($filename , $imgExt, strrpos($filename , '.') + 1);
                break;
              }
            }

            if($fileCoverImg)
              $fileCoverImgSrc = '//' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . 'content/' . $fileCoverImg;

            //check if id3v2 is available
            if($fileInfo['tags']['id3v2'])
              $audioTags = $fileInfo['tags']['id3v2'];
            else
              $audioTags = $fileInfo['tags']['id3v1'];

            $album = $audioTags['album'][0] ?? 'Album (?)';
            $artist = $audioTags['artist'][0] ?? 'Artist (?)';
            $genre = $audioTags['genre'][0] ?? 'Genre (?)';
            $title = $audioTags['title'][0] ?? 'Title (?)';
            $track_number = $audioTags['track_number'][0] ?? 'Track Number (?)';
            $year = $audioTags['year'][0] ?? 'Year (?)';

            $fileSrc = '//' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . 'content/' . $filename;

            $rss .= '<item>';
            $rss .=   '<title>' . $title . '</title>';
            $rss .=   '<link>' . $this->feedURL . '</link>';
            $rss .=   '<enclosure url="' . $fileSrc . '" type="audio/mpeg" length="' . $filesize . '"></enclosure>';
            $rss .=   '<author>' . $artist . '</author>';
            $rss .=   '<itunes:author>' . $artist . '</itunes:author>';
            $rss .=   '<album>' . $album . '</album>';
            $rss .=   '<track>' . $track_number . '</track>';
            $rss .=   '<year>' . $year . '</year>';
            $rss .=   '<itunes:duration>' . $playtime . '</itunes:duration>';
            $rss .=   '<guid>' . $this->feedURL . '</guid>';
            if($fileCoverImgSrc){
              $rss .=     '<image>';
              $rss .=       '<title>' . $title . '</title>';
              $rss .=       '<link>' . $this->feedURL . '</link>';
              $rss .=       '<url>' . $fileCoverImgSrc . '</url>';
              $rss .=     '</image>';
              $rss .=     '<itunes:image href="' . $fileCoverImgSrc . '"/>';
              $rss .=   '<description><![CDATA[<img alt="' . $title . ' - Cover Image" src="' . $fileCoverImgSrc . '" >]]></description>';
              $rss .=   '<itunes:summary><![CDATA[<img alt="' . $title . ' - Cover Image" src="' . $fileCoverImgSrc . '" >]]></itunes:summary>';
            }
            $rss .= '</item>';

          }
        }
      }

      $rss .=	  '</channel>';
      $rss .= '</rss>';

      /* ---------- JSON SAVE ---------- */

      $xml = simplexml_load_string($rss);
      $json = json_encode($xml);

      $this->writer = new Writer('feed_content', '.json');
      $this->writer->writeFile($json);

      /* ---------- XML SAVE ---------- */

      $this->writer = new Writer('rss_feed', '.xml');
      $this->writer->writeFile($rss);

      /* ---------- SHOW LOADED RSS ---------- */

      return $rss;

    }
  }
