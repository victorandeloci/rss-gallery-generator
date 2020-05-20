<?php

  /*
  	Runs from a directory containing files to provide an
  	RSS 2.0 feed that contains the list and modification times for all the
  	files.
  */

  require_once('Rss.php');
  $rss = new Rss();

  function get_m_time_dir($path){
    $directory = new RecursiveDirectoryIterator(
      $path,
      FilesystemIterator::KEY_AS_PATHNAME |
      FilesystemIterator::CURRENT_AS_FILEINFO |
      FilesystemIterator::SKIP_DOTS
    );
    $iterator = new RecursiveIteratorIterator(
      $directory,
      RecursiveIteratorIterator::SELF_FIRST
    );
    $resultFile = $iterator->current();
    foreach($iterator as $file) {
      if ($file->getMtime() > $resultFile->getMtime()) {
        $resultFile = $file;
      }
    }
    return $resultFile->getMtime();
  }

  $lastFile = get_m_time_dir('./content/');

  header('Content-type: text/xml');

  if(file_exists('rss_feed.xml') && (filemtime('rss_feed.xml') >= $lastFile)){

    $xmlRss = file_get_contents('rss_feed.xml');
    echo $xmlRss;

  } else {

    echo $rss->generateFeed();

  }
