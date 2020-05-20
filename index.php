<?php
  $feed = json_decode(file_get_contents('feed/feed_content.json'));
 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?=$feed->channel->title?></title>

    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/master.css">

    <script
			  src="https://code.jquery.com/jquery-3.5.1.min.js"
			  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
			  crossorigin="anonymous"></script>

    <script src="js/player.js" charset="utf-8"></script>

  </head>
  <body>

    <div class="container">
      <div class="column-container">
        <div class="column first">
          <img src="<?=$feed->channel->image->url?>" alt="Feed Cover">
        </div>
        <div class="column">
          <h1><?=$feed->channel->title?></h1>
          <p><?=$feed->channel->description?></p>
          <p><?=$feed->channel->copyright?></p>
          <a href="<?php echo '//' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . 'feed/'; ?>"><i class="fas fa-rss"></i></a>
        </div>
      </div>
    </div>

    <section class="gallery">
      <?php

        foreach ($feed->channel->item as $item) {

          foreach ($item->enclosure as $enclosureItem)
            $itemEnclosure = $enclosureItem;

          ?>
            <div class="item" style="background-image: url('<?php if($item->image->url) echo $item->image->url; else echo 'img/play_cover.png';?>');">
              <div class="buttons">
                <i data-src="<?=$itemEnclosure->url?>" class="fas fa-play playItem"></i>
                <a href="<?=$itemEnclosure->url?>" target="_blank" rel="noreferrer noopener" download><i class="fas fa-download downloadItem"></i></a>
              </div>
              <div class="overlay"></div>
              <div class="content">
                <h3><?=$item->title?></h3>
                <p><span><?=$item->album?></span> - <?=$item->track?> | <?=$item->year?></p>
              </div>
            </div>
          <?php
        }

       ?>
    </section>

    <?php require_once('parts/player.html'); ?>

  </body>
</html>
