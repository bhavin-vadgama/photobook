function downloadAlbum(i){
    var albumId = new Array(i.id);
    
    $.ajax({
        url: 'php/download-controller.php',
        type: 'POST',
        data: {"get-zip": albumId},
        success: function (result) {
            if(result.response=="OK"){
                if(result.zip=="0"){
                    alert('There is no photo in the album!!');
                }
                else{
                    $('#zip').css({"display":""}); 
                    alert('Album download successful. Download the album from top right corner link.');
                }
            }
        },
        error: function (xhr, status, error) {
            alert(xhr + '\nstatus:' + status + '\nerror:' + error + '\n' + 'page: at dd');
        }
    });
}

function downloadSelectedAlbum(){
    var checkedSize = $('input[name="album_checklist"]:checked').length;//:checked
    if(checkedSize>0){
       //$checked = $('input[name="album_checklist"]');
        var checked = document.getElementsByName('album_checklist');
        var albumIds = [];
        for(var i=0;i<checked.length;i++){
            if(checked[i].checked)
                albumIds.push(checked[i].id);
        }
        
        $.ajax({
            url: 'php/download-controller.php',
            type: 'POST',
            data: {"get-zip": albumIds},
            success: function (result) {
                if(result.response=="OK"){
                    if(result.zip=="0"){
                        alert('There is no photo in the album!!');
                    }
                    else{
                        $('#zip').css({"display":""});  
                        alert('Selected album download successful. Download the album from top right corner link.');
                    }
                }
            },
            error: function (xhr, status, error) {
                alert(xhr + '\nstatus:' + status + '\nerror:' + error + '\n' + 'page: at dds');
            }
        });
    } else {
        var checked = document.getElementsByName('album_checklist');
        if(checked.length>0){
           alert('Please select the album to download...');
        } else {
            alert("You don't have any album(s).!!");
        }
    }
}

function downloadAllAlbum(){
    var checked = document.getElementsByName('album_checklist');
    if(checked.length>0){
        var albumIds = [];
        for(var i=0;i<checked.length;i++){
                albumIds.push(checked[i].id);
        }
        
        $.ajax({
            url: 'php/download-controller.php',
            type: 'POST',
            data: {"get-zip": albumIds},
            success: function (result) {
                if(result.response=="OK"){
                    if(result.zip=="0"){
                        alert('There is no photo in the album!!');
                    }
                    else{
                        $('#zip').css({"display":""});    
                        alert('All the album download successful. Download the album from top right corner link.');
                    }
                }
            },
            error: function (xhr, status, error) {
                alert(xhr + '\nstatus:' + status + '\nerror:' + error + '\n' + 'page: at ddall');
            }
        });    
    }
    else{
        alert("You don't have any album(s).!!");
    }
}
