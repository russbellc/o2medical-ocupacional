$(document).ready(function() {
    $("#webcam").scriptcam({
        showMicrophoneErrors: false,
        onError: onError,
        cornerRadius: 20,
        width: 320,
        height: 400,
        disableHardwareAcceleration: 1,
        cornerColor: 'e3e5e2',
        onWebcamReady: onWebcamReady
                //uploadImage:'upload.gif',
                //onPictureAsBase64:base64_tofield_and_image
    });
});
function base64_tofield() {
    $('#formfield').val($.scriptcam.getFrameAsBase64());
}
;
function base64_toimage() {
    $('#image').attr("src", "data:image/png;base64," + $.scriptcam.getFrameAsBase64());
}
;

/*function base64_tofield_and_image(b64) {
 $('#formfield').val(b64);
 $('#image').attr("src","data:image/png;base64,"+b64);
 };*/
function changeCamera() {
    $.scriptcam.changeCamera($('#cameraNames').val());
}
function onError(errorId, errorMsg) {
    $("#btn1").attr("disabled", true);
    $("#btn2").attr("disabled", true);
    alert(errorMsg);
}
function onWebcamReady(cameraNames, camera, microphoneNames, microphone, volume) {
    $.each(cameraNames, function(index, text) {
        $('#cameraNames').append($('<option></option>').val(index).html(text))
    });
    $('#cameraNames').val(camera);
}

			function uploadEx() {
				var dataURL = ("src","data:image/png;base64,"+$.scriptcam.getFrameAsBase64());
				console.log(dataURL);
                document.getElementById('hidden_data').value = dataURL;
                var fd = new FormData(document.forms["form1"]);
 
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'upload_data.php', true);
 
                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        var percentComplete = (e.loaded / e.total) * 100;
                        console.log(percentComplete + '% uploaded');
                        alert('Succesfully uploaded');
                    }
                };
 
                xhr.onload = function() {
 
                };
                xhr.send(fd);
            };