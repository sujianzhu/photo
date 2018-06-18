//处理图片路径
function __IMG(img_path){
    var path = "";
    if(img_path != undefined && img_path != ""){
        if(img_path.indexOf("http://") == -1 && img_path.indexOf("https://") == -1){
            path = UPLOAD+"\/"+img_path;
        }else{
            path = img_path;
        }
    }
    return path;
}

