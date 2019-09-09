
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ClipYeu18++</title>
    <link rel="stylesheet" href="./Swiper-3.4.2/dist/css/swiper.min.css">
    <link rel="stylesheet" href="./css/video.css">
    <script src="./js/jquery.min.js"></script>
    <script src="./Swiper-3.4.2/dist/js/swiper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script src="https://ssl.p.jwpcdn.com/player/v/8.1.3/jwplayer.js" type="text/javascript"></script>
    <script type="text/javascript">
        jwplayer.key = "W7zSm81+mmIsg7F+fyHRKhF3ggLkTqtGMhvI92kbqf/ysE99";
    </script>
    <style>
        .bg {
            position: absolute;
            z-index: 9999999;
            top: 0;
            left: 0;
            background: #0000008a;
            width: 100%;
            height: 100vh;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .bg img {
            width: 50%;
        }
    </style>
</head>

<body style="position:relative">
    <div class="bg">
        <img src="./img/loading.gif" alt="">
    </div>
    <div class="swiper-container" id="swiper2">
        <div class="swiper-wrapper">
            <img src="https://clipyeu18.com/img/bg_2.jpg" width="100vw" height="100vh" style="position:absolute; top:0; left:0;width:100vw; height: 100vh;" alt="bg">
        </div>
    </div>

<script>
    var video = {
        videoData: [],
        index: 0,
        perpage:15,
        isPlay: true,
        loadVideo: function(mySwiper,callback) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var data = JSON.parse(this.responseText);
                    if(data.msg === 'success') {
                        video.videoData = data.data.list;
                        // console.log(data.data.list)
                        callback(mySwiper,data.data.list);
                    }
                }       
            };
            xmlhttp.open("GET", "https://clipyeu2.com/data.php", true);
            xmlhttp.send();
        },
        initVideoSlider: function(mySwiper,data) {

            let start = video.index * video.perpage;
            let end = (video.index + 1) *  video.perpage;
            let list = data.slice(start,end);
            var html = '';
            for(let i = 0; i < list.length; i++) {
                if(list[i].videoUrl != "") {
                    let url = `https://app.clipyeuplus.com${list[i].videoUrl}`;
                    let img = list[i].videoUrlPng;
                    if(img == '') {
                        img = list[i].videoUrlGif;
                    }
                    let poster = `https://app.clipyeuplus.com${img}`;
                    
                        html += `<div class="swiper-slide">
                            <div class="wrap" class="video_play" data-link="${url}" data-poster="${poster}" data-time="0">
                                <div id="video_${list[i].id}" ></div>
                            </div>
                            
                        </div>`;
                }   
            }
            mySwiper.appendSlide(html);
            mySwiper.update();
            video.playVideo();
            document.querySelector('.bg').style.display='none';
        },
        
        run: function() {
            var mySwiper =  new Swiper('#swiper2', {
                direction: 'vertical',
                spaceBetween: 30,
                loop: false
            })
            if(video.videoData.length == 0) {
                document.querySelector('.bg').style.display='flex';
                video.loadVideo(mySwiper,video.initVideoSlider);
            }
            mySwiper.on('transitionEnd',function(swiper) {
                video.stopVideo();
                video.playVideo();
                
            });
            
            mySwiper.on('touchEnd',function(swiper) {
                if(swiper.activeIndex == swiper.slides.length -2 && swiper.touches.diff < 0) {
                    video.index++;
                    document.querySelector('.bg').style.display= 'flex';
                    setTimeout(function(){
                        video.initVideoSlider(swiper, video.videoData);
                    },500);
                }
                
            })
            mySwiper.on('click', function() {
                var mv = document.querySelector('.swiper-slide.swiper-slide-active .video_play');
                if(video.isPlay == true) {
                    mv.pause();
                    video.isPlay = false;
                } else {
                    mv.play();
                    video.isPlay = true;
                }
            })
        },
        playVideo() {
            var wrap = document.querySelector('.swiper-slide.swiper-slide-active .wrap');
            var link = wrap.getAttribute('data-link');
            var id = wrap.querySelector('div').id;
            var poster = wrap.getAttribute('data-poster');
            var time = wrap.getAttribute('data-time');
            var player = jwplayer(id);
            player.setup({
                sources: [{
                    file: link,
                    type: "mp4"
                }],
                image:poster,
                width: "100%",
                aspectratio: "3:8",
                primary: "html5",
                autostart: true,
                muted: false,
                volume: 100
            });
            
        },
        stopVideo() {
            var prev = document.querySelector('.swiper-slide.swiper-slide-prev .wrap');
            var next = document.querySelector('.swiper-slide swiper-slide-next .wrap');
            if(prev != undefined) {
                var movie  = prev.querySelector('video');
                
                if(movie != undefined) {
                    var playtime = movie.currentTime;
                    prev.setAttribute('data-time',playtime);
                    var id = prev.querySelector('div').id;
                    var player = jwplayer(id);
                    player.remove();
                }
            }

            if(next != undefined) {
                var movie  = next.querySelector('video');
                if(movie != undefined) {
                    var playtime = movie.currentTime;
                    next.setAttribute('data-time',playtime);
                    var id = next.querySelector('div').id;
                    var player = jwplayer(id);
                    player.remove();
                }
                
            }
        }
    }
   video.run();
</script>
</body>
</html>