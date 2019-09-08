
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ClipYeu++</title>
    <link rel="stylesheet" href="./Swiper-3.4.2/dist/css/swiper.min.css">
    <link rel="stylesheet" href="./css/video.css">
    <script src="./js/jquery.min.js"></script>
    <script src="./Swiper-3.4.2/dist/js/swiper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
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
        perpage:5,
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
        initVideoSlider: function(mySwiper,data,diff) {

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
                            <video id="video_${list[i].id}" class="video_play" data-time="0" poster="${poster}" width="100%" data-link="${url}" webkit-playsinline="true" playsinline="true" preload="none"  muted controls x5-video-player-fullscreen="portraint" style="object-fit:fill" >
                                </video>
                            <div class="btn"><img class="player" src="./img/play.png" alt=""><img class="pause" src="./img/pause.png" alt=""></div>
                        </div>`;
                }   
            }

            if(diff === 'up') {
                mySwiper.removeAllSlides();
                mySwiper.appendSlide(html);
                let to = mySwiper.slides.length -1;
                mySwiper.slideTo(to,0,false);
                mySwiper.update();
                
            } else {
                mySwiper.removeAllSlides();
                mySwiper.appendSlide(html);
                mySwiper.init();
            }
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
                if(swiper.activeIndex == 0 && swiper.touches.diff > 0) {
                    video.index--;

                    if(video.index < 0) {
                        video.index = 0;
                        return;
                    }
                    document.querySelector('.bg').style.display= 'flex';
                    setTimeout(function(){
                        video.initVideoSlider(swiper, video.videoData,'up');
                    },3000);                  
                }
                if(swiper.activeIndex == swiper.slides.length -1 && swiper.touches.diff < 0) {
                    video.index++;
                    document.querySelector('.bg').style.display= 'flex';
                    setTimeout(function(){
                        video.initVideoSlider(swiper, video.videoData,'down');
                    },1000);
                }
                
            })
            mySwiper.on('click', function() {
                var mv = document.querySelector('.swiper-slide.swiper-slide-active .video_play');
                if(video.isPlay == true) {
                    mv.pause();
                    // document.querySelector('.swiper-slide.swiper-slide-active .btn .player').style.opacity = 1;
                    // document.querySelector('.swiper-slide.swiper-slide-active .btn .pause').style.opacity = 0;
                    video.isPlay = false;
                } else {
                    mv.play();
                    // document.querySelector('.swiper-slide.swiper-slide-active .btn .player').style.opacity = 0;
                    // document.querySelector('.swiper-slide.swiper-slide-active .btn .pause').style.opacity = 0;
                    video.isPlay = true;
                }
            })
        },
        playVideo() {
            var movie = document.querySelector('.swiper-slide.swiper-slide-active .video_play');
            var link = movie.getAttribute('data-link');
            if(movie.readyState == 4) {
                movie.play();
            } else {
                if(Hls.isSupported()) {
                    if(link.includes('m3u8')) {
                        var hls = new Hls();
                        hls.loadSource(link);
                        hls.attachMedia(movie);
                        hls.on(Hls.Events.MANIFEST_PARSED,function() {
                            movie.muted = false;
                            var time = movie.getAttribute('data-time');
                            movie.currentTime = time;
                            movie.play();
                        });
                    } else {
                        movie.src = link;
                        var source = document.createElement('source');
                        source.src = link;
                        source.type = 'video/mp4';
                        movie.appendChild(source);
                        movie.muted = false;
                        var time = movie.getAttribute('data-time');
                        movie.currentTime = time;
                        movie.play();
                    }   
                }else if(movie.canPlayType('application/vnd.apple.mpegurl')){
                    movie.src = link;
                    movie.addEventListener('loadedmetadata',function() {
                        movie.play();
                    });
                }
            }
        },
        stopVideo() {
            // var movie = document.querySelectorAll('.swiper-slide .video_play');
            // for(let i = 0; i < movie.length; i++) {
            //     movie[i].pause();
            //     movie[i].addEventListener('pause',function() {
            //         var tmp_src = movie[i].src;
            //         var playtime = movie[i].currentTime;
            //         movie[i].setAttribute('data-time',playtime);
            //         movie[i].src = '';
            //         movie[i].load();
            //         movie[i].src = tmp_src;
            //         movie[i].currentTime = playtime;
            //     });
            // }
            var prev = document.querySelector('.swiper-slide.swiper-slide-prev .video_play');
            var next = document.querySelector('.swiper-slide swiper-slide-next .video_play');
            if(prev != undefined) {
                if(prev.readyState == 4) {
                    prev.pause();
                    var tmp_src = prev.src;
                    var playtime = prev.currentTime;
                    prev.setAttribute('data-time',playtime);
                    prev.src='';
                    prev.load();
                }
            }

            if(next != undefined) {
                if(next.readyState == 4) {
                    next.pause();
                    var tmp_src = next.src;
                    var playtime = next.currentTime;
                    next.setAttribute('data-time',playtime);
                    next.src='';
                    next.load();
                }
            }
        }
    }
   video.run();
</script>
</body>

</html>