<div class="di"
    style="height:540px; border:#999 1px solid; width:53.2%; margin:2px 0px 0px 0px; float:left; position:relative; left:20px;">
    <marquee scrolldelay="120" direction="left" style="position:absolute; width:100%; height:40px;">
        <?php
        $ads=$Ad->all(['sh'=>1]);
        foreach($ads as $ad){
            echo $ad['text']."&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        ?>
    </marquee>
    <div style="height:32px; display:block;"></div>
    <!--正中央-->
    <div style="width:100%; padding:2px; height:290px;">
        <div id="mwww" loop="true" style="width:100%; height:100%;">
            <div style="width:99%; height:100%; position:relative;" class="cent">沒有資料</div>
        </div>
    </div>
    <script>
    var lin = new Array();
    <?php
        $mvims=$Mvim->all(['sh'=>1]);
        foreach($mvims as $mv){
            echo "lin.push('pic/{$mv['img']}');\n";
        }
    ?>

    var now = 0;                    // 設定一個計數器，從第 0 張開始
    ww()                            // 網頁一打開，先執行一次顯示第一張

    if (lin.length > 1) {           // 如果照片超過 1 張
        setInterval("ww()", 3000);  // 每隔 3000 毫秒 (3秒) 就執行一次 ww()
        now = 1;                    // 準備顯示下一張
    }

    function ww() {
// 這一行最關鍵：它把 id 為 mwww 的區塊內容換成新的 <embed> 標籤（圖片路徑從 lin 陣列拿）
        $("#mwww").html("<embed loop=true src='" + lin[now] + "' style='width:99%; height:100%;'></embed>")

        now++;                      // 數字加 1，下次換下一張
        if (now >= lin.length)      // 如果播到最後一張了
            now = 0;                // 就回到第 0 張重新開始
    }
    </script>
    
    <div
        style="width:95%; padding:2px; height:190px; margin-top:10px; padding:5px 10px 5px 10px; border:#0C3 dashed 3px; position:relative;">
        <span class="t botli">最新消息區
            <?php
            if($News->count(['sh'=>1])>5);
            echo "<a href='?do=news' style='float:right'> More...</a>"
            ?>
        </span>
        <?php
        $news=$News->all(['sh'=>1],"limit 5");
        ?>
        <ul class="ssaa" style="list-style-type:decimal;">
            <?php
            foreach($news as $n){
                echo "<li>";
                echo mb_substr($n['text'],0,20);
                echo "<div class='all' style='display:none'>";
                echo $n['text'];
                echo "</div>";
                echo "</li>";    
            }
            ?>
        </ul>
        <div id="altt"
            style="position: absolute; width: 350px; min-height: 100px; background-color: rgb(255, 255, 204); top: 50px; left: 130px; z-index: 99; display: none; padding: 5px; border: 3px double rgb(255, 153, 0); background-position: initial initial; background-repeat: initial initial;">
        </div>
        <script>
        $(".ssaa li").hover(
            function() {
                $("#altt").html("<pre>" + $(this).children(".all").html() + "</pre>")
                $("#altt").show()
            }
        )
        $(".ssaa li").mouseout(
            function() {
                $("#altt").hide()
            }
        )
        </script>
    </div>
</div>