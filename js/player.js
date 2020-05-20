function initVolumeBar()
{
  var a = document.getElementById("player");
  var b = a.volume;
  var d = document.getElementById("vol-obj");
  d.value = b;
  d.addEventListener("click", c);

  function c(f)
  {
    var e = f.offsetX / this.offsetWidth;
    a.volume = e;
    d.value = e
  }
}

function initProgressBar()
{
  var f = document.getElementById("player");
  var b = f.duration;
  var c = f.currentTime;
  var d = calculateTotalValue(b - c);
  document.getElementById("end-time").innerHTML = d;
  var a = calculateCurrentValue(c);
  document.getElementById("start-time").innerHTML = a;
  var e = document.getElementById("seek-obj");
  e.value = f.currentTime / f.duration;
  e.addEventListener("click", g);
  if (f.currentTime == f.duration)
  {
    document.getElementById("play-btn-icon").classlist.toggle("fa-play");
    document.getElementById("play-btn-icon").classlist.toggle("fa-pause")
  }

  function g(i)
  {
    var h = i.offsetX / this.offsetWidth;
    f.currentTime = h * f.duration;
    e.value = h / 100
  }
}

function initPlayers(a)
{
  for (var b = 0; b < a; b++)
  {
    (function ()
    {
      var d = document.getElementById("player-container"),
        c = document.getElementById("player"),
        f = false,
        h = document.getElementById("play-btn");
      var g = document.getElementById("plus-btn"),
        j = document.getElementById("less-btn");
      var i = document.getElementById("volume-btn");
      var e = c.volume;
      if (h != null)
      {
        h.addEventListener("click", function ()
        {
          togglePlay()
        })
      }
      if (g != null)
      {
        g.addEventListener("click", function ()
        {
          c.currentTime += 15
        })
      }
      if (j != null)
      {
        j.addEventListener("click", function ()
        {
          c.currentTime -= 15
        })
      }
      if (i != null)
      {
        i.addEventListener("click", function ()
        {
          if (c.volume != 0)
          {
            e = c.volume;
            c.volume = 0;
            i.classList.toggle("fa-volume-up");
            i.classList.toggle("fa-volume-mute")
          }
          else
          {
            c.volume = e;
            i.classList.toggle("fa-volume-up");
            i.classList.toggle("fa-volume-mute")
          }
        })
      }
    }())
  }
}

function togglePlay()
{
  if (player.paused === false)
  {
    player.pause();
    isPlaying = false;
    if (document.getElementById("play-btn-icon").classList.contains("fa-pause"))
    {
      document.getElementById("play-btn-icon").classList.remove("fa-pause");
      document.getElementById("play-btn-icon").classList.add("fa-play")
    }
  }
  else
  {
    player.play();
    document.getElementById("play-btn-icon").classList.remove("fa-play");
    document.getElementById("play-btn-icon").classList.add("fa-pause");
    isPlaying = true
  }
  var a = document.getElementById("audio-player");
  if (!a.classList.contains("player-active"))
  {
    a.classList.add("player-active");
  }
}

function calculateTotalValue(a)
{
  var f = Math.floor(a / 60);
  var c = a - f * 60;
  var e = "";
  if (c < 10)
  {
    e = "0" + c.toString()
  }
  else
  {
    e = c.toString()
  }
  var d = e.substr(0, 2);
  var b = f + ":" + d;
  return b
}

function calculateCurrentValue(b)
{
  var e = parseInt(b / 3600) % 24,
    a = parseInt(b / 60) % 60,
    d = b % 60,
    f = d.toFixed(),
    c = (a < 10 ? "0" + a : a) + ":" + (f < 10 ? "0" + f : f);
  return c
}

$(document).ready(function(){
  $(document).on("click", ".playItem", function(){
    var player = document.getElementById("player");
    var playerSource = document.getElementById('playerSource');
    var playerDownload = document.getElementById('playerPodDownload');

    //carrega no source e audio
    playerSource.src = $(this).attr("data-src");
    playerDownload.href = $(this).attr("data-src");
    player.load();

    togglePlay();
  });

  $("body").keyup(function (k){
    if (k.keyCode == 32)
    {
      togglePlay()
    }
    else
    {
      if (k.keyCode == 39)
      {
        c.currentTime += 15
      }
      else
      {
        if (k.keyCode == 37)
        {
          c.currentTime -= 15
        }
      }
    }
  });

  initPlayers($("#player-container").length);

});
