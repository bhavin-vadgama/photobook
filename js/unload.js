function unload(){
    $.ajax({
        url: 'php/logout.php',
        type: 'POST',
        success: function (result) {
            alert('You need to login');
        },
        error: function (xhr, status, error) {
            alert(xhr+ '\nstatus:' + status + '\nerror:' + error + '\n');
        }
    });
}