function moveAlbum(i){
    var albumId = new Array(i.id);
    $.ajax({
        url: 'php/gdrive-controller.php',
        type: 'POST',
        //dataType: 'json',
        //data: {"fetch-album": '{"id":"12"}'},
        success: function (result) {
            if(result.response=="LINK"){
                window.location.replace(result.url);
            } else if(result.response=="OK"){
                //do something
                $.ajax({
                    url: 'php/move-controller.php',
                    type: 'POST',
                    data: {"move": albumId},
                    success: function (result) {
                        alert('Album moved successfully!!!');
                    },
                    error: function (xhr, status, error) {
                        alert(xhr + '\nstatus:' + status + '\nerror:' + error + '\n');
                    }
                });
            }
            else{
                alert('There is some error in authentication.');
            }
        },
        error: function (xhr, status, error) {
            alert(xhr+ '\nstatus:' + status + '\nerror:' + error + '\n');
        }
    });
}

function moveSeleceted(){
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
        url: 'php/gdrive-controller.php',
        type: 'POST',
        //dataType: 'json',
        //data: {"fetch-album": '{"id":"12"}'},
        success: function (result) {
                if(result.response=="LINK"){
                    window.location.replace(result.url);
                } else if(result.response=="OK"){
                    //do something
                    $.ajax({
                        url: 'php/move-controller.php',
                        type: 'POST',
                        data: {"move": albumIds},
                        success: function (result) {
                            alert('Selected album moved successfully!!!');
                        },
                        error: function (xhr, status, error) {
                            alert(xhr + '\nstatus:' + status + '\nerror:' + error + '\n');
                        }
                    });
                }
                else{
                    alert('There is some error in authentication.');
                }
            },
        error: function (xhr, status, error) {
            alert(xhr+ '\nstatus:' + status + '\nerror:' + error + '\n');
        }
        });
        
    } else {
        var checked = document.getElementsByName('album_checklist');
        if(checked.length>0){
           alert('Please select the album to move...');
        } else {
            alert("You don't have any album(s).!!");
        }
    }
}

function moveAll(){
    var checked = document.getElementsByName('album_checklist');
    if(checked.length>0){
        var albumIds = [];
        for(var i=0;i<checked.length;i++){
                albumIds.push(checked[i].id);
        }
        $.ajax({
        url: 'php/gdrive-controller.php',
        type: 'POST',
        //dataType: 'json',
        //data: {"fetch-album": '{"id":"12"}'},
        success: function (result) {
                if(result.response=="LINK"){
                    window.location.replace(result.url);
                } else if(result.response=="OK"){
                    //do something
                    $.ajax({
                        url: 'php/move-controller.php',
                        type: 'POST',
                        data: {"move": albumIds},
                        success: function (result) {
                            alert('All the album moved successfully!!!');

                        },
                        error: function (xhr, status, error) {
                            alert(xhr + '\nstatus:' + status + '\nerror:' + error + '\n');
                        }
                    });    
                }
                else{
                    alert('There is some error in authentication.');
                }
            },
        error: function (xhr, status, error) {
            alert(xhr+ '\nstatus:' + status + '\nerror:' + error + '\n');
        }
        });
    }
    else{
        alert("You don't have any album(s).!!");
    }
}