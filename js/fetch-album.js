var response=null;
var index=0;
function fetchAlbum(i){
    $.ajax({
        url: 'php/fetch-album.php',
        type: 'POST',
        dataType: 'json',
        data: {"fetch-album": '{"id":"'+i.id+'"}'},
        success: function (result) {
            response=result;
            if(result.photo_count>0){
                drawSlider();
            }
            else{
                alert("There is nothing in "+result.name);
            }
        },
        error: function (xhr, status, error) {
            alert(xhr+ '\nstatus:' + status + '\nerror:' + error + '\n');
        }
    });
}

function drawSlider(){
    var display_album_container = $("<div/>", {"class": "display-album-container"});
    var close = $("<div/>", {"class": "close"}).appendTo(display_album_container);
    var close_a = $("<a/>", {"href":"javascript:void(0);","onclick":"closeSlider();"}).appendTo(close);
    var close_a_span = $("<span/>", {"class": "glyphicon glyphicon-remove-sign fa-2x","style":"color:white;"}).appendTo(close_a);
    
    var album_pic = $("<div/>", {"class": "album-pic"}).appendTo(display_album_container);
    var album_pic_imgContainer = $("<div/>", {"class": "img-container"}).appendTo(album_pic);
    var album_pic_img = $("<img/>", {"src": ""}).appendTo(album_pic_imgContainer);
    var album_pic_navigation = $("<div/>", {"class": "album-pic-navigation",}).appendTo(display_album_container);
    var album_pic_navigation_a_left = $("<a/>", {"href":"javascript:void(0);","class":"left carousel-control","onclick":"navigateSlider(-1)"}).appendTo(album_pic_navigation);
    var album_pic_navigation_a_left_span = $("<span/>", {"class": "glyphicon glyphicon-chevron-left"}).appendTo(album_pic_navigation_a_left);
    var pic_num = $("<span/>",{"class":"count"}).appendTo(album_pic_navigation);
    var album_pic_navigation_a_right = $("<a/>", {"href":"javascript:void(0);","class":"right carousel-control","onclick":"navigateSlider(+1)"}).appendTo(album_pic_navigation);
    var album_pic_navigation_a_right_span = $("<span/>", {"class": "glyphicon glyphicon-chevron-right"}).appendTo(album_pic_navigation_a_right);
    $('body').append(display_album_container).fadeIn();
    $('body').css({"overflow":"hidden"});
    navigateSlider(0);
}

function closeSlider(){
    $('.display-album-container').css({"display":"none"});
    $('body').css({"overflow":"auto"});
    $('.display-album-container').remove();
}

function navigateSlider(i){
    if(index+i>=response.photo_count){
           index=0;
    } else if(index+i<0) {
        index=response.photo_count-1;
    }
    else{
        index+=i;
    }
    $('.img-container img').attr('src',response.photos.data[index].source);
    $('.count').html((index+1)+'/'+response.photo_count);
}