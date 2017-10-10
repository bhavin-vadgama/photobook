function unload(){
    $.ajax({
        url: 'php/logout.php',
        type: 'POST',
        /*dataType: 'json',
        data: {"fetch-album": '{"id":"'+i.id+'"}'},*/
        success: function (result) {
            //response=result;
            alert('You need to login');
            /*if(result.photo_count>0){
                drawSlider();
            }
            else{
                alert("There is nothing in "+result.name);
            }*/
        },
        error: function (xhr, status, error) {
            alert(xhr+ '\nstatus:' + status + '\nerror:' + error + '\n');
        }
    });
}