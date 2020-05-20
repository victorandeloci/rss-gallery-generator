<?php

  /*
  	Runs from a directory containing files to provide an
  	RSS 2.0 feed that contains the list and modification times for all the
  	files.
  */

  $feedName = 'My Audio Feed';
  $feedDesc = 'Feed for the my audio files in some server folder';
  $feedURL = 'http://www.mysite.com/audio';
  $feedBaseURL = 'http://www.mysite.com/audio/'; // must end in trailing forward slash (/).
  $explicit = 'NO'; //explicit content?
  $coverImg = ''; //cover image src
  $copyright = '';
  $language = ''; //pt-br, en-us

  //allowed extensions for audio cover image search
  $imgExts = ['jpg', 'jpeg', 'png'];

  $allowedExts = [
    'mp3',
    'MP3'
  ];

  $categories = [
    'Category',
    'Category',
    'Category',
    'Category',
    'Category'
  ];

  $keywords = [
    'keyword',
    'keyword',
    'keyword',
    'keyword',
    'keyword'
  ];

  /* ----------- BEGIN RSS RENDERING ----------- */

  require_once('getID3/getid3/getid3.php');
  $getID3 = new getID3;

  header('Content-type: text/xml');

  $rss .= '<?xml version="1.0"?>';
  $rss .= '<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:media="http://search.yahoo.com/mrss/" version="2.0">';
  $rss .=   '<channel>';
  $rss .=     '<title>' . $feedName . '</title>';
  $rss .=     '<name>' . $feedName . '</name>';
  $rss .=     '<itunes:name>' . $feedName . '</itunes:name>';
  $rss .=     '<link>' . $feedURL . '</link>';
  $rss .=     '<description>' . $feedDesc . '</description>';
  $rss .=     '<atom:link href="' . $feedURL . '" rel="self" type="application/rss+xml" />';
  $rss .=     '<itunes:explicit>' . $explicit . '</itunes:explicit>';
  $rss .=     '<itunes:image href="' . $coverImg . '"/>';
  $rss .=     '<webMaster>' . $feedURL . '</webMaster>';
  $rss .=     '<image>';
  $rss .=       '<title>' . $feedName . '</title>';
  $rss .=       '<link>' . $feedURL . '</link>';
  $rss .=       '<url>' . $coverImg . '</url>';
  $rss .=     '</image>';

  foreach ($categories as $cat)
    $rss .= '<itunes:category text="' . $cat . '"/>';

  $rss .= '<itunes:keywords>';
  foreach ($keywords as $keyword)
    $rss .= $keyword . ',';
  $rss .= '</itunes:keywords>';

  define( 'ROOT_DIR', dirname(__FILE__) );

  $content = scandir(ROOT_DIR);

  if($content){
    foreach ($content as $file) {

      //reset item cover image
      $fileCoverImg = '';
      $fileCoverImgSrc = '';

      //validate extension
      $ext = pathinfo($file)['extension'];
      if(in_array($ext, $allowedExts)){

        $fileInfo = $getID3->analyze('./' . $file);

        $filename = $fileInfo['filename'];
        $format = $fileInfo['fileformat'];
        $playtime = $fileInfo['playtime_string'];
        $filesize = $fileInfo['filesize'];

        //search for a item image, based on audio file name
        foreach ($imgExts as $imgExt) {
          if(file_exists('./' . substr_replace($filename , $imgExt, strrpos($filename , '.') + 1))){
            $fileCoverImg = substr_replace($filename , $imgExt, strrpos($filename , '.') + 1);
            break;
          }
        }

        if($fileCoverImg)
          $fileCoverImgSrc = '//' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . $fileCoverImg;

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

        $fileSrc = '//' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . $filename;

        $rss .= '<item>';
        $rss .=   '<title>' . $title . '</title>';
        $rss .=   '<link>' . $feedURL . '</link>';
        $rss .=   '<enclosure url="' . $fileSrc . '" type="audio/mpeg" length="' . $filesize . '"></enclosure>';
        $rss .=   '<author>' . $artist . '</author>';
        $rss .=   '<itunes:author>' . $artist . '</itunes:author>';
        $rss .=   '<album>' . $album . '</album>';
        $rss .=   '<itunes:duration>' . $playtime . '</itunes:duration>';
        $rss .=   '<guid>' . $feedURL . '</guid>';
        if($fileCoverImgSrc){
          $rss .=   '<description><![CDATA[<img alt="' . $title . ' - Cover Image" src="' . $fileCoverImgSrc . '" >]]></description>';
          $rss .=   '<itunes:summary><![CDATA[<img alt="' . $title . ' - Cover Image" src="' . $fileCoverImgSrc . '" >]]></itunes:summary>';
        }
        $rss .= '</item>';

      }
    }
  }

  $rss .=	  '</channel>';
  $rss .= '</rss>';

  echo $rss;
