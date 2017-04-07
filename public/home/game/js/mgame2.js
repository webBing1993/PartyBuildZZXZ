
var puzzleGame = function(){
    var square,sort=0;
    for(var i = 0; i < level; i++){
        for(var j = 0; j < level; j++){
            square = document.createElement("div");
            $(square).css({
                "width": boxW/level,
                "height": boxW/level,
                "background": "url("+ img_path +") no-repeat" ,
                "background-size":'300px 300px',
                "backgroundPosition": -j*boxW/level +"px "+(-i)*boxW/level + "px"

            });
            sort++;
            $(square).attr({
                "sort": sort//给各个小方格加上正确的顺序
            })
            $(".piece_container").append(square);
        }
    }
    init();
}
init = function(){
    var pageLeft = 0,
        pageTop = 0,
        getStartX = 0,
        getStartY = 0,
        floatLayer = "",
        getSort = "",
        getBkP="",
        nextCheck = "<button class='nextcheck'>下一关</button>",
        playAgain = "<button class='playagain'>再来一次</button>";
    //按下方格触发方法
    $("div[sort]").on("touchstart mousedown",function(e){
        e.preventDefault();
        if(floatLayer != ""){
            floatLayer.remove();
        }
        var getEvent = window.event || arguments.callee.caller.arguments[0];//获取触发事件的元素
        thisE = getEvent.target;//获取鼠标按下时的方格
        if(getEvent.changedTouches){//手机情况下
            if(getEvent.changedTouches.length == 1){
                var thisElement = getEvent.target;
                getSort = $(thisElement).attr("sort");//获取触摸时的元素的sort
                getBkP = $(thisElement).css("backgroundPosition");
                var getBkImg = $(thisElement).css("background");

                var getPoints = parseInt($(thisElement).parents(".piece_container").attr("points"));
                floatLayer = $(document.createElement("div")).attr({"sort": getSort}).css({
                    "background": getBkImg, "position": "fixed", "width": boxW/level,"height": boxW/level
                });//创建浮层并加上样式
                //取得手指相对于文档的边距
                getStartX = getEvent.changedTouches[0].pageX;
                getStartY =  getEvent.changedTouches[0].pageY;

                pageLeft = $(thisElement).offset().left;
                pageTop = $(thisElement).offset().top;

                floatLayer.css({top: pageTop+1, left: pageLeft+1});
                floatLayer.appendTo("body");
            }
        }else {//电脑情况下
            var thisElement = getEvent.target;
            getSort = $(thisElement).attr("sort");//获取点击时的元素的sort
            getBkP = $(thisElement).css("backgroundPosition");
            var getBkImg = $(thisElement).css("background");

            var getPoints = parseInt($(thisElement).parents(".piece_container").attr("points"));
            floatLayer = $(document.createElement("div")).attr({"sort": getSort}).css({
                "background": getBkImg, "position": "fixed", "width": boxW/level,"height": boxW/level
            });//创建浮层并加上样式

            //取得手指相对于文档的边距
            getStartX = getEvent.pageX;
            getStartY =  getEvent.pageY;

            pageLeft = $(thisElement).offset().left;
            pageTop = $(thisElement).offset().top;

            floatLayer.css({top: pageTop+1, left: pageLeft+1});
            floatLayer.appendTo("body");
        }
        return true;
    })

    //移动手指或鼠标触发事件
    $(document).on("touchmove mousemove",function(e){
        e.preventDefault();
        //获取触发touchmove的对象
        var getEvent = window.event || arguments.callee.caller.arguments[0];
        if(getEvent.changedTouches){
            if(getEvent.changedTouches.length == 1){//确保只有一个手指
                //获取手指的位置
                var getCurrentX = getEvent.changedTouches[0].pageX;
                var getCurrentY = getEvent.changedTouches[0].pageY;
            }
        }else{
            //获取鼠标的位置
            var getCurrentX = getEvent.pageX;
            var getCurrentY = getEvent.pageY;
        }
        if(floatLayer){
            floatLayer.css({top: getCurrentY - (getStartY - pageTop), left: getCurrentX - (getStartX - pageLeft)});//决定图层的样式
        }
    })

    //手指离开触发事件
    $(document).on("touchend mouseup",function(e){
        e.preventDefault();
        var getEvent = window.event || arguments.callee.caller.arguments[0];//获取触发事件的元素
        if(floatLayer){
            //取得浮层的位置
            var getLayerX = floatLayer.offset().left;
            var getLayerY = floatLayer.offset().top;

            var layerValX = parseInt(getLayerX);
            var layerValY = parseInt(getLayerY);

            var layerCenterX = layerValX + floatLayer.width()/2;
            var layerCenterY = layerValY + floatLayer.height()/2;

            floatLayer.remove();//删除浮层
            var piece = $("div[sort]");
            for(var i=0; i<piece.length; i++){
                //取得每一个小方格的位置
                var getPieceX = $(piece[i]).offset().left;
                var getPieceY = $(piece[i]).offset().top;

                var pieceValX = parseInt(getPieceX);
                var pieceValY = parseInt(getPieceY);

                var pieceEndX = pieceValX+$(thisE).width();
                var pieceEndY = pieceValY+$(thisE).height();
                
                if((pieceValX < layerCenterX && pieceValY < layerCenterY) && (pieceEndX > layerCenterX && pieceEndY > layerCenterY)){
                    var getEndSort = $(piece[i]).attr("sort");//手指或鼠标松开时所在的方格
                    var getEndBkP = $(piece[i]).css("backgroundPosition");
                    if(getEndSort != getSort){//确定把浮层移动到了另外一个方格
                        stepCount++;
                        $(thisE).attr("sort",getEndSort).css("backgroundPosition",getEndBkP);
                        $(piece[i]).attr("sort",getSort).css("backgroundPosition",getBkP);
                        $(".over-step").text(stepCount);
                    }
                }
               
            }
            pageLeft = 0;
            pageTop = 0;
            getStartX = 0;
            getStartY = 0;
            floatLayer = "";
            getSort = "";
            getBkP = "";

            for(var i=0; i < piece.length; i++){//如果排序正确，继续往下执行！否则返回
                var getSortVal = parseInt($(piece[i]).attr("sort"));
                var number = i+1;

                if(getSortVal == number){
                    continue;
                }else{
                    return;
                }
            }
            gameOver1();
            clearInterval(timer);
        }else{
            return;
        }
    })
}

//创建一个没有重复数字的随机数组，并打乱方格的顺序
function upsetPiece(){
    if(timeCount >= 0){
        timer = setInterval(setTime,1000);
    }
    var pieceArr = $(".piece_container div[sort]"),
        randomArr = [],//创建一个随机数数组
        bkPArr = [];//创建一个数组，用来存储每个方格背景的定位！
    //取得每个方格的背景元素的定位
    for(var i=0; i< pieceArr.length; i++){
        bkPArr.push($(pieceArr[i]).css("backgroundPosition"));
    }
    for(var i=0; i< pieceArr.length; i++){
        var random = Math.floor(Math.random()*(level*level+1));
        if(random != 0){
            if(randomArr.length == 0){
                randomArr.push(random);
            }else{
                var la = 0;
                for(var j=0; j < randomArr.length; j++){
                    if(randomArr[j] == random){
                        i--;
                        break;
                    }
                    la++;
                }
                if(la == randomArr.length){
                    randomArr.push(random);
                }
            }
        }else{
            i--;
            continue;
        }
    }
    //打乱方格的顺序
    for(var i=0; i<randomArr.length; i++){
        $(pieceArr[i]).css("backgroundPosition",bkPArr[parseInt(randomArr[i])-1]);
        $(pieceArr[i]).attr("sort",randomArr[i]);
    }
}

//倒计时方法
function setTime(){
    timeCount++;
    $(".top2>p").html(formatTime(timeCount));
}

// $(document).ready(function(){
//     // //开始游戏
//     // startGame();
//     // function startGame(){
//     //     puzzleGame();
//     //     clearInterval(timer);
//     // }
//     //恢复按钮
//     $(".rule-description button").on("touchstart click",function(){
//         clearInterval(timer);
//         upsetPiece();
//         timeCount = 0;
//         stepCount = 0;
//         $(".rule-description .over-time").text(timeCount);
//         $(".rule-description .over-step").text(stepCount);
//     })
// })