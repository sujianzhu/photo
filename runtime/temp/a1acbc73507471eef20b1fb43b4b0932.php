<?php /*a:1:{s:64:"F:\wamp\www\htdocs\photo\application\admin\view\index\login.html";i:1529333647;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="renderer" content="webkit" />
    <meta http-equiv="X-UA-COMPATIBLE" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>图片社交小程序</title>
    <meta name="keywords" content="图片社交小程序" />
    <meta name="description" content="图片社交小程序" />
    <meta name="author" content="图片社交小程序" />
    <link rel="shortcut  icon" type="image/x-icon" href="" media="screen"/>
    <script src="/static/js/jquery-1.8.1.min.js"></script>
    <script src="/static/bootstrap/js/bootstrap.js"></script>
    <link href="/static/css/login.css" rel="stylesheet" type="text/css" />
    <script src="/static/js/load_bottom.js" type="text/javascript"></script>
    <script src="/static/js/fun.js" type="text/javascript"></script>

</head>
<body>
<div class="admin-login-box">
    <div class="login-content-area">
        <div class="left-logo-area">
            <a href="" class="logo-img">
                <img src="/static/images/login-logo.png" alt="" />
            </a>
        </div>
        <div class="right-login-area">
            <form action="javascript:;">
                <div class="tip_info">
                    <div class="prompt_information" id="hint">
                        账号密码错误
                    </div>
                </div>
                <!-- 用户名 -->
                <div class="user-name-box">
                    <div class="username_bg" ></div>
                    <input type="text" placeholder="请输入账号" id="txtName"/>
                </div>
                <!-- 密码框 -->
                <div class="password-box">
                    <div class="password_bg" ></div>
                    <input type="password" placeholder="请输入密码" id="txtPWD"/>
                </div>
                <!-- 验证码 -->
                <input type="button" value="登录" class="sub_login" onclick="btnlogin();" />
            </form>
        </div>
    </div>
    <div class="txt" id="bottom_copyright">
        <p><span id="copyright_desc"></span>
            <br><a href="http://www.niushop.com.cn" target="_blank" style="text-decoration: none;color:#666;" id="copyright_companyname"></a>&nbsp;&nbsp;&nbsp;&nbsp;
            <label id="copyright_meta"></label>
        </p>
    </div>
</div>
<script>
    $(function(){
        ini_margin_top();
    })

    window.onresize = function(){
        ini_margin_top();
    }
    function ini_margin_top(){
        var admin_login_box_height = $(".admin-login-box").height();
        var login_content_area_height = $(".login-content-area").height();
        var margin_top = (admin_login_box_height - login_content_area_height)/2;
        $(".login-content-area").css("margin", margin_top+"px auto 0 auto");
        if(admin_login_box_height < 800){
            $(".txt").hide();
        }else{
            $(".txt").show();
        }
    }

    // enter 键登录
    document.onkeypress = function() {
        var iKeyCode = -1;
        if (arguments[0]) {
            iKeyCode = arguments[0].which;
        } else {
            iKeyCode = event.keyCode;
        }
        if (iKeyCode == 13) {
            // 登录
            $(".sub_login").click();
        }
    }
    //键盘tab
    $(document).keyup(function(e){
        var key =  e.which;
        if(key == 9){
            check_is_focus();
        }
    });

    $("body").click(function(){
        check_is_focus();
    })

    function check_is_focus(){
        if($("#txtName").is(":focus")){
            $("#txtName").parent("div").css("border-color","#0072D3");
        }else{
            $("#txtName").parent("div").css("border-color","#D9D9D8");
        }
        if($("#txtPWD").is(":focus")){
            $("#txtPWD").parent("div").css("border-color","#0072D3");
        }else{
            $("#txtPWD").parent("div").css("border-color","#D9D9D8");
        }
    }

    // 登陆 登录时 登录按钮"变暗"
    function btnlogin() {
        ClearCookie(); //登录时清除之前的cookie
        if ($("#txtName").val() == "") {
            $("#hint").css("display", "block");
            $("#hint").text("请输入账号");
            $("#txtName").focus();
            return false;
        } else if ($("#txtPWD").val() == "") {
            $("#hint").css("display", "block");
            $("#hint").text("请输入密码");
            $("#txtPWD").focus();
            return false;
        }
        var userName = $('#txtName').val();
        var password = $('#txtPWD').val();

        // 后台验证
        $.post("<?php echo url('index/login'); ?>", {
            "admin_name" : userName,
            "admin_pwd" : password,
        }, function(data) {
            if (data['code'] > 0) {
                $("#hint").css("display", "none");
                $(".sub_login").attr("disabled", "disabled");
                window.location.href = '<?php echo url("index/index"); ?>';
            } else {
                $("#hint").show();
                $("#hint").text(data['message']); //  用户名密码提示
            }
        });
    };

    function ClearCookie() {
        var expires = new Date();
        expires.setTime(expires.getTime() - 1000);
        document.cookie = "appCode='';path=/;expires=" + expires.toGMTString() + "";
        document.cookie = "roleID='';path=/;expires=" + expires.toGMTString() + "";
        document.cookie = "parentMenuID='';path=/;expires=" + expires.toGMTString() + "";
        document.cookie = "currentMenuName='';path=/;expires=" + expires.toGMTString() + "";
    }
</script>
</body>
</html>